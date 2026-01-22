<?php // IZLETI

$admin = new Admin(); // IZLETI
$settings = $admin->get_settings(); // IZLETI
$dc_validate_licence = $admin->dominant_core_api(array( // IZLETI
    'licence' => $settings->dc_postavke->licenca // IZLETI
), 'check_licence'); // IZLETI

$_SESSION['dc_return_url'] = get_permalink(); // IZLETI
$return_url = $_SESSION['dc_return_url']; // IZLETI

if($dc_validate_licence->status == 'success' && $settings->dc_postavke->active == 1) { // IZLETI

    if(isset($_GET['korak'])) { // IZLETI

        if($_GET['korak'] == 1 && isset($_GET['pid'])) { // IZLETI

            $_SESSION['putovanje_id'] = $_GET['pid']; // IZLETI
            wp_redirect($return_url . '/?korak=2'); // IZLETI
            exit(); // IZLETI

        } else if($_GET['korak'] == 2 && isset($_SESSION['putovanje_id'])) { // IZLETI

            $AdminClass = new Admin(); // IZLETI
            $putovanje = $AdminClass->get_putovanje($_SESSION['putovanje_id']); // IZLETI

            include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/izlet/step_2.php'); // IZLETI

        } else if($_GET['korak'] == 3 && isset($_SESSION['dc_first_step_data'])) { // IZLETI

            $AdminClass = new Admin(); // IZLETI
            $putovanje = $AdminClass->get_putovanje($_SESSION['putovanje_id']); // IZLETI

            include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/izlet/step_3.php'); // IZLETI

        } else { // IZLETI

            $form_handler = new Shortcode(); // IZLETI
            $_SESSION['dc_warning'] = 'Neispavna poveznica.'; // IZLETI
            echo '<script>window.location.href = "' . $return_url . '"</script>'; // IZLETI

        } // IZLETI

    } else if(isset($_SESSION['dc_success_reservation'])) { // IZLETI

        $form_handler = new Shortcode(); // IZLETI
        include_once (DC_REZERVACIJE_PATH . 'views/web/thank_you_page.php'); // IZLETI
        $form_handler->dc_reset_session(); // IZLETI

    } else { // IZLETI

        include_once (DC_REZERVACIJE_PATH . 'views/web/rezervacija/izlet/step_1.php'); // IZLETI

    } // IZLETI
} else { // IZLETI

    if($settings->dc_postavke->active != 1) { // IZLETI

        echo '<div class="dc-alert dc-alert-warning">' . nl2br($settings->dc_postavke->active_message) . '</div>'; // IZLETI

    } else { // IZLETI

        echo '<div class="dc-alert dc-alert-danger">Rezervacije trenutno nisu omogućene. Molimo obratite se administratoru.</div>'; // IZLETI

    } // IZLETI

} // IZLETI
