<?php

class dbClass
{
    private $wpdb;
    private $reservation_table;
    private $id_uplate_table;
    private $school_table;
    private $grades_table;
    private $trips_table;
    private $passengers_table;
    private $settings_table;
    private $connection_table;
    private $offers_table;
    public $dc_settings;

    public function __construct() {
        
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->reservation_table = $this->wpdb->prefix . 'dcr_rezervacije';
        $this->id_uplate_table = $this->wpdb->prefix . 'dcr_id_uplate';
        $this->school_table = $this->wpdb->prefix . 'dcr_skole';
        $this->grades_table = $this->wpdb->prefix . 'dcr_razredi';
        $this->trips_table = $this->wpdb->prefix . 'dcr_putovanja';
        $this->passengers_table = $this->wpdb->prefix . 'dcr_putnici';
        $this->settings_table = $this->wpdb->prefix . 'dcr_postavke';
        $this->connection_table = $this->wpdb->prefix . 'dcr_connection';
        $this->offers_table = $this->wpdb->prefix . 'dcr_offers';

        $dc_settings = $this->wpdb->get_row("SELECT * FROM {$this->postavke_table}");
        $this->dc_settings = $dc_settings;
        
    }
}