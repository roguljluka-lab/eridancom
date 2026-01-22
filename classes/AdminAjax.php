<?php

function get_razredi_by_school_id() {
    global $wpdb;

    $skola_id = $_GET['skola_id'];
    $sk_godina = $_GET['sk_godina'];

    $results = $wpdb->get_results($wpdb->prepare("
        SELECT * 
        FROM {$wpdb->prefix}dcr_razredi 
        WHERE skola_id = %d && sk_godina = %d
    ", $skola_id, $sk_godina));

    $data = array();
    foreach ($results as $result) {
        $data[] = array(
            'razred_id' => $result->id,
            'razred_naziv' => $result->naziv,
            'razred_razrednik' => $result->razrednik
        );
    }

    wp_send_json($data);
}
add_action('wp_ajax_get_razredi_by_school_id', 'get_razredi_by_school_id'); // Handle AJAX requests for logged-in users
add_action('wp_ajax_nopriv_get_razredi_by_school_id', 'get_razredi_by_school_id'); // Handle AJAX requests for non-logged-in users