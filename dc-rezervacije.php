<?php
/*
Plugin Name: DC Rezervacije
Description: Dodatak za upravljanje rezervacijama. Online naplata kreditnim karticama i izdavanje računa. Potrebno imati aktivne WsPay i e-racuni usluge.
Version: 2.8
Author: Domagoj Rogošić
Plugin URI: https://dominant-core.hr
Author URI: https://dominant-core.hr
Text Domain: dc-rezervacije
Domain Path: /languages
Requires at least: 4.0
Requires PHP: 5.6
*/

function start_session() {

    // Nemoj paliti session u REST API pozivima
    // (ovo je jedino što WP Site Health detektira kao problem)
    if ( defined('REST_REQUEST') && REST_REQUEST ) {
        return;
    }

    // SVE OSTALO radi kao i prije (frontend, admin, WSPay callbacks)
    if ( ! session_id() && ! headers_sent() ) {
        session_start();
    }
}
add_action('init', 'start_session', 1);

define('DC_REZERVACIJE_URL', plugin_dir_url(__FILE__));
define('DC_REZERVACIJE_ADMIN_URL', admin_url('admin.php?page=dcr'));
define('DC_REZERVACIJE_PATH', plugin_dir_path(__FILE__));
define('TCPDF_PATH', plugin_dir_path(__FILE__) . 'includes/tcpdf/');

function dc_scripts_frontend() {
    wp_enqueue_style('dcr-style', plugins_url('assets/css/dcr-style.css', __FILE__));
    wp_enqueue_script('jquery-inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', array('jquery'), '5.0.6', true);
    wp_enqueue_script('dcr-script', plugins_url('assets/js/dcr-script.js', __FILE__), array('jquery'), '1.1', true);
}
// Enqueue plugin scripts and styles
add_action('wp_enqueue_scripts', 'dc_scripts_frontend');

function dcr_script_backend() {
    wp_enqueue_style('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css', array(), '4.0.13');
    wp_enqueue_style('dcra-style', plugins_url('assets/css/dcra-style.css', __FILE__));

    wp_enqueue_script('select2', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js', array('jquery'), '4.0.13', true);
    wp_enqueue_script('dcra-script', plugins_url('assets/js/dcra-script.js', __FILE__), array('jquery'), '1.1', true);
    wp_enqueue_script('jquery-inputmask', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.inputmask/5.0.6/jquery.inputmask.min.js', array('jquery'), '5.0.6', true);

    wp_localize_script('dcra-script', 'ajax_object', array('ajax_url' => admin_url('admin-ajax.php')));
}
add_action('admin_enqueue_scripts', 'dcr_script_backend');

function dc_upload_image() {
    // Enqueue media scripts
    wp_enqueue_media();
}
add_action('admin_enqueue_scripts', 'dc_upload_image');

require_once(DC_REZERVACIJE_PATH . 'classes/Install.php');
register_activation_hook(__FILE__, 'dc_activate');
register_deactivation_hook(__FILE__, 'dc_deactivate');

// cron job
add_action( 'cron_refresh_invoices', 'cron_refresh_invoices_function' );
add_filter( 'cron_schedules', 'cron_refresh_schedules' );
// cron job end

add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'dc_main_link');

require_once(DC_REZERVACIJE_PATH . 'classes/dbClass.php');
require_once(DC_REZERVACIJE_PATH . 'classes/eRacuniClass.php');
require_once(DC_REZERVACIJE_PATH . 'classes/Admin.php');
require_once(DC_REZERVACIJE_PATH . 'classes/AdminAjax.php');
require_once(DC_REZERVACIJE_PATH . 'classes/TravelBoxClass.php');
require_once(DC_REZERVACIJE_PATH . 'classes/Shortcode.php');

/**
 * Provjera treba li plugin učitati.
 */
function dc_rezervacije_should_load() {

    // Always load in admin
    if ( is_admin() ) {
        return true;
    }

    // Fallback by URL slug
    if ( isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'rezerviraj-putovanje') !== false ) { // IZLETI
        return true;
    }

    // IZLETI
    if ( isset($_SERVER['REQUEST_URI']) && strpos($_SERVER['REQUEST_URI'], 'rezerviraj-izlet') !== false ) { // IZLETI
        return true; // IZLETI
    } // IZLETI

    // Online naplata stranica po URL-u
    if ( strpos($_SERVER['REQUEST_URI'], 'online-naplata') !== false ) {
        return true;
    }
    
    // PROVJERA SHORTCODEA U SADRŽAJU STRANICE
    global $post;

    if ( $post && isset( $post->post_content ) ) {

        // Svi shortcodeovi koje plugin registrira
        $shortcodes = array( 'dc_rezervacija', 'dc_naplata', 'dc_izleti' ); //'dc_trips'  // IZLETI

        foreach ( $shortcodes as $sc ) {
            if ( has_shortcode( $post->post_content, $sc ) ) {
                return true;
            }
        }
    }

    return false;
}

/**
 * Load plugin conditionally.
 */
if ( is_admin() ) {

    // ADMIN → always load init.php
    require_once( DC_REZERVACIJE_PATH . 'includes/init.php' );

} else {

    // FRONTEND → only when needed
    add_action('wp', function() {

        if ( ! dc_rezervacije_should_load() ) {
            return; // skip plugin on irrelevant pages
        }

        require_once( DC_REZERVACIJE_PATH . 'includes/init.php' );

        $shortcode_instance = new Shortcode();
        $shortcode_instance->handle_dc_form_submit();
    });
}
