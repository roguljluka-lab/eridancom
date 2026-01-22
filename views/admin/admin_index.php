<?php

$skola_id = isset($_GET['skola_id']) ? $_GET['skola_id'] : '';
$action = isset($_GET['action']) ? $_GET['action'] : '';
$page = isset($_GET['page']) ? $_GET['page'] : '';
$end_page = explode('-', $page);
$end_page = end($end_page);

$AdminClass = new Admin();

if($end_page == 'dcr') {

	if(!empty($action)) {

        if($action == 'uredi' && isset($_GET['id'])) {

            $handle_new_trip = $AdminClass->dc_handle_trip();
            $rezervacija = $AdminClass->get_rezervacija($_GET['id']);

            if($rezervacija) {

                $dc_settings = $AdminClass->get_settings();

                $logs = $AdminClass->get_logs('rezervacija', $rezervacija->id);
                $payments = $AdminClass->get_payments($rezervacija->id);

                if($rezervacija->putovanje_type == 'skolska' || $rezervacija->putovanje_type == 'izlet') { // IZLETI

                    include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/skolska/uredi.php'); // IZLETI

                } else {

                    include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/ostala/uredi.php');

                }


            }

        } else if($action == 'smece') {

            if(isset($_GET['id'])) {

                // premjesti u smeće
                $AdminClass->delete_rezervacija($_GET['id']);

            }

            $rezervacije = $AdminClass->get_rezervacije(1); // 1 means "smece = 1"
            include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/smece.php');

        } else if($action == 'reaktiviraj') {

            if(isset($_GET['id'])) {

                $AdminClass->reaktiviraj_rezervaciju($_GET['id']);
                exit();

            }

        }

	} else {

        $filter = $AdminClass->get_filter();
        $dc_settings = $AdminClass->get_settings();

        if(isset($_GET['filter']) && $_GET['filter'] == 'res') {

            if(isset($_GET['export']) && $_GET['export'] == 'csv-filter') {

                $rezervacije = $AdminClass->get_filter_results_export();
                include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/export.php');

            } else {

                // $AdminClass->refresh_eracuni_payments(false, true); // osvježiti sve uplate
                $rezervacije = $AdminClass->filter_results();

                if($_GET['type'] == 'skolska' || $_GET['type'] == 'izlet') { // IZLETI

                    include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/skolska/list.php'); // IZLETI

                } else {

                    include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/ostala/list.php');

                }


            }

        } else if(isset($_GET['eracuni']) && $_GET['eracuni'] == 'refresh') {

            $AdminClass->refresh_eracuni_payments(); // osvježiti sve uplate
            wp_redirect(admin_url('admin.php?page=dcr'));

        } else if(isset($_GET['export']) && $_GET['export'] == 'csv') {

            $rezervacije = $AdminClass->get_rezervacije_export();
            include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/export.php');

        } else {

            // $AdminClass->refresh_eracuni_payments(false, true); // osvježiti sve uplate
            $rezervacije = $AdminClass->get_rezervacije();
            include_once(DC_REZERVACIJE_PATH . 'views/admin/rezervacije/list.php');

        }
		
	}

} else if($end_page == 'devlogs') {

    $current_user = wp_get_current_user();

    if ($current_user->user_email === 'info@dominant-core.hr' || $current_user->user_email === 'domagoj.rogosic1@gmail.com') {

        $logs = $AdminClass->get_all_superadmin_logs();
        include_once(DC_REZERVACIJE_PATH . 'views/admin/logs/logs.php');

    } else {

        wp_redirect(admin_url('admin.php?page=dcr'));
        exit();

    }


} else if($end_page == 'putnici') {

    if(!empty($action)) {

        if($action == 'uredi' && isset($_GET['id'])) {

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $handle_putnik_update = $AdminClass->update_putnik();
            }

            $putnik = $AdminClass->get_putnik($_GET['id']);

            include_once(DC_REZERVACIJE_PATH . 'views/admin/putnici/uredi_putnika.php');

        } else if($action == 'smece' && !isset($_GET['id'])) {

            $putnici = $AdminClass->get_putnici(0);
            include_once(DC_REZERVACIJE_PATH . 'views/admin/putnici/putnici.php');

        } else if($action == 'smece' && isset($_GET['id'])) {

            $AdminClass->delete_putnik($_GET['id']);

        }

    } else {

        $putnici = $AdminClass->get_putnici();
        include_once(DC_REZERVACIJE_PATH . 'views/admin/putnici/putnici.php');

    }

} else if($end_page == 'skole') {

    if(isset($skola_id) && $skola_id > 0) {

        $razredi = $AdminClass->get_skola_razredi($skola_id);
        include_once(DC_REZERVACIJE_PATH . 'views/admin/skole/razredi.php');

    } else {

        $skole = $AdminClass->get_skole();
        include_once(DC_REZERVACIJE_PATH . 'views/admin/skole/skole.php');

    }

} else if($end_page == 'putovanja') {

    if(isset($action) && !empty($action)) {
        if($action == 'dodaj-skolsko') {

            $skole = $AdminClass->get_skole();
            include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/skolska/dodaj-uredi.php');

        } else if($action == 'dodaj-izlet') { // IZLETI

            $skole = $AdminClass->get_skole(); // IZLETI
            include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/izlet/dodaj-uredi.php'); // IZLETI

        } else if($action == 'dodaj-ostalo') {

            include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/ostala/dodaj-uredi.php');

        } else if($action == 'smece') {

            if(isset($_GET['id'])) {

                // premjesti u smeće
                $AdminClass->delete_putovanje($_GET['id']);

            }

            $putovanja = $AdminClass->get_putovanja(0); // 1 means "status = 1"

            include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/smece.php');

        } else if($action == 'uredi') {

            $skole = $AdminClass->get_skole();
            $logs = $AdminClass->get_logs('putovanje', $_GET['id']);
            $putovanje = $AdminClass->get_putovanje($_GET['id']);

            if($putovanje->type == 'ostala') { // IZLETI

                $additionals = $AdminClass->get_additionals($putovanje->id);
                $post_connection = $AdminClass->check_putovanje_connection($putovanje->id);
                include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/ostala/dodaj-uredi.php');

            } else if($putovanje->type == 'izlet') { // IZLETI

                include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/izlet/dodaj-uredi.php'); // IZLETI

            } else {

                include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/skolska/dodaj-uredi.php');

            }



        }

    } else {

        $type = $_GET['type'] ?? '';
        $filter = $AdminClass->get_filter();
        $putovanja = $AdminClass->get_putovanja(1, $type); // status = 1
        include_once(DC_REZERVACIJE_PATH . 'views/admin/putovanja/putovanja.php');

    }

} else if($end_page == 'postavke') {

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $AdminClass->update_dcr_settings();
    }
	
	$settings = $AdminClass->get_settings();
    $licence = $AdminClass->dominant_core_api(array(
        'licence' => $settings->dc_postavke->licenca)
    , 'check_licence');

    if(isset($action) && $action == 'dozvole') {

        include_once(DC_REZERVACIJE_PATH . 'views/admin/postavke/postavke_dozvole.php');

    } else {

        include_once(DC_REZERVACIJE_PATH . 'views/admin/postavke/postavke.php');

    }

	
} else if($end_page == 'uplate') {

    if(isset($_GET['export']) && $_GET['export'] == 'csv') {

        $payments = $AdminClass->export_all_payments();
        include_once(DC_REZERVACIJE_PATH . 'views/admin/uplate/export.php');

    } else {

        $AdminClass->refresh_eracuni_payments(false, true); // osvježiti sve uplate
        $payments = $AdminClass->get_all_payments();

        if( isset($_POST['payments']) && $_POST['payments'] == 'remove_payment') {

            $payments = $AdminClass->remove_payment($_POST);

            wp_redirect($_SERVER['HTTP_REFERER']);
            exit();
        } else {

            include_once(DC_REZERVACIJE_PATH . 'views/admin/uplate/uplate.php');

        }

    }

}

?>
