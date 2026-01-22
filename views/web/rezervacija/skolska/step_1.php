<?php
global $wpdb;

$putovanja_table = $wpdb->prefix . 'dcr_putovanja';
$current_date    = wp_date('Y-m-d');

// sva aktivna buduća putovanja
$putovanja = $wpdb->get_results(
    $wpdb->prepare(
        "SELECT id, sifra, naziv, putovanje_od, putovanje_do
         FROM {$putovanja_table}
         WHERE status = %d AND putovanje_do >= %s AND type = %s
         ORDER BY putovanje_od ASC",
        1, // IZLETI
        $current_date, // IZLETI
        'skolska' // IZLETI
    )
);
?>
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
    <div class="dc-row">
        <div class="dc-col-12">
            <?php include_once (DC_REZERVACIJE_PATH . 'views/errors/errors.php'); ?>

            <label for="putovanje_id" class="dc-label">
    Odaberite putovanje
</label>

<select name="putovanje_id" id="putovanje_id" class="dc-form-control" required>
    <option value="">-- Odaberite putovanje --</option>

    <?php if ( ! empty( $putovanja ) ) : ?>
        <?php foreach ( $putovanja as $p ) : ?>
            <?php
            $label = strtoupper( $p->naziv . ' – ' . $p->sifra );
            // po želji možeš dodati i datume:  . ' (' . $p->putovanje_od . ' – ' . $p->putovanje_do . ')'
            ?>
            <option value="<?php echo esc_attr( $p->id ); ?>">
                <?php echo esc_html( $label ); ?>
            </option>
        <?php endforeach; ?>
    <?php endif; ?>
</select>
        </div>
    </div>
    <div class="dc-row">
        <div class="dc-col-6">&nbsp;</div>
        <div class="dc-col-6">
            <button type="submit" class="nd_travel_width_100_percentage nd_travel_border_width_0 nd_travel_cursor_pointer nd_travel_margin_top_10 nd_travel_font_family_poppins nd_travel_letter_spacing_1_important dc-mt-3 ">
                Sljedeći korak
            </button>
        </div>
    </div>
</form>
