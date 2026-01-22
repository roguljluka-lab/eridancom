<?php

function dc_activate()
{
    dc_install_base();

// Schedule the cron event to run every 10 minutes
if ( ! wp_next_scheduled( 'cron_refresh_invoices' ) ) {
    wp_schedule_event( time(), 'every_10_minutes', 'cron_refresh_invoices' );
}


}

function dc_deactivate() {

    wp_clear_scheduled_hook( 'cron_refresh_invoices' );

}

function cron_refresh_invoices_function() {

    $admin = new Admin();
    $admin->refresh_eracuni_payments(true);

}

function cron_refresh_schedules( $schedules ) {

    // Svakih 10 minuta
    $schedules['every_10_minutes'] = array(
        'interval' => 10 * 60,
        'display'  => 'Every 10 Minutes'
    );

    // Dnevni cron (ostavljamo ako ikad zatreba)
    $schedules['daily_at_3am'] = array(
        'interval' => 24 * 60 * 60,
        'display'  => 'Once Daily at 3 AM'
    );

    return $schedules;
}

function dc_install_base() {
    dcr_connection();
    dcr_putovanja();
    dcr_putovanja_additionals();
    dcr_putnici();
    dcr_razredi();
    dcr_rezervacije();
    dcr_skole();
    dcr_logs(); // old one, should be removed
    dcr_logovi();
    dcr_postavke();
    dcr_id_uplate();
    dcr_eracuni_payments();
    dcr_offers();
}

function dcr_id_uplate() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_id_uplate';

    $sql = "CREATE TABLE $table_name (
			   `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `reservation_id` int NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_connection() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_connection';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `dcr_table_id` int NOT NULL,
			  `dcr_table` varchar(50) NOT NULL,
			  `belongs_to` int NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_putnici() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_putnici';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `ime` varchar(255) NOT NULL,
			  `prezime` varchar(255) NOT NULL,
			  `adresa` varchar(255) DEFAULT NULL,
			  `pb` varchar(255) DEFAULT NULL,
			  `mjesto` varchar(255) DEFAULT NULL,
			  `oib_putnika` varchar(20) DEFAULT NULL,
			  `rodendan` varchar(20) DEFAULT NULL,
			  `spol` varchar(20) DEFAULT NULL,
			  `vrsta_isprave` varchar(50) DEFAULT NULL,
			  `broj_isprave` varchar(50) DEFAULT NULL,
			  `broj_zdravstvene` varchar(50) DEFAULT NULL,
			  `isprava_vrijedi` varchar(50) DEFAULT NULL,
			  `kontakt` varchar(20) DEFAULT NULL,
			  `email` varchar(100) DEFAULT NULL,
			  `tip` varchar(50) DEFAULT NULL,
			  `status` int(11) NOT NULL DEFAULT 1,
			  `created_at` datetime NOT NULL,
			  `updated_at` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_eracuni_payments() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_eracuni_payments';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `reservation_id` INT(11) NOT NULL,
			  `documentID` varchar(55) NOT NULL,
			  `invoice_number` varchar(55) DEFAULT NULL,
			  `paymentDate` date DEFAULT NULL,
			  `paymentAmount` decimal(10,2) DEFAULT NULL,
			  `methodOfPayment` varchar(55) DEFAULT NULL,
			  `storno` INT(1) DEFAULT 0
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_offers() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_offers';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `reservation_id` INT(11) NOT NULL,
			  `eracuni_offer_id` varchar(55) NOT NULL,
			  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_putovanja() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_putovanja';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `naziv` varchar(255) NOT NULL,
			  `sifra` varchar(20) NOT NULL,
			  `skola_id` INT(11) NULL,
			  `status` int(11) NOT NULL DEFAULT 1,
			  `ukupni_iznos` double(8,2) NOT NULL,
			  `ukupni_iznos_kartica` double(8,2) NOT NULL,
			  `akontacija` double(8,2) NOT NULL,
			  `broj_putnika` INT(11) NOT NULL,
			  `putovanje_od` date NOT NULL,
			  `putovanje_do` date NOT NULL,
			  `eracuni_documentID` varchar(50) NULL,
			  `eracuni_number` varchar(50) NULL,
			  `id_plana_putovanja` INT(11) NULL,
			  `text_ugovora` text NULL,
			  `type` varchar(15) NOT NULL DEFAULT 'skolska',
			  `created_at` datetime NOT NULL,
			  `updated_at` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_putovanja_additionals() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_putovanja_additionals';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `putovanje_id` int(11) NOT NULL,
			  `name` varchar(255) NOT NULL,
			  `amount` double(8,2) NOT NULL,
			  `created_at` datetime NOT NULL,
			  `updated_at` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_razredi() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_razredi';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `naziv` varchar(20) NOT NULL,
			  `skola_id` INT(11) NOT NULL,
			  `razrednik` varchar(150) NOT NULL,
			  `sk_godina` varchar(20) NOT NULL,
			  `created_at` datetime NOT NULL,
			  `updated_at` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_rezervacije() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_rezervacije';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `putovanje_id` int NOT NULL,
			  `skola_id` int DEFAULT NULL,
			  `razred_id` int DEFAULT NULL,
			  `ugovaratelj_id` int DEFAULT NULL,
			  `putnik_id` int DEFAULT NULL,
			  `informacije` text NOT NULL,
			  `nacin_placanja` varchar(50) NOT NULL,
			  `popust` double(8,2) NOT NULL DEFAULT 0.00,
			  `status` int NOT NULL,
			  `broj_rezervacije` int(11) DEFAULT NULL,
			  `broj_eracuni_ponude` varchar(50) NOT NULL,
			  `admin_info` text NULL,
			  `smece` INT(11) NOT NULL DEFAULT 0,
			  `created_at` datetime NOT NULL,
			  `updated_at` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_skole() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_skole';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `naziv` varchar(255) NOT NULL,
			  `adresa_skole` varchar(255) NOT NULL,
			  `mjesto_skole` varchar(255) NOT NULL,
			  `post_skole` varchar(5) NOT NULL,
			  `created_at` datetime NOT NULL,
			  `updated_at` timestamp NOT NULL
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_logs() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_logs';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `username` varchar(255) NULL,
			  `table_name` varchar(255) NULL,
			  `table_id` INT(11) NULL,
			  `rezervacija_id` varchar(255) NULL,
			  `log` text NULL,
			  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_logovi() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_logovi';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `username` varchar(255) NULL,
			  `table_name` varchar(255) NULL,
			  `table_id` INT(11) NULL,
			  `log` text NULL,
			  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

function dcr_postavke() {
    global $wpdb;

    $table_name = $wpdb->prefix . 'dcr_postavke';

    $sql = "CREATE TABLE $table_name (
			  `id` INT(11) NOT NULL AUTO_INCREMENT UNIQUE,
			  `licenca` varchar(155) NULL,
			  `stranica_naplate` int NULL,
			  `stranica_rezervacije` int NULL,
			  `admin_name` varchar(100) NULL,
			  `admin_mail` varchar(100) NULL,
			  `copy_mail` varchar(100) NULL,
			  `naziv_tvrtke` varchar(100) NULL,
			  `adresa_tvrtke` varchar(155) NULL,
			  `postanski_broj_tvrtke` varchar(5) NULL,
			  `mjesto_tvrtke` varchar(100) NULL,
			  `iban_tvrtke` varchar(30) NULL,
			  `banka_tvrtke` varchar(50) NULL,
			  `ws_pay_secret` varchar(255) NULL,
			  `ws_pay_test` INT(11) NOT NULL DEFAULT 1,
			  `eracuni_token` varchar(255) NULL,
			  `eracuni_password` varchar(255) NULL,
			  `eracuni_posl_prostor` varchar(15) NULL,
			  `eracuni_posl_prostor_2` varchar(15) NULL,
			  `radno_vrijeme_od` varchar(15) NULL,
			  `radno_vrijeme_do` varchar(15) NULL,
			  `rezervirano_sati` INT(11) NULL,
			  `slika_potpisa_id` INT(11) NULL,
			  `varijabilni_text_ugovora_draft` text NULL,
			  `prefix_broja` varchar(15) NOT NULL DEFAULT 'DC',
			  `prefix_broja_ostala` varchar(15) NOT NULL DEFAULT 'DC-OST',
			  `eracuni_broj_artikla` varchar(15) NOT NULL DEFAULT '000001',
			  `active` INT(15) NOT NULL DEFAULT 1,
			  `active_message` text NULL,
			  `opci_uvjeti` text NULL,
			  `post_type` text NOT NULL,
			  `zadnje_osvjezeni_eracuni` datetime NULL,
			  `debug_api` INT(1) NOT NULL DEFAULT 0
			) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci";

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

// Function to add main link
function dc_main_link($links) {
    $settings_link = '<a href="' . admin_url('admin.php?page=dcr-postavke') . '">POSTAVKE</a>';
    array_push($links, $settings_link);
    return $links;
}
