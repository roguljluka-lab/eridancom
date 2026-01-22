<?php

class TravelBoxClass {
    public function __construct() {
        add_action('add_meta_boxes', [$this, 'dc_add_travel_metabox']);
        add_action('save_post', [$this, 'dc_save_travel_metabox']);
        add_shortcode('dc_trips', [$this, 'render_dc_trips']);
    }

    // Shortcode callback function
    public function render_dc_trips()
    {

        $admin = new Admin();
        $post_id = get_the_ID();

        $dc_putovanje_id = get_post_meta($post_id, '_dc_putovanje_id', true);
        $putovanje = $admin->get_putovanje($dc_putovanje_id);
        $dc_settings = $admin->get_settings()->dc_postavke;

        if (!$putovanje) {
            return 'Neispravan ID putovanja.';
        }

        return include(DC_REZERVACIJE_PATH . 'views/web/rezervacija/ostala/book_now_widget.php');

    }

    // Add the Meta Box
    public function dc_add_travel_metabox() {

        $shortcode = new Shortcode();
        $post_type = $shortcode->dc_settings->post_type;

        add_meta_box(
            'nd_travel_settings',
            'DC rezervacije - ostala putovanja',
            [$this, 'dc_render_travel_metabox'],
            $post_type,
            'normal',
            'high'
        );
    }

    // Render the Meta Box HTML
    public function dc_render_travel_metabox($post) {

        $admin = new Admin();
        $putovanja = $admin->get_putovanja(1, 'ostala');

        $dc_putovanje_id = get_post_meta($post->ID, '_dc_putovanje_id', true);

        if(isset($dc_putovanje_id) && $dc_putovanje_id > 0) :
            $putovanje = $admin->get_putovanje($dc_putovanje_id);
            ?>

            <p>
                Putovanje: <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=uredi&id=' . $putovanje->id) ?>"><?php echo $putovanje->naziv; ?></a>
            </p>

        <?php else: ?>

            <p class="notice notice-error is-dismissible">
                Ovo putovanje još nije povezano sa DC rezervacije. Odaberite putovanje u padajućem izborniku.
            </p>

        <?php endif; ?>

        <select name="dc_putovanje_id" id="dc_putovanje_id" style="width: 100%;">
            <option value="0">Odaberite putovanje za povezivanje</option>
            <?php
            foreach($putovanja['result'] as $putovanje) {
                $selected_5 = $putovanje->id == $dc_putovanje_id ? 'selected="selected"' : '';
                echo '<option value="' . $putovanje->id . '" ' . $selected_5 . '>' . $putovanje->naziv . '</option>';
            }
            ?>
        </select>
        <?php

    }

    // Save Meta Box Data
    public function dc_save_travel_metabox($post_id) {
        if (isset($_POST['dc_putovanje_id'])) {
            update_post_meta($post_id, '_dc_putovanje_id', sanitize_text_field($_POST['dc_putovanje_id']));

            if($_POST['dc_putovanje_id'] > 0) {
                add_action('shutdown', function () use ($post_id) {
                    update_post_meta($post_id, 'nd_travel_meta_box_package_woo_product', sanitize_text_field('6926'));
                });
            } else {
                add_action('shutdown', function () use ($post_id) {
                    update_post_meta($post_id, 'nd_travel_meta_box_package_woo_product', sanitize_text_field('0'));
                });
            }
        }
    }

}

new TravelBoxClass();