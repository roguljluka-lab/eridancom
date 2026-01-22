<?php

function rezervacije_menu() {
    add_menu_page(
        'DC Rezervacije',
        'DC Rezervacije',
        'edit_others_posts',
        'dcr',
        'dcr',
        'dashicons-calendar',
        3
    );
    add_submenu_page(
        'dcr',
        'Rezervacije',
        'Rezervacije',
        'edit_others_posts',
        'dcr',
        'dcr'
    );
    add_submenu_page(
        'dcr',
        'Putovanja',
        'Putovanja',
        'edit_others_posts',
        'dcr-putovanja',
        'dcr'
    );
    add_submenu_page(
        'dcr',
        'Uplate',
        'Uplate',
        'edit_others_posts',
        'dcr-uplate',
        'dcr'
    );
	add_submenu_page(
        'dcr',
        'Putnici i ugovaratelji',
        'Putnici i ugovaratelji',
        'edit_others_posts',
        'dcr-putnici',
        'dcr'
    );
    add_submenu_page(
        'dcr',
        'Škole i razredi',
        'Škole i razredi',
        'edit_others_posts',
        'dcr-skole',
        'dcr'
    );
    add_submenu_page(
        'dcr',
        'Postavke',
        'POSTAVKE',
        'edit_others_posts',
        'dcr-postavke',
        'dcr'
    );
    add_submenu_page(
        'dcr',
        'Alati',
        'Alati',
        'manage_options',
        'dcr-alati',
        'dcr'
    );
    add_submenu_page(
        null, // No parent menu — so it won't appear in the sidebar
        'Logs',
        'Logs',
        'edit_others_posts',
        'dcr-devlogs',
        'dcr'
    );
}

add_action('admin_menu', 'rezervacije_menu');

function dcr() {
    include_once(DC_REZERVACIJE_PATH . 'views/admin/admin_index.php');
}

function dc_rezervacija_shortcode()
{

    ob_start();
    echo '<div class="dc-main">';

    include(DC_REZERVACIJE_PATH . 'views/web/form.php');

    echo '</div>';
    return ob_get_clean();

}

add_shortcode('dc_rezervacija', 'dc_rezervacija_shortcode');

// IZLETI
function dc_izleti_shortcode() // IZLETI
{ // IZLETI

    ob_start(); // IZLETI
    echo '<div class="dc-main">'; // IZLETI

    include(DC_REZERVACIJE_PATH . 'views/web/form_izlet.php'); // IZLETI

    echo '</div>'; // IZLETI
    return ob_get_clean(); // IZLETI

} // IZLETI

add_shortcode('dc_izleti', 'dc_izleti_shortcode'); // IZLETI

function dc_naplata_shortcode()
{

    ob_start();
    echo '<div class="dc-main">';

    include(DC_REZERVACIJE_PATH . 'views/web/naplata/naplata.php');

    echo '</div>';
    return ob_get_clean();

}

add_shortcode('dc_naplata', 'dc_naplata_shortcode');

// Dodavanje funkcionalnosti za ažuriranje plugina
add_filter('plugins_api', 'dc_plugin_update_info', 20, 3);
add_filter('site_transient_update_plugins', 'dc_plugin_push_update');
add_filter('upgrader_post_install', 'dc_plugin_after_update', 10, 3);

/**
 * Informacije o ažuriranju za plugins_api endpoint.
 */
function dc_plugin_update_info($res, $action, $args) {
    if ($action !== 'plugin_information' || $args->slug !== 'dc-rezervacije') {
        return $res;
    }

    // Povucite podatke o ažuriranju sa servera
    $remote = wp_remote_get('https://rezervacije.dominant-core.com/updates/dc-rezervacije-info.json');

    if (!is_wp_error($remote) && wp_remote_retrieve_response_code($remote) === 200) {
        $body = json_decode(wp_remote_retrieve_body($remote));
        $res = (object) array(
            'name' => $body->name,
            'slug' => $body->slug,
            'version' => $body->version,
            'tested' => $body->tested,
            'requires' => $body->requires,
            'download_link' => $body->download_url,
            'requires_php' => $body->requires_php,
            'sections' => array(
                'description' => isset($body->description) ? $body->description : 'Opis nije dostupan.',
                'changelog' => isset($body->changelog) ? $body->changelog : 'Changelog nije dostupan.'
            ),
        );
    }

    return $res;
}

/**
 * Informacije o dostupnom ažuriranju za update_plugins endpoint.
 */
function dc_plugin_push_update($transient) {
    if (empty($transient->checked)) {
        return $transient;
    }

    // Povucite podatke o najnovijoj verziji sa servera
    $remote = wp_remote_get('https://rezervacije.dominant-core.com/updates/dc-rezervacije-info.json');

    if (!is_wp_error($remote) && wp_remote_retrieve_response_code($remote) === 200) {
        $body = json_decode(wp_remote_retrieve_body($remote));

        if (version_compare($transient->checked['dc-rezervacije/dc-rezervacije.php'], $body->version, '<')) {
            $transient->response['dc-rezervacije/dc-rezervacije.php'] = (object) array(
                'slug' => $body->slug,
                'plugin' => 'dc-rezervacije/dc-rezervacije.php',
                'new_version' => $body->version,
                'url' => $body->plugin_uri,
                'package' => $body->download_url,
            );
        }
    }

    return $transient;
}

/**
 * Akcija nakon instalacije ažuriranja.
 */
function dc_plugin_after_update($true, $hook_extra, $result) {
    if ($hook_extra['plugin'] === 'dc-rezervacije/dc-rezervacije.php') {
        // Dodajte potrebne akcije nakon instalacije (npr. reset cache-a)
    }

    return $result;
}
