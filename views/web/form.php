<?php

$admin = new Admin();
$settings = $admin->get_settings();
$dc_validate_licence = $admin->dominant_core_api(array(
    'licence' => $settings->dc_postavke->licenca
), 'check_licence');

if($dc_validate_licence->status == 'success' && $settings->dc_postavke->active == 1) {

    if(isset($_GET['korak'])) {

        if($_GET['korak'] == 1 && isset($_GET['pid'])) {

            $_SESSION['putovanje_id'] = $_GET['pid'];
            wp_redirect(get_permalink($settings->dc_postavke->stranica_rezervacije) . '/?korak=2');
            exit();

        } else if($_GET['korak'] == 2 && isset($_SESSION['putovanje_id'])) {

            $AdminClass = new Admin();
            $putovanje = $AdminClass->get_putovanje($_SESSION['putovanje_id']);

            if($putovanje->type == 'ostala') {

                include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/ostala/step_2.php');

            } else {

                include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/skolska/step_2.php');

            }


        } else if($_GET['korak'] == 3 && isset($_SESSION['dc_first_step_data'])) {

            $AdminClass = new Admin();
            $putovanje = $AdminClass->get_putovanje($_SESSION['putovanje_id']);

            if($putovanje->type == 'ostala') {

                $additionals = $AdminClass->get_additionals($putovanje->id);
                include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/ostala/step_3.php');

            } else {

                include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/skolska/step_3.php');

            }

        } else {

            $form_handler = new Shortcode();
            $_SESSION['dc_warning'] = 'Neispavna poveznica.';
            echo '<script>window.location.href = "' . get_permalink($form_handler->dc_settings->stranica_rezervacije) . '"</script>';

        }

    } else if(isset($_SESSION['dc_success_reservation'])) {

        $form_handler = new Shortcode();
        include_once (DC_REZERVACIJE_PATH . 'views/web/thank_you_page.php');
        $form_handler->dc_reset_session();

    } else {
        
        include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/skolska/step_1.php');

    }
} else {

    if($settings->dc_postavke->active != 1) {

        echo '<div class="dc-alert dc-alert-warning">' . nl2br($settings->dc_postavke->active_message) . '</div>';

    } else {

        echo '<div class="dc-alert dc-alert-danger">Rezervacije trenutno nisu omogućene. Molimo obratite se administratoru.</div>';

    }

}

