<?php

$admin = new Admin();
$settings = $admin->get_settings();
$dc_validate_licence = $admin->dominant_core_api(array(
    'licence' => $settings->dc_postavke->licenca
), 'check_licence');

if($dc_validate_licence->status == 'success' && $settings->dc_postavke->active == 1) {

    $shortcode_handler = new Shortcode();

    if(isset($_GET['ResponseCode']) && $_GET['ResponseCode'] == 15) {

        $_SESSION['dc_danger'] = 'Žao nam je što ste odustali od naplate. Ako trebate dodatnu pomoć slobodno nas kontaktirajte.';

    }

    $shortcode_handler->dc_reset_session();
    include_once (DC_REZERVACIJE_PATH . 'views/web/naplata/formular.php');

} else {

    if($settings->dc_postavke->active != 1) {

        echo '<div class="dc-alert dc-alert-warning">' . nl2br($settings->dc_postavke->active_message) . '</div>';

    } else {

        echo '<div class="dc-alert dc-alert-danger">Uplate trenutno nisu omogućene. Molimo obratite se administratoru.</div>';

    }

}


