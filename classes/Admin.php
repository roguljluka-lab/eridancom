<?php

class Admin {
    private $wpdb;
    private $rezervacije_table;
    private $skole_table;
    private $razredi_table;
    private $putovanja_table;
    private $putovanja_additionals_table;
    private $putnici_table;
    private $postavke_table;
    private $connection_table;
    private $log_table;
    private $logovi_table;
    private $payments_table;
    private $per_page;
    private $current_page;
    private $offset;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->rezervacije_table = $this->wpdb->prefix . 'dcr_rezervacije';
        $this->skole_table = $this->wpdb->prefix . 'dcr_skole';
        $this->razredi_table = $this->wpdb->prefix . 'dcr_razredi';
        $this->putovanja_table = $this->wpdb->prefix . 'dcr_putovanja';
        $this->putovanja_additionals_table = $this->wpdb->prefix . 'dcr_putovanja_additionals';
        $this->putnici_table = $this->wpdb->prefix . 'dcr_putnici';
        $this->connection_table = $this->wpdb->prefix . 'dcr_connection';
        $this->postavke_table = $this->wpdb->prefix . 'dcr_postavke';
        $this->log_table = $this->wpdb->prefix . 'dcr_logs';
        $this->logovi_table = $this->wpdb->prefix . 'dcr_logovi';
        $this->payments_table = $this->wpdb->prefix . 'dcr_eracuni_payments';

        $this->per_page = 50;
        $this->current_page = isset($_GET['pa']) ? max(1, intval($_GET['pa'])) : 1;
        $this->offset = ($this->current_page - 1) * $this->per_page;
    }

    public function get_filter()
    {
        $putovanja_query = "SELECT * FROM {$this->putovanja_table}";
        $putovanja_result = $this->wpdb->get_results($putovanja_query, ARRAY_A);
        $putovanja_formated = array();
        foreach ($putovanja_result as $putovanje) {
            $putovanja_formated[] = array(
                "id" => $putovanje['id'],
                "naziv" => $putovanje['naziv'],
                "sifra" => $putovanje['sifra'],
                "broj_putnika" => $putovanje['broj_putnika']
            );
        }

        $razredi_query = "SELECT * FROM {$this->razredi_table}";
        $razredi_results = $this->wpdb->get_results($razredi_query, ARRAY_A);
        $razredi_formatted = array();
        foreach ($razredi_results as $razred) {
            $razredi_formatted[] = array(
                "id" => $razred['id'],
                "naziv" => $razred['naziv'],
                "razrednik" => $razred['razrednik'],
                "sk_godina" => $razred['sk_godina']
            );
        }

        $unique_skola = array();
        foreach ($razredi_formatted as $razred) {
            $sk_godina = $razred['sk_godina'];
            $razred_id = $razred['id'];
            if (!isset($unique_skola[$sk_godina])) {
                $unique_skola[$sk_godina] = $razred_id;
            }
        }

        $skole_query = "SELECT * FROM {$this->skole_table}";
        $skole_result = $this->wpdb->get_results($skole_query, ARRAY_A);
        $skola_formated = array();
        foreach ($skole_result as $skola) {
            $skola_formated[] = array(
                "id" => $skola['id'],
                "naziv" => $skola['naziv'],
            );
        }

        $result = array(
            "putovanja" => $putovanja_formated,
            "razredi" => $razredi_formatted,
            "skole" => $skola_formated,
            "skolske_godine" => $unique_skola
        );

        return $result;
    }

    public function filter_results()
    {

        $current_user_id = get_current_user_id();
        $type_of_view = 'ostala'; // IZLETI
        $additional_condition = " AND rezervacije.skola_id IS NULL AND rezervacije.razred_id IS NULL"; // IZLETI

        if(isset($_GET['type']) && $_GET['type'] == 'skolska') { // IZLETI
            $type_of_view = 'skolska'; // IZLETI
            $additional_condition = ""; // IZLETI
        } else if(isset($_GET['type']) && $_GET['type'] == 'izlet') { // IZLETI
            $type_of_view = 'izlet'; // IZLETI
            $additional_condition = ""; // IZLETI
        } // IZLETI
        update_user_meta($current_user_id, 'dc_type_of_view', $type_of_view);

        $conditions = array();
        $prepared_values = array();

        $order = isset($_GET['or']) ? $_GET['or'] : 'rezervacije.id';
        $sort = isset($_GET['so']) ? $_GET['so']: 'DESC';

        // Define filter parameters and their corresponding columns
        $filters = array(
            'sg' => 'razred.sk_godina',
            'p' => 'putovanje.id',
            's' => 'skola.id',
            'su' => 'rezervacije.status',
            'np' => 'rezervacije.nacin_placanja',
            'r' => 'razred.naziv'
        );

        // Loop through each filter parameter and add condition if not equal to -1
        foreach ($filters as $param => $column) {
            if ($_GET[$param] != -1) {
                $conditions[] = "$column = %s";
                $prepared_values[] = $_GET[$param];
            }
        }

        if ($_GET['od'] && $_GET['do']) {
            $conditions[] = "rezervacije.created_at BETWEEN %s AND %s";
            $prepared_values[] = $_GET['od'];
            $prepared_values[] = $_GET['do'];
        } elseif ($_GET['od'] && !$_GET['do']) {
            // If only 'od' is provided, set the date range between 'od' and today
            $conditions[] = "rezervacije.created_at >= %s";
            $prepared_values[] = $_GET['od'];
        } elseif (!$_GET['od'] && $_GET['do']) {
            // If only 'do' is provided, set the date range between minimum date and 'do'
            $conditions[] = "rezervacije.created_at <= %s";
            $prepared_values[] = $_GET['do'];
        }

        if (!empty($_GET['q'])) {
            // Escape the search query
            $text_query = $this->wpdb->esc_like($_GET['q']);

            // Split the query into words (assuming space-separated words)
            $words = explode(' ', $text_query);

            // Loop through each word in the query
            foreach ($words as $word) {
                // For each word, apply LIKE to all fields
                $word_with_wildcards = '%' . $word . '%';

                $conditions[] = "(
                    putnik.ime LIKE %s OR 
                    putnik.prezime LIKE %s OR 
                    putnik.adresa LIKE %s OR
                    putnik.mjesto LIKE %s OR
                    putnik.oib_putnika LIKE %s OR 
                    putnik.kontakt LIKE %s OR
                    putnik.email LIKE %s OR
                    rezervacije.id LIKE %s OR
                    rezervacije.broj_rezervacije LIKE %s OR
                    ugovaratelj.ime LIKE %s OR
                    ugovaratelj.prezime LIKE %s
                    )";

                // Add the prepared value for each LIKE condition
                $prepared_values = array_merge($prepared_values, array_fill(0, 11, $word_with_wildcards));
            }
        }

        $where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : "";

        if ($type_of_view == 'ostala' || $type_of_view == 'skolska' || $type_of_view == 'izlet') { // IZLETI
            $where_clause .= ($where_clause ? ' AND ' : ' WHERE ') . 'putovanje.type = %s'; // IZLETI
            $prepared_values[] = $type_of_view; // IZLETI
        } // IZLETI

        $where_clause .= $additional_condition;

        $query = "
            SELECT 
                rezervacije.*,
                skola.naziv AS skola_naziv,
                razred.naziv AS razred_naziv,
                razred.razrednik AS razred_razrednik,
                razred.sk_godina AS razred_sk_godina,
                putnik.ime AS putnik_ime,
                putnik.prezime AS putnik_prezime,
                putnik.adresa AS putnik_adresa,
                putnik.pb AS putnik_pb,
                putnik.mjesto AS putnik_mjesto,
                putnik.oib_putnika AS putnik_oib,
                putnik.rodendan AS putnik_rodendan,
                putnik.spol AS putnik_spol,
                putnik.vrsta_isprave AS putnik_vrsta_isprave,
                putnik.broj_isprave AS putnik_broj_isprave,
                putnik.isprava_vrijedi AS putnik_isprava_vrijedi,
                putnik.broj_zdravstvene AS putnik_broj_zdravstvene,
                putnik.kontakt AS putnik_kontakt,
                putovanje.naziv AS putovanje_naziv,
                putovanje.sifra AS putovanje_sifra,
                putovanje.status AS putovanje_status,
                putovanje.ukupni_iznos AS putovanje_ukupni_iznos,
                putovanje.ukupni_iznos_kartica AS putovanje_ukupni_iznos_kartica,
                putovanje.akontacija AS putovanje_akontacija,
                putovanje.type AS putovanje_type,
                ugovaratelj.ime AS ugovaratelj_ime,
                ugovaratelj.prezime AS ugovaratelj_prezime,
                ugovaratelj.email AS ugovaratelj_email,
                ugovaratelj.kontakt AS ugovaratelj_kontakt
            FROM 
                {$this->rezervacije_table} AS rezervacije
            LEFT JOIN 
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            LEFT JOIN 
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            LEFT JOIN 
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            JOIN 
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            JOIN 
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            {$where_clause}
            GROUP BY
                rezervacije.id
            ORDER BY rezervacije.smece ASC, {$order} {$sort}
            LIMIT 
                {$this->offset}, {$this->per_page}
        ";

        $prepared_query = $this->wpdb->prepare($query, $prepared_values);
        $results = $this->wpdb->get_results($prepared_query);

        $count_query = "
            SELECT COUNT(DISTINCT rezervacije.id) AS total
            FROM 
                {$this->rezervacije_table} AS rezervacije
            LEFT JOIN 
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            LEFT JOIN 
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            LEFT JOIN 
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            JOIN 
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            LEFT JOIN 
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            {$where_clause}
        ";

        $prepared_query_count = $this->wpdb->prepare($count_query, $prepared_values);

        // Dohvaćanje ukupnog broja rezultata
        $total_results = $this->wpdb->get_var($prepared_query_count);

        return array(
            'result' => $results,
            'total_count' => $total_results
        );
    }

    public function get_rezervacije($smece = 0)
    {

        if($smece == 0) {
            $current_user_id = get_current_user_id();
            $user_type_of_view = get_user_meta($current_user_id, 'dc_type_of_view', true);

            if($user_type_of_view == 'ostala') { // IZLETI

                wp_redirect(admin_url('admin.php?page=dcr&type=ostala&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'));
                exit();

            } else if($user_type_of_view == 'izlet') { // IZLETI

                wp_redirect(admin_url('admin.php?page=dcr&type=izlet&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1')); // IZLETI
                exit(); // IZLETI

            } else { // IZLETI

                wp_redirect(admin_url('admin.php?page=dcr&type=skolska&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'));
                exit();

            }
        }


        $query = "
            SELECT 
                rezervacije.*,
                skola.naziv AS skola_naziv,
                razred.naziv AS razred_naziv,
                razred.razrednik AS razred_razrednik,
                razred.sk_godina AS razred_sk_godina,
                putnik.ime AS putnik_ime,
                putnik.prezime AS putnik_prezime,
                putnik.adresa AS putnik_adresa,
                putnik.pb AS putnik_pb,
                putnik.mjesto AS putnik_mjesto,
                putnik.oib_putnika AS putnik_oib,
                putnik.rodendan AS putnik_rodendan,
                putnik.spol AS putnik_spol,
                putnik.vrsta_isprave AS putnik_vrsta_isprave,
                putnik.broj_isprave AS putnik_broj_isprave,
                putnik.isprava_vrijedi AS putnik_isprava_vrijedi,
                putnik.broj_zdravstvene AS putnik_broj_zdravstvene,
                putnik.kontakt AS putnik_kontakt,
                putovanje.naziv AS putovanje_naziv,
                putovanje.sifra AS putovanje_sifra,
                putovanje.status AS putovanje_status,
                putovanje.ukupni_iznos AS putovanje_ukupni_iznos,
                putovanje.ukupni_iznos_kartica AS putovanje_ukupni_iznos_kartica,
                putovanje.akontacija AS putovanje_akontacija,
                ugovaratelj.ime AS ugovaratelj_ime,
                ugovaratelj.prezime AS ugovaratelj_prezime,
                ugovaratelj.email AS ugovaratelj_email,
                ugovaratelj.kontakt AS ugovaratelj_kontakt
            FROM 
                {$this->rezervacije_table} AS rezervacije
            LEFT JOIN 
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            LEFT JOIN  
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            LEFT JOIN  
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            LEFT JOIN  
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            LEFT JOIN  
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            WHERE
                rezervacije.smece = $smece
            GROUP BY
                rezervacije.id
            ORDER BY 
                rezervacije.id DESC
            LIMIT 
                {$this->offset}, {$this->per_page}
        ";

        $results = $this->wpdb->get_results($query);

        $count_query = "
            SELECT COUNT(DISTINCT rezervacije.id) AS total
            FROM 
                {$this->rezervacije_table} AS rezervacije
            LEFT JOIN  
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            LEFT JOIN  
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            LEFT JOIN  
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            LEFT JOIN  
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            LEFT JOIN 
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            WHERE
                rezervacije.smece = $smece
        ";

        $total_results = $this->wpdb->get_var($count_query);

        return array(
            'result' => $results,
            'total_count' => $total_results
        );
    }


    public function get_filter_results_export()
    {

        $current_user_id = get_current_user_id(); // IZLETI
        $type_of_view = 'ostala'; // IZLETI
        $additional_condition = " AND rezervacije.skola_id IS NULL AND rezervacije.razred_id IS NULL"; // IZLETI

        if(isset($_GET['type']) && $_GET['type'] == 'skolska') { // IZLETI
            $type_of_view = 'skolska'; // IZLETI
            $additional_condition = ""; // IZLETI
        } else if(isset($_GET['type']) && $_GET['type'] == 'izlet') { // IZLETI
            $type_of_view = 'izlet'; // IZLETI
            $additional_condition = ""; // IZLETI
        } // IZLETI

        $conditions = array();
        $prepared_values = array();

        $order = isset($_GET['or']) ? $_GET['or'] : 'rezervacije.id';
        $sort = isset($_GET['so']) ? $_GET['so']: 'DESC';

        // Define filter parameters and their corresponding columns
        $filters = array(
            'sg' => 'razred.sk_godina',
            'p' => 'putovanje.id',
            's' => 'skola.id',
            'su' => 'rezervacije.status',
            'np' => 'rezervacije.nacin_placanja',
            'r' => 'razred.naziv'
        );

        // Loop through each filter parameter and add condition if not equal to -1
        foreach ($filters as $param => $column) {
            if ($_GET[$param] != -1) {
                $conditions[] = "$column = %s";
                $prepared_values[] = $_GET[$param];
            }
        }

        if ($_GET['od'] && $_GET['do']) {
            $conditions[] = "rezervacije.created_at BETWEEN %s AND %s";
            $prepared_values[] = $_GET['od'];
            $prepared_values[] = $_GET['do'];
        } elseif ($_GET['od'] && !$_GET['do']) {
            // If only 'od' is provided, set the date range between 'od' and today
            $conditions[] = "rezervacije.created_at >= %s";
            $prepared_values[] = $_GET['od'];
        } elseif (!$_GET['od'] && $_GET['do']) {
            // If only 'do' is provided, set the date range between minimum date and 'do'
            $conditions[] = "rezervacije.created_at <= %s";
            $prepared_values[] = $_GET['do'];
        }

        if (!empty($_GET['q'])) {
            // Escape the search query
            $text_query = $this->wpdb->esc_like($_GET['q']);

            // Split the query into words (assuming space-separated words)
            $words = explode(' ', $text_query);

            // Loop through each word in the query
            foreach ($words as $word) {
                // For each word, apply LIKE to all fields
                $word_with_wildcards = '%' . $word . '%';

                $conditions[] = "(
                    putnik.ime LIKE %s OR 
                    putnik.prezime LIKE %s OR 
                    putnik.adresa LIKE %s OR
                    putnik.mjesto LIKE %s OR
                    putnik.oib_putnika LIKE %s OR 
                    putnik.kontakt LIKE %s OR
                    putnik.email LIKE %s OR
                    rezervacije.id LIKE %s OR
                    rezervacije.broj_rezervacije LIKE %s OR
                    ugovaratelj.ime LIKE %s OR
                    ugovaratelj.prezime LIKE %s
                    )";

                // Add the prepared value for each LIKE condition
                $prepared_values = array_merge($prepared_values, array_fill(0, 11, $word_with_wildcards));
            }
        }

        $where_clause = !empty($conditions) ? "WHERE " . implode(" AND ", $conditions) : ""; // IZLETI

        if ($type_of_view == 'ostala' || $type_of_view == 'skolska' || $type_of_view == 'izlet') { // IZLETI
            $where_clause .= ($where_clause ? ' AND ' : ' WHERE ') . 'putovanje.type = %s'; // IZLETI
            $prepared_values[] = $type_of_view; // IZLETI
        } // IZLETI

        $where_clause .= $additional_condition; // IZLETI

        $query = "
            SELECT 
                rezervacije.*,
                skola.naziv AS skola_naziv,
                razred.naziv AS razred_naziv,
                razred.razrednik AS razred_razrednik,
                razred.sk_godina AS razred_sk_godina,
                putnik.ime AS putnik_ime,
                putnik.prezime AS putnik_prezime,
                putnik.adresa AS putnik_adresa,
                putnik.pb AS putnik_pb,
                putnik.mjesto AS putnik_mjesto,
                putnik.oib_putnika AS putnik_oib,
                putnik.rodendan AS putnik_rodendan,
                putnik.spol AS putnik_spol,
                putnik.vrsta_isprave AS putnik_vrsta_isprave,
                putnik.broj_isprave AS putnik_broj_isprave,
                putnik.isprava_vrijedi AS putnik_isprava_vrijedi,
                putnik.broj_zdravstvene AS putnik_broj_zdravstvene,
                putnik.kontakt AS putnik_kontakt,
                putovanje.naziv AS putovanje_naziv,
                putovanje.sifra AS putovanje_sifra,
                putovanje.status AS putovanje_status,
                putovanje.ukupni_iznos AS putovanje_ukupni_iznos,
                putovanje.ukupni_iznos_kartica AS putovanje_ukupni_iznos_kartica,
                putovanje.akontacija AS putovanje_akontacija,
                putovanje.type AS putovanje_type,
                ugovaratelj.ime AS ugovaratelj_ime,
                ugovaratelj.prezime AS ugovaratelj_prezime,
                ugovaratelj.email AS ugovaratelj_email,
                ugovaratelj.kontakt AS ugovaratelj_kontakt
            FROM 
                {$this->rezervacije_table} AS rezervacije
            LEFT JOIN 
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            LEFT JOIN 
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            LEFT JOIN 
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            JOIN 
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            JOIN 
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            {$where_clause}
            GROUP BY
                rezervacije.id
            ORDER BY rezervacije.smece ASC, {$order} {$sort}
            
        ";

        $prepared_query = $this->wpdb->prepare($query, $prepared_values);
        $results = $this->wpdb->get_results($prepared_query);

        return array(
            'result' => $results
        );
    }

    public function get_rezervacije_export()
    {
        $query = "
            SELECT 
                rezervacije.*,
                skola.naziv AS skola_naziv,
                razred.naziv AS razred_naziv,
                razred.razrednik AS razred_razrednik,
                razred.sk_godina AS razred_sk_godina,
                putnik.ime AS putnik_ime,
                putnik.prezime AS putnik_prezime,
                putnik.adresa AS putnik_adresa,
                putnik.pb AS putnik_pb,
                putnik.mjesto AS putnik_mjesto,
                putnik.oib_putnika AS putnik_oib,
                putnik.rodendan AS putnik_rodendan,
                putnik.spol AS putnik_spol,
                putnik.vrsta_isprave AS putnik_vrsta_isprave,
                putnik.broj_isprave AS putnik_broj_isprave,
                putnik.isprava_vrijedi AS putnik_isprava_vrijedi,
                putnik.broj_zdravstvene AS putnik_broj_zdravstvene,
                putnik.kontakt AS putnik_kontakt,
                putovanje.naziv AS putovanje_naziv,
                putovanje.sifra AS putovanje_sifra,
                putovanje.status AS putovanje_status,
                putovanje.ukupni_iznos AS putovanje_ukupni_iznos,
                putovanje.ukupni_iznos_kartica AS putovanje_ukupni_iznos_kartica,
                putovanje.akontacija AS putovanje_akontacija,
                ugovaratelj.ime AS ugovaratelj_ime,
                ugovaratelj.prezime AS ugovaratelj_prezime,
                ugovaratelj.email AS ugovaratelj_email,
                ugovaratelj.kontakt AS ugovaratelj_kontakt
            FROM 
                {$this->rezervacije_table} AS rezervacije
            JOIN 
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            JOIN 
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            JOIN 
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            JOIN 
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            JOIN 
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            GROUP BY
                rezervacije.id
            ORDER BY 
                rezervacije.id DESC
        ";

        $results = $this->wpdb->get_results($query);

        return array(
            'result' => $results
        );
    }

    public function get_rezervacija($rezervacija_id)
    {
        $query = "
            SELECT 
                rezervacije.*,
                skola.naziv AS skola_naziv,
                razred.naziv AS razred_naziv,
                razred.razrednik AS razred_razrednik,
                razred.sk_godina AS razred_sk_godina,
                putnik.ime AS putnik_ime,
                putnik.prezime AS putnik_prezime,
                putnik.adresa AS putnik_adresa,
                putnik.pb AS putnik_pb,
                putnik.mjesto AS putnik_mjesto,
                putnik.oib_putnika AS putnik_oib,
                putnik.rodendan AS putnik_rodendan,
                putnik.spol AS putnik_spol,
                putnik.vrsta_isprave AS putnik_vrsta_isprave,
                putnik.broj_isprave AS putnik_broj_isprave,
                putnik.isprava_vrijedi AS putnik_isprava_vrijedi,
                putnik.broj_zdravstvene AS putnik_broj_zdravstvene,
                putnik.kontakt AS putnik_kontakt,
                putovanje.naziv AS putovanje_naziv,
                putovanje.sifra AS putovanje_sifra,
                putovanje.status AS putovanje_status,
                putovanje.ukupni_iznos AS putovanje_ukupni_iznos,
                putovanje.ukupni_iznos_kartica AS putovanje_ukupni_iznos_kartica,
                putovanje.akontacija AS putovanje_akontacija,
                putovanje.eracuni_number AS putovanje_ta_booking_ref,
                putovanje.text_ugovora AS putovanje_text_ugovora,
                putovanje.type AS putovanje_type,
                ugovaratelj.ime AS ugovaratelj_ime,
                ugovaratelj.prezime AS ugovaratelj_prezime,
                ugovaratelj.email AS ugovaratelj_email,
                ugovaratelj.kontakt AS ugovaratelj_kontakt,
                ugovaratelj.adresa AS ugovaratelj_adresa,
                ugovaratelj.pb AS ugovaratelj_pb,
                ugovaratelj.mjesto AS ugovaratelj_mjesto,
                ugovaratelj.rodendan AS ugovaratelj_rodendan
            FROM 
                {$this->rezervacije_table} AS rezervacije
            LEFT JOIN 
                {$this->skole_table} AS skola ON rezervacije.skola_id = skola.id
            LEFT JOIN 
                {$this->razredi_table} AS razred ON rezervacije.razred_id = razred.id
            LEFT JOIN 
                {$this->putnici_table} AS putnik ON rezervacije.putnik_id = putnik.id
            JOIN 
                {$this->putnici_table} AS ugovaratelj ON rezervacije.ugovaratelj_id = ugovaratelj.id
            JOIN 
                {$this->putovanja_table} AS putovanje ON rezervacije.putovanje_id = putovanje.id
            WHERE 
                rezervacije.id = %d
            ORDER BY 
                rezervacije.id DESC
            LIMIT 1
        ";

        return $this->wpdb->get_row($this->wpdb->prepare($query, $rezervacija_id));

    }

    public function get_payments($reservation_id)
    {

        $query = "SELECT *
              FROM {$this->payments_table}
              WHERE reservation_id = %d AND smece = %s
              ORDER BY id DESC";

        return $this->wpdb->get_results($this->wpdb->prepare($query, $reservation_id, "0"));

    }

    public function remove_payment($post)
    {
        $this->wpdb->update($this->payments_table, array('smece' => "1"), array('id' => $_POST['payment_id']));
        $this->store_logs_data_new('rezervacija', $_POST['remove_reservation_id'], 'Uklonjena je uplata ID' . $_POST['payment_id'] . ' u iznosu od ' . $_POST['remove_amount'] . '€');
    }

    public function get_all_payments()
    {

        $where_statement = "payments.smece != %s";
        $group_statement = "reservation.id";
        $additional_where = false;

        if (isset($_GET['q']) && !empty($_GET['q'])) {
            $search = sanitize_text_field($_GET['q']);
            $words = preg_split('/\s+/', $search); // Split by any whitespace
            $like_clauses = [];
            $params = [];

            foreach ($words as $word) {
                $like = '%' . $this->wpdb->esc_like($word) . '%';

                $like_clauses[] = "( 
                    CAST(reservation.id AS CHAR) LIKE %s OR 
                    payments.invoice_number LIKE %s OR 
                    CAST(payments.paymentAmount AS CHAR) LIKE %s OR 
                    ugovaratelj.ime LIKE %s OR 
                    ugovaratelj.prezime LIKE %s OR 
                    putnik.ime LIKE %s OR 
                    putnik.prezime LIKE %s
                )";

                // Add same $like for each field above (7 fields)
                $params = array_merge($params, array_fill(0, 7, $like));
            }

            $where_statement = "payments.smece != %s AND (" . implode(" AND ", $like_clauses) . ")";
            array_unshift($params, '1'); // For payments.smece != %s

            $group_statement = "payments.id";
            $additional_where = true;
        }

        $query = "
            SELECT 
                payments.*,
                reservation.id AS reservation_id, reservation.nacin_placanja, reservation.putovanje_id,
                trip.id AS putovanje_id, trip.naziv, trip.sifra, trip.ukupni_iznos, trip.ukupni_iznos_kartica, trip.akontacija,
                ugovaratelj.ime, ugovaratelj.prezime,
                putnik.ime as putnik_ime, putnik.prezime as putnik_prezime,
                SUM(payments.paymentAmount) AS total_payment
            FROM {$this->payments_table} payments
            LEFT JOIN {$this->rezervacije_table} reservation ON payments.reservation_id = reservation.id
            LEFT JOIN {$this->putovanja_table} trip ON reservation.putovanje_id = trip.id
            LEFT JOIN {$this->putnici_table} ugovaratelj ON reservation.ugovaratelj_id = ugovaratelj.id
            LEFT JOIN {$this->putnici_table} putnik ON reservation.putnik_id = putnik.id
            WHERE {$where_statement}
            GROUP BY {$group_statement}
            ORDER BY reservation.id DESC, payments.paymentDate DESC
            LIMIT 
                {$this->offset}, {$this->per_page}
        ";

        if ($additional_where) {
            $prepared_query = $this->wpdb->prepare($query, ...$params);
            $results = $this->wpdb->get_results($prepared_query);
        } else {
            $results = $this->wpdb->get_results($this->wpdb->prepare($query, '1'));
        }

        $count_query = "
            SELECT COUNT(DISTINCT payments.id) AS total_count
            FROM {$this->payments_table} payments
            LEFT JOIN {$this->rezervacije_table} reservation ON payments.reservation_id = reservation.id
            LEFT JOIN {$this->putovanja_table} trip ON reservation.putovanje_id = trip.id
            LEFT JOIN {$this->putnici_table} ugovaratelj ON reservation.ugovaratelj_id = ugovaratelj.id
            LEFT JOIN {$this->putnici_table} putnik ON reservation.putnik_id = putnik.id
            WHERE {$where_statement}
        ";

        if ($additional_where) {
            $prepared_count_query = $this->wpdb->prepare($count_query, ...$params);
            $total_count = $this->wpdb->get_var($prepared_count_query);
        } else {
            $total_count = $this->wpdb->get_var($this->wpdb->prepare($count_query, '1'));
        }

        return [
            'result' => $results,
            'total_count' => $total_count
        ];

    }

    public function export_all_payments()
    {

        $query = "
            SELECT 
                payments.*,
                reservation.id AS reservation_id, reservation.nacin_placanja, reservation.putovanje_id,
                trip.id AS putovanje_id, trip.naziv, trip.sifra, trip.ukupni_iznos, trip.ukupni_iznos_kartica, trip.akontacija,
                ugovaratelj.ime, ugovaratelj.prezime,
                putnik.ime as putnik_ime, putnik.prezime as putnik_prezime,
                SUM(payments.paymentAmount) AS total_payment
            FROM {$this->payments_table} payments
            LEFT JOIN {$this->rezervacije_table} reservation ON payments.reservation_id = reservation.id
            LEFT JOIN {$this->putovanja_table} trip ON reservation.putovanje_id = trip.id
            LEFT JOIN {$this->putnici_table} ugovaratelj ON reservation.ugovaratelj_id = ugovaratelj.id
            LEFT JOIN {$this->putnici_table} putnik ON reservation.putnik_id = putnik.id
            GROUP BY reservation.id
            ORDER BY reservation.id DESC, payments.paymentDate DESC
        ";

        return $this->wpdb->get_results($query);

    }

    public function get_skole()
    {
        $query = "SELECT * FROM {$this->skole_table} ORDER BY id DESC";
        return $this->wpdb->get_results($query);
    }

    public function get_skola_razredi($skola_id)
    {
        $query = "SELECT skola.naziv AS skola_naziv, razredi.* 
              FROM {$this->skole_table} AS skola
              LEFT JOIN {$this->razredi_table} AS razredi ON skola.id = razredi.skola_id
              WHERE skola.id = %d";

        return $this->wpdb->get_results($this->wpdb->prepare($query, $skola_id));
    }

    public function get_putnici($status = 1) {

        $query = "
            SELECT 
                putnici.*,
                ugovaratelj.ime AS ugovaratelj_ime,   
                ugovaratelj.prezime AS ugovaratelj_prezime,   
                ugovaratelj.email AS ugovaratelj_email,   
                ugovaratelj.kontakt AS ugovaratelj_kontakt  
            FROM {$this->putnici_table} AS putnici 
            LEFT JOIN {$this->rezervacije_table} AS rezervacija ON putnici.id = rezervacija.putnik_id
            LEFT JOIN {$this->putnici_table} AS ugovaratelj ON rezervacija.ugovaratelj_id = ugovaratelj.id
            WHERE putnici.status = {$status}
            GROUP BY putnici.oib_putnika
            ORDER BY putnici.id DESC
            LIMIT 
                {$this->offset}, {$this->per_page}
        ";

        $results = $this->wpdb->get_results($query);

        $count_query = "
        SELECT COUNT(DISTINCT putnici.id) AS total_count
        FROM {$this->putnici_table} AS putnici 
            LEFT JOIN {$this->rezervacije_table} AS rezervacija ON putnici.id = rezervacija.putnik_id
            LEFT JOIN {$this->putnici_table} AS ugovaratelj ON rezervacija.ugovaratelj_id = ugovaratelj.id
    ";

        $total_count = $this->wpdb->get_var($count_query);

        return [
            'result' => $results,
            'total_count' => $total_count
        ];

    }

    public function update_putnik()
    {

        if(isset($_POST['update_putnik'])) {

            $putnik_data = array(
                'ime' => $_POST['ime'],
                'prezime' => $_POST['prezime'],
                'adresa' => $_POST['adresa'],
                'pb' => $_POST['pb'],
                'mjesto' => $_POST['mjesto'],
                'oib_putnika' => $_POST['oib_putnika'],
                'rodendan' => $_POST['rodendan'],
                'spol' => $_POST['spol'],
                'vrsta_isprave' => $_POST['vrsta_isprave'],
                'broj_isprave' => $_POST['broj_isprave'],
                'isprava_vrijedi' => $_POST['isprava_vrijedi'],
                'kontakt' => $_POST['kontakt'],
                'email' => $_POST['email'],
                'tip' => $_POST['tip']
            );

            $where = array('id' => $_GET['id']);
            $updated = $this->wpdb->update($this->putnici_table, $putnik_data, $where);

            $this->store_logs_data_new('putnik', $_GET['id'], 'Putnik je uređen.');

            $_SESSION['dc_admin_success'] = 'Uspješno spremljeno';
            wp_redirect(admin_url('admin.php?page=dcr-putnici&action=uredi&id=' . $_GET['id']));

        }

    }

    public function get_putnik($putnik_id)
    {
        $query = "
            SELECT * FROM {$this->putnici_table} WHERE id = %d
        ";

        $query = $this->wpdb->prepare($query, $putnik_id);
        return $this->wpdb->get_row($query);
    }

    public function get_putovanja($smece = 1, $type = 'all')
    {
        $where_type = ''; // IZLETI
        if($type == 'skolska' || $type == 'ostala' || $type == 'izlet') { // IZLETI
            $where_type = " && p.type = '" . $type . "'"; // IZLETI
        } // IZLETI

        $query = "
            SELECT 
				p.*, 
				JSON_ARRAYAGG(JSON_OBJECT('id', r.id, 'naziv', r.naziv)) AS razredi,
				JSON_OBJECT('id', s.id, 'naziv', s.naziv) AS skola
            FROM {$this->putovanja_table} p
            LEFT JOIN {$this->connection_table} ct ON p.id = ct.dcr_table_id AND ct.dcr_table = '{$this->putovanja_table}'
            LEFT JOIN {$this->razredi_table} r ON ct.belongs_to = r.id
            LEFT JOIN {$this->skole_table} s ON p.skola_id = s.id
            WHERE p.status = $smece $where_type
            GROUP BY p.id
            ORDER BY p.id DESC
            LIMIT 
                {$this->offset}, {$this->per_page}
        ";

        $results = $this->wpdb->get_results($query);

        $count_query = "
            SELECT 
                COUNT(DISTINCT p.id) AS total_count
            FROM {$this->putovanja_table} p
            LEFT JOIN {$this->connection_table} ct ON p.id = ct.dcr_table_id AND ct.dcr_table = '{$this->putovanja_table}'
            LEFT JOIN {$this->razredi_table} r ON ct.belongs_to = r.id
            LEFT JOIN {$this->skole_table} s ON p.skola_id = s.id
            WHERE p.status = $smece $where_type
        ";
        $total_count = $this->wpdb->get_var($count_query);

        return array(
            'result' => $results,
            'total_count' => $total_count
        );
    }

    public function get_putovanje($putovanje_id)
    {
        $query = "
            SELECT 
                p.*, 
                JSON_ARRAYAGG(JSON_OBJECT('id', r.id, 'naziv', r.naziv, 'razrednik', r.razrednik, 'sk_godina', r.sk_godina)) AS razredi, 
                s.naziv AS skola_naziv
            FROM {$this->putovanja_table} p
            LEFT JOIN {$this->connection_table} ct ON p.id = ct.dcr_table_id AND ct.dcr_table = '{$this->putovanja_table}'
            LEFT JOIN {$this->razredi_table} r ON ct.belongs_to = r.id
            LEFT JOIN {$this->skole_table} s ON p.skola_id = s.id
            WHERE p.id = %d
            GROUP BY p.id
        ";

        $query = $this->wpdb->prepare($query, $putovanje_id);
        return $this->wpdb->get_row($query);
    }

    public function check_putovanje_connection($putovanje_id)
    {
        $post_type = $this->get_dcr_settings()->post_type;

        $args = [
            'post_type'  => $post_type, // Replace with your post type
            'meta_query' => [
                [
                    'key'   => '_dc_putovanje_id',
                    'value' => $putovanje_id,
                ]
            ],
            'posts_per_page' => 1, // Get only one post
        ];

        $query = new WP_Query($args);

        if ($query->have_posts()) {
            return $query->posts[0]->ID;
        } else {
            return 0;
        }
    }

    public function get_additionals($putovanje_id)
    {

        $additionals = "SELECT * FROM {$this->putovanja_additionals_table} WHERE putovanje_id = %d";
        $query = $this->wpdb->prepare($additionals, $putovanje_id);
        return $this->wpdb->get_results($query);

    }

    public function dc_handle_trip()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $dc_settings = $this->get_settings()->dc_postavke;

            if (isset($_POST['submit_type']) && $_POST['submit_type'] == 'create_new') {

                $currentTime = wp_date('Y-m-d H:i:s');
                $edit = false;

                if($_POST['putovanje_type'] == 'skolska') {

                    if (isset($_POST['edit_tour'])) {
                        $edit = true;
                        $putovanje_id = $_POST['edit_tour'];

                        // detach all razredi
                        $this->wpdb->query(
                            $this->wpdb->prepare(
                                "DELETE FROM $this->connection_table WHERE dcr_table_id = %d",
                                $putovanje_id
                            )
                        );
                    }

                    // store skole
                    $skola_id = $_POST['skola_id'];
                    if (!is_numeric($skola_id)) {

                        //create new skola
                        $skole_data = array(
                            'naziv' => $_POST['skola_id'],
                            'mjesto_skole' => $_POST['mjesto_skole'],
                            'adresa_skole' => $_POST['adresa_skole'],
                            'post_skole' => $_POST['post_skole'],
                            'created_at' => $currentTime
                        );
                        $nova_skola = $this->wpdb->insert($this->skole_table, $skole_data);

                        $skola_id = $this->wpdb->insert_id;

                    } else {

                        // update existing skola
                        $skole_data = array(
                            'mjesto_skole' => $_POST['mjesto_skole'],
                            'adresa_skole' => $_POST['adresa_skole'],
                            'post_skole' => $_POST['post_skole'],
                            'updated_at' => $currentTime
                        );
                        $where = array('id' => $_POST['skola_id']);
                        $this->wpdb->update($this->skole_table, $skole_data, $where);

                    }

                    if(!$edit) {

                        $sifra_putovanja = $this->generateUniqueSifra($_POST['putovanje_type']);

                        // store putovanje
                        $putovanje_data = array(
                            'naziv' => $_POST['naziv'],
                            'akontacija' => $_POST['akontacija'],
                            'ukupni_iznos' => $_POST['ukupni_iznos'],
                            'ukupni_iznos_kartica' => $_POST['ukupni_iznos_kartica'],
                            'skola_id' => $skola_id ?? null,
                            'status' => $_POST['status'],
                            'sifra' => $sifra_putovanja,
                            'broj_putnika' => $_POST['broj_putnika'],
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'id_plana_putovanja' => $_POST['id_plana_putovanja'],
                            'text_ugovora' => $_POST['text_ugovora'],
                            'created_at' => $currentTime,
                            'type' => $_POST['putovanje_type']
                        );

                        $novo_putovanje = $this->wpdb->insert($this->putovanja_table, $putovanje_data);
                        $putovanje_id = $this->wpdb->insert_id;

                        // store eracun reservation
                        $data = array(
                            'licence' => $dc_settings->licenca,
                            'md5pass' => $dc_settings->eracuni_password,
                            'token' => $dc_settings->eracuni_token,
                            'naziv' => $_POST['naziv'],
                            'sifra_putovanja' => $sifra_putovanja,
                            'naziv_skole' => empty($_POST['naziv_skole']) ? (empty($_POST['skola_id']) ? '' : $_POST['skola_id']) : $_POST['naziv_skole'],
                            'mjesto_skole' => $_POST['mjesto_skole'] ?? '',
                            'post_skole' => $_POST['post_skole'] ?? '',
                            'adresa_skole' => $_POST['adresa_skole'] ?? '',
                            'broj_putnika' => str_replace('_', '', $_POST['broj_putnika']),
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'ukupni_iznos' => number_format($_POST['ukupni_iznos'] * $_POST['broj_putnika'], 2, '.', '')
                        );

                        $eracuni_tour = $this->dominant_core_api($data, 'cTA');

                        $update_data = array(
                            'eracuni_documentID' => $eracuni_tour->response->result->documentID,
                            'eracuni_number' => $eracuni_tour->response->result->number
                        );

                        $where_putovanje = array('id' => $putovanje_id);
                        $update_putovanje = $this->wpdb->update($this->putovanja_table, $update_data, $where_putovanje);
                        // store eracun reservation end


                    } else {

                        // update putovanje
                        $putovanje_data = array(
                            'naziv' => $_POST['naziv'],
                            'akontacija' => $_POST['akontacija'],
                            'ukupni_iznos' => $_POST['ukupni_iznos'],
                            'ukupni_iznos_kartica' => $_POST['ukupni_iznos_kartica'],
                            'skola_id' => $skola_id ?? null,
                            'status' => $_POST['status'],
                            'broj_putnika' => $_POST['broj_putnika'],
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'id_plana_putovanja' => $_POST['id_plana_putovanja'],
                            'text_ugovora' => $_POST['text_ugovora'],
                            'updated_at' => $currentTime
                        );

                        $where = array('id' => $putovanje_id);
                        $updated = $this->wpdb->update($this->putovanja_table, $putovanje_data, $where);

                        $table_data = $this->wpdb->prepare(
                            "SELECT p.eracuni_number, p.sifra, s.naziv, s.adresa_skole, s.mjesto_skole, s.post_skole
                         FROM {$this->putovanja_table} AS p
                         INNER JOIN {$this->skole_table} AS s ON p.skola_id = s.id
                         WHERE p.id = %d",
                            $putovanje_id
                        );
                        $get_data = $this->wpdb->get_row($table_data);

                        //update eracuni
                        $data = array(
                            'licence' => $dc_settings->licenca,
                            'md5pass' => $dc_settings->eracuni_password,
                            'token' => $dc_settings->eracuni_token,
                            'document_number' => $get_data->eracuni_number,
                            'sifra_putovanja' => $get_data->sifra,
                            'naziv' => $_POST['naziv'],
                            'naziv_skole' => $get_data->naziv,
                            'mjesto_skole' => $get_data->mjesto_skole,
                            'post_skole' => $get_data->post_skole,
                            'adresa_skole' => $get_data->adresa_skole,
                            'broj_putnika' => str_replace('_', '', $_POST['broj_putnika']),
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'ukupni_iznos' => number_format($_POST['ukupni_iznos'] * $_POST['broj_putnika'], 2, '.', ''),
                            'status' => 'Confirm'
                        );

                        $eracuni_tour = $this->dominant_core_api($data, 'uuTA');

                        $this->store_logs_data_new('putovanje', $putovanje_id, 'Uređeno putovanje.');

                    }

                } else if($_POST['putovanje_type'] == 'izlet') { // IZLETI

                    if (isset($_POST['edit_tour'])) { // IZLETI
                        $edit = true; // IZLETI
                        $putovanje_id = $_POST['edit_tour']; // IZLETI

                        // detach all razredi // IZLETI
                        $this->wpdb->query( // IZLETI
                            $this->wpdb->prepare( // IZLETI
                                "DELETE FROM $this->connection_table WHERE dcr_table_id = %d", // IZLETI
                                $putovanje_id // IZLETI
                            ) // IZLETI
                        ); // IZLETI
                    } // IZLETI

                    // store skole // IZLETI
                    $skola_id = $_POST['skola_id']; // IZLETI
                    if (!is_numeric($skola_id)) { // IZLETI

                        //create new skola // IZLETI
                        $skole_data = array( // IZLETI
                            'naziv' => $_POST['skola_id'], // IZLETI
                            'mjesto_skole' => $_POST['mjesto_skole'], // IZLETI
                            'adresa_skole' => $_POST['adresa_skole'], // IZLETI
                            'post_skole' => $_POST['post_skole'], // IZLETI
                            'created_at' => $currentTime // IZLETI
                        ); // IZLETI
                        $nova_skola = $this->wpdb->insert($this->skole_table, $skole_data); // IZLETI

                        $skola_id = $this->wpdb->insert_id; // IZLETI

                    } else { // IZLETI

                        // update existing skola // IZLETI
                        $skole_data = array( // IZLETI
                            'mjesto_skole' => $_POST['mjesto_skole'], // IZLETI
                            'adresa_skole' => $_POST['adresa_skole'], // IZLETI
                            'post_skole' => $_POST['post_skole'], // IZLETI
                            'updated_at' => $currentTime // IZLETI
                        ); // IZLETI
                        $where = array('id' => $_POST['skola_id']); // IZLETI
                        $this->wpdb->update($this->skole_table, $skole_data, $where); // IZLETI

                    } // IZLETI

                    $putovanje_od = $_POST['putovanje_od']; // IZLETI
                    $putovanje_do = $_POST['putovanje_od']; // IZLETI
                    $akontacija = $_POST['ukupni_iznos']; // IZLETI

                    if(!$edit) { // IZLETI

                        $sifra_putovanja = $this->generateUniqueSifra($_POST['putovanje_type']); // IZLETI

                        // store putovanje // IZLETI
                        $putovanje_data = array( // IZLETI
                            'naziv' => $_POST['naziv'], // IZLETI
                            'akontacija' => $akontacija, // IZLETI
                            'ukupni_iznos' => $_POST['ukupni_iznos'], // IZLETI
                            'ukupni_iznos_kartica' => 0, // IZLETI
                            'skola_id' => $skola_id ?? null, // IZLETI
                            'status' => $_POST['status'], // IZLETI
                            'sifra' => $sifra_putovanja, // IZLETI
                            'broj_putnika' => $_POST['broj_putnika'], // IZLETI
                            'putovanje_od' => $putovanje_od, // IZLETI
                            'putovanje_do' => $putovanje_do, // IZLETI
                            'id_plana_putovanja' => $_POST['id_plana_putovanja'], // IZLETI
                            'text_ugovora' => $_POST['text_ugovora'], // IZLETI
                            'created_at' => $currentTime, // IZLETI
                            'type' => 'izlet' // IZLETI
                        ); // IZLETI

                        $novo_putovanje = $this->wpdb->insert($this->putovanja_table, $putovanje_data); // IZLETI
                        $putovanje_id = $this->wpdb->insert_id; // IZLETI

                        // store eracun reservation // IZLETI
                        $data = array( // IZLETI
                            'licence' => $dc_settings->licenca, // IZLETI
                            'md5pass' => $dc_settings->eracuni_password, // IZLETI
                            'token' => $dc_settings->eracuni_token, // IZLETI
                            'naziv' => $_POST['naziv'], // IZLETI
                            'sifra_putovanja' => $sifra_putovanja, // IZLETI
                            'naziv_skole' => empty($_POST['naziv_skole']) ? (empty($_POST['skola_id']) ? '' : $_POST['skola_id']) : $_POST['naziv_skole'], // IZLETI
                            'mjesto_skole' => $_POST['mjesto_skole'] ?? '', // IZLETI
                            'post_skole' => $_POST['post_skole'] ?? '', // IZLETI
                            'adresa_skole' => $_POST['adresa_skole'] ?? '', // IZLETI
                            'broj_putnika' => str_replace('_', '', $_POST['broj_putnika']), // IZLETI
                            'putovanje_od' => $putovanje_od, // IZLETI
                            'putovanje_do' => $putovanje_do, // IZLETI
                            'ukupni_iznos' => number_format($_POST['ukupni_iznos'] * $_POST['broj_putnika'], 2, '.', '') // IZLETI
                        ); // IZLETI

                        $eracuni_tour = $this->dominant_core_api($data, 'cTA'); // IZLETI

                        $update_data = array( // IZLETI
                            'eracuni_documentID' => $eracuni_tour->response->result->documentID, // IZLETI
                            'eracuni_number' => $eracuni_tour->response->result->number // IZLETI
                        ); // IZLETI

                        $where_putovanje = array('id' => $putovanje_id); // IZLETI
                        $update_putovanje = $this->wpdb->update($this->putovanja_table, $update_data, $where_putovanje); // IZLETI
                        // store eracun reservation end // IZLETI


                    } else { // IZLETI

                        // update putovanje // IZLETI
                        $putovanje_data = array( // IZLETI
                            'naziv' => $_POST['naziv'], // IZLETI
                            'akontacija' => $akontacija, // IZLETI
                            'ukupni_iznos' => $_POST['ukupni_iznos'], // IZLETI
                            'ukupni_iznos_kartica' => 0, // IZLETI
                            'skola_id' => $skola_id ?? null, // IZLETI
                            'status' => $_POST['status'], // IZLETI
                            'broj_putnika' => $_POST['broj_putnika'], // IZLETI
                            'putovanje_od' => $putovanje_od, // IZLETI
                            'putovanje_do' => $putovanje_do, // IZLETI
                            'id_plana_putovanja' => $_POST['id_plana_putovanja'], // IZLETI
                            'text_ugovora' => $_POST['text_ugovora'], // IZLETI
                            'updated_at' => $currentTime, // IZLETI
                            'type' => 'izlet' // IZLETI
                        ); // IZLETI

                        $where = array('id' => $putovanje_id); // IZLETI
                        $updated = $this->wpdb->update($this->putovanja_table, $putovanje_data, $where); // IZLETI

                        $table_data = $this->wpdb->prepare( // IZLETI
                            "SELECT p.eracuni_number, p.sifra, s.naziv, s.adresa_skole, s.mjesto_skole, s.post_skole
                         FROM {$this->putovanja_table} AS p
                         INNER JOIN {$this->skole_table} AS s ON p.skola_id = s.id
                         WHERE p.id = %d", // IZLETI
                            $putovanje_id // IZLETI
                        ); // IZLETI
                        $get_data = $this->wpdb->get_row($table_data); // IZLETI

                        //update eracuni // IZLETI
                        $data = array( // IZLETI
                            'licence' => $dc_settings->licenca, // IZLETI
                            'md5pass' => $dc_settings->eracuni_password, // IZLETI
                            'token' => $dc_settings->eracuni_token, // IZLETI
                            'document_number' => $get_data->eracuni_number, // IZLETI
                            'sifra_putovanja' => $get_data->sifra, // IZLETI
                            'naziv' => $_POST['naziv'], // IZLETI
                            'naziv_skole' => $get_data->naziv, // IZLETI
                            'mjesto_skole' => $get_data->mjesto_skole, // IZLETI
                            'post_skole' => $get_data->post_skole, // IZLETI
                            'adresa_skole' => $get_data->adresa_skole, // IZLETI
                            'broj_putnika' => str_replace('_', '', $_POST['broj_putnika']), // IZLETI
                            'putovanje_od' => $putovanje_od, // IZLETI
                            'putovanje_do' => $putovanje_do, // IZLETI
                            'ukupni_iznos' => number_format($_POST['ukupni_iznos'] * $_POST['broj_putnika'], 2, '.', ''), // IZLETI
                            'status' => 'Confirm' // IZLETI
                        ); // IZLETI

                        $eracuni_tour = $this->dominant_core_api($data, 'uuTA'); // IZLETI

                        $this->store_logs_data_new('putovanje', $putovanje_id, 'Uređeno putovanje.'); // IZLETI

                    } // IZLETI

                } else {

                    if (isset($_POST['edit_tour'])) {
                        $edit = true;
                        $putovanje_id = $_POST['edit_tour'];

                        // detach all additionals
                        $this->wpdb->query(
                            $this->wpdb->prepare(
                                "DELETE FROM $this->putovanja_additionals_table WHERE putovanje_id = %d",
                                $putovanje_id
                            )
                        );
                    }

                    if(!$edit) {

                        $sifra_putovanja = $this->generateUniqueSifra($_POST['putovanje_type']);

                        // store putovanje
                        $putovanje_data = array(
                            'naziv' => $_POST['naziv'],
                            'akontacija' => $_POST['akontacija'],
                            'ukupni_iznos' => $_POST['ukupni_iznos'],
                            'ukupni_iznos_kartica' => $_POST['ukupni_iznos_kartica'],
                            'skola_id' => $skola_id ?? null,
                            'status' => $_POST['status'],
                            'sifra' => $sifra_putovanja,
                            'broj_putnika' => $_POST['broj_putnika'],
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'id_plana_putovanja' => $_POST['id_plana_putovanja'],
                            'text_ugovora' => $_POST['text_ugovora'],
                            'created_at' => $currentTime,
                            'type' => $_POST['putovanje_type']
                        );

                        $novo_putovanje = $this->wpdb->insert($this->putovanja_table, $putovanje_data);
                        $putovanje_id = $this->wpdb->insert_id;

                        // store eracun reservation
                        $data = array(
                            'licence' => $dc_settings->licenca,
                            'md5pass' => $dc_settings->eracuni_password,
                            'token' => $dc_settings->eracuni_token,
                            'naziv' => $_POST['naziv'],
                            'sifra_putovanja' => $sifra_putovanja,
                            'naziv_skole' => $dc_settings->naziv_tvrtke,
                            'mjesto_skole' => $dc_settings->mjesto_tvrtke,
                            'post_skole' => $dc_settings->postanski_broj_tvrtke,
                            'adresa_skole' => $dc_settings->adresa_tvrtke,
                            'broj_putnika' => str_replace('_', '', $_POST['broj_putnika']),
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'ukupni_iznos' => number_format($_POST['ukupni_iznos'] * $_POST['broj_putnika'], 2, '.', '')
                        );

                        $eracuni_tour = $this->dominant_core_api($data, 'cTA');

                        $update_data = array(
                            'eracuni_documentID' => $eracuni_tour->response->result->documentID,
                            'eracuni_number' => $eracuni_tour->response->result->number
                        );

                        $where_putovanje = array('id' => $putovanje_id);
                        $update_putovanje = $this->wpdb->update($this->putovanja_table, $update_data, $where_putovanje);
                        // store eracun reservation end


                    } else {

                        // update ostalo putovanje
                        $putovanje_data = array(
                            'naziv' => $_POST['naziv'],
                            'akontacija' => $_POST['akontacija'],
                            'ukupni_iznos' => $_POST['ukupni_iznos'],
                            'ukupni_iznos_kartica' => $_POST['ukupni_iznos_kartica'],
                            'skola_id' => $skola_id ?? null,
                            'status' => $_POST['status'],
                            'broj_putnika' => $_POST['broj_putnika'],
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'id_plana_putovanja' => $_POST['id_plana_putovanja'],
                            'text_ugovora' => $_POST['text_ugovora'],
                            'updated_at' => $currentTime
                        );

                        $where = array('id' => $putovanje_id);
                        $updated = $this->wpdb->update($this->putovanja_table, $putovanje_data, $where);

                        $table_data = $this->wpdb->prepare(
                            "SELECT p.eracuni_number, p.sifra
                         FROM {$this->putovanja_table} AS p
                         WHERE p.id = %d",
                            $putovanje_id
                        );
                        $get_data = $this->wpdb->get_row($table_data);

                        //update eracuni
                        $data = array(
                            'licence' => $dc_settings->licenca,
                            'md5pass' => $dc_settings->eracuni_password,
                            'token' => $dc_settings->eracuni_token,
                            'document_number' => $get_data->eracuni_number,
                            'sifra_putovanja' => $get_data->sifra,
                            'naziv' => $_POST['naziv'],
                            'naziv_skole' => $dc_settings->naziv_tvrtke,
                            'mjesto_skole' => $dc_settings->mjesto_tvrtke,
                            'post_skole' => $dc_settings->postanski_broj_tvrtke,
                            'adresa_skole' => $dc_settings->adresa_tvrtke,
                            'broj_putnika' => str_replace('_', '', $_POST['broj_putnika']),
                            'putovanje_od' => $_POST['putovanje_od'],
                            'putovanje_do' => $_POST['putovanje_do'],
                            'ukupni_iznos' => number_format($_POST['ukupni_iznos'] * $_POST['broj_putnika'], 2, '.', ''),
                            'status' => 'Confirm'
                        );

                        $eracuni_tour = $this->dominant_core_api($data, 'uuTA');

                        $this->store_logs_data_new('putovanje', $putovanje_id, 'Uređeno putovanje.');

                    }

                }


                // store razredi // IZLETI
                if($_POST['putovanje_type'] == 'izlet') { // IZLETI
                    if(isset($_POST['razredi']) && isset($skola_id)) { // IZLETI
                        foreach ($_POST['razredi'] as $key => $razred) { // IZLETI

                            if (is_numeric($key)) { // IZLETI
                                $razred_id = $key; // IZLETI
                            } else { // IZLETI

                                //create new // IZLETI
                                $razredi_data = array( // IZLETI
                                    'naziv' => $razred, // IZLETI
                                    'skola_id' => $skola_id ?? null, // IZLETI
                                    'razrednik' => 'RAZREDNIK', // IZLETI
                                    'sk_godina' => $_POST['skolska_godina'], // IZLETI
                                    'created_at' => $currentTime // IZLETI
                                ); // IZLETI

                                $novi_razred = $this->wpdb->insert($this->razredi_table, $razredi_data); // IZLETI
                                $razred_id = $this->wpdb->insert_id; // IZLETI

                            } // IZLETI

                            $putovanja_razredi_data = array( // IZLETI
                                'dcr_table_id' => $putovanje_id, // IZLETI
                                'dcr_table' => $this->putovanja_table, // IZLETI
                                'belongs_to' => $razred_id // IZLETI
                            ); // IZLETI
                            $this->wpdb->insert($this->connection_table, $putovanja_razredi_data); // IZLETI

                        } // IZLETI
                    } else if(!isset($_POST['razredi']) && isset($skola_id)) { // IZLETI

                        $query = "SELECT * FROM {$this->razredi_table} WHERE skola_id = {$skola_id} AND naziv = 'Nedefiniran razred'"; // IZLETI
                        $razred = $this->wpdb->get_row($query); // IZLETI

                        if($razred) { // IZLETI

                            $razred_id = $razred->id; // IZLETI
                            $putovanja_razredi_data = array( // IZLETI
                                'dcr_table_id' => $putovanje_id, // IZLETI
                                'dcr_table' => $this->putovanja_table, // IZLETI
                                'belongs_to' => $razred_id // IZLETI
                            ); // IZLETI
                            $this->wpdb->insert($this->connection_table, $putovanja_razredi_data); // IZLETI

                        } else { // IZLETI

                            // kreiraj nedefinirani razred ako ne postoji // IZLETI
                            $razredi_data = array( // IZLETI
                                'naziv' => 'Nedefiniran razred', // IZLETI
                                'skola_id' => $skola_id, // IZLETI
                                'razrednik' => 'RAZREDNIK', // IZLETI
                                'sk_godina' => $_POST['skolska_godina'], // IZLETI
                                'created_at' => $currentTime // IZLETI
                            ); // IZLETI

                            $this->wpdb->insert($this->razredi_table, $razredi_data); // IZLETI
                            $razred_id = $this->wpdb->insert_id; // IZLETI

                            $putovanja_razredi_data = array( // IZLETI
                                'dcr_table_id' => $putovanje_id, // IZLETI
                                'dcr_table' => $this->putovanja_table, // IZLETI
                                'belongs_to' => $razred_id // IZLETI
                            ); // IZLETI
                            $this->wpdb->insert($this->connection_table, $putovanja_razredi_data); // IZLETI

                        } // IZLETI

                    } // IZLETI
                } else if(isset($_POST['razredi']) && isset($skola_id)) { // IZLETI
                    foreach ($_POST['razredi'] as $key => $razred) {

                        if(!empty($_POST['razrednici'][$key]) && $_POST['razrednici'][$key] != '') {
                            $razrednik_name = $_POST['razrednici'][$key];
                        } else {
                            $razrednik_name = 'Još nije određen';
                        }

                        if (is_numeric($key)) {
                            // update
                            $razredi_data = array(
                                'razrednik' => $razrednik_name,
                                'updated_at' => $currentTime
                            );

                            $razred_id = $key;
                            $where = array('id' => $razred_id);
                            $this->wpdb->update($this->razredi_table, $razredi_data, $where);

                        } else {

                            //create new
                            $razredi_data = array(
                                'naziv' => $razred,
                                'skola_id' => $skola_id ?? null,
                                'razrednik' => $razrednik_name,
                                'sk_godina' => $_POST['skolska_godina'],
                                'created_at' => $currentTime
                            );

                            $novi_razred = $this->wpdb->insert($this->razredi_table, $razredi_data);
                            $razred_id = $this->wpdb->insert_id;

                        }

                        $putovanja_razredi_data = array(
                            'dcr_table_id' => $putovanje_id,
                            'dcr_table' => $this->putovanja_table,
                            'belongs_to' => $razred_id
                        );
                        $this->wpdb->insert($this->connection_table, $putovanja_razredi_data);

                    }
                } else if(!isset($_POST['razredi']) && isset($skola_id)) {

                    $query = "SELECT * FROM {$this->razredi_table} WHERE skola_id = {$skola_id} AND naziv = 'Nedefiniran razred'";
                    $razred = $this->wpdb->get_row($query);

                    if($razred) {

                        $razred_id = $razred->id;
                        $putovanja_razredi_data = array(
                            'dcr_table_id' => $putovanje_id,
                            'dcr_table' => $this->putovanja_table,
                            'belongs_to' => $razred_id
                        );
                        $this->wpdb->insert($this->connection_table, $putovanja_razredi_data);

                    } else {

                        // kreiraj nedefinirani razred ako ne postoji
                        $razredi_data = array(
                            'naziv' => 'Nedefiniran razred',
                            'skola_id' => $skola_id,
                            'razrednik' => 'Nedefiniran razrednik',
                            'sk_godina' => $_POST['skolska_godina'],
                            'created_at' => $currentTime
                        );

                        $this->wpdb->insert($this->razredi_table, $razredi_data);
                        $razred_id = $this->wpdb->insert_id;

                        $putovanja_razredi_data = array(
                            'dcr_table_id' => $putovanje_id,
                            'dcr_table' => $this->putovanja_table,
                            'belongs_to' => $razred_id
                        );
                        $this->wpdb->insert($this->connection_table, $putovanja_razredi_data);

                    }

                }

                foreach($_POST['additionals']['amount'] as $amount_key => $amount) {
                    $name = $_POST['additionals']['name'][$amount_key];
                    if(!empty($amount) && !empty($name)) {

                        $additionals = array(
                            'putovanje_id' => $putovanje_id,
                            'name' => $name,
                            'amount' => $amount,
                            'created_at' => $currentTime
                        );

                        $this->wpdb->insert($this->putovanja_additionals_table, $additionals);
                    }
                }

                $_SESSION['dc_admin_success'] = 'Uspješno spremljeno.';
                wp_redirect(admin_url('admin.php?page=dcr-putovanja'));

            } else if (isset($_POST['update_reservation'])) {

                $rezervacija_data = array(
                    'admin_info' => $_POST['admin_info'],
                    'popust' => $_POST['popust'],
                    'status' => $_POST['status']
                );

                $where = array('id' => $_POST['rezervacija_id']);
                $this->wpdb->update($this->rezervacije_table, $rezervacija_data, $where);

                $this->store_logs_data_new('rezervacija', $_POST['rezervacija_id'], 'Rezervacija je uređena.<br>Popust: ' . $_POST['popust'] . '<br>Status: ' . $_POST['status'] . '<br>Admin napomene: ' . $_POST['admin_info']);

                return 'Uspješno spremljeno';

            } else if( isset($_POST['eracuni_new_payment']) && $_POST['eracuni_new_payment'] == 'offer_invoice') {

                $reservation = $this->get_rezervacija($_GET['id']);

                $data = array(
                    'licence' => $dc_settings->licenca,
                    'md5pass' => $dc_settings->eracuni_password,
                    'token' => $dc_settings->eracuni_token,
                    'eracuni_broj_artikla' => $dc_settings->eracuni_broj_artikla,
                    'ime_ugovaratelja' => $reservation->ugovaratelj_ime,
                    'prezime_ugovaratelja' => $reservation->ugovaratelj_prezime,
                    'adresa_ugovaratelja' => $reservation->putnik_adresa,
                    'post_ugovaratelja' => $reservation->putnik_pb,
                    'mjesto_ugovaratelja' => $reservation->putnik_mjesto,
                    'number' => $reservation->putovanje_ta_booking_ref, //ta booking ref number
                    'naziv' => $reservation->putovanje_naziv,
                    'sifra' => $reservation->putovanje_sifra,
                    'ime_prezime_putnika' => $reservation->putnik_ime . ' ' . $reservation->putnik_prezime,
                    'iznos_za_uplatu' => $_POST['ukupni_iznos'],
                    'ukupni_iznos_putovanja' => $_POST['ukupni_iznos_putovanja'],
                    'reservation_id' => $reservation->id,
                    'eracuni_posl_prostor' => $dc_settings->eracuni_posl_prostor
                );

                $shortcode = new Shortcode();

                if(isset($_POST['document_type']) && $_POST['document_type'] == 'just_offer')
                {
                    $data['methodOfPayment'] = 'BankTransfer';
                    $offer = $this->dominant_core_api($data, 'cOF');
                    $this->store_logs_data_new('rezervacija', $data['reservation_id'], 'Dodana je nova ponuda broj ' . $offer->response->result->number . '.');
                    return;

                } else {

                    $data['methodOfPayment'] = $_POST['methodOfPayment'];
                    $new_offer_number = $this->dominant_core_api($data, 'cOFAP')->response->result->number;

                    $data['ta_booking_number'] = $reservation->putovanje_ta_booking_ref;
                    $data['ime_prezime_ugovaratelja'] = $reservation->ugovaratelj_ime . ' ' . $reservation->ugovaratelj_prezime;
                    $this->dominant_core_api($data, 'taAP'); // dodavanje uplate na rezervaciju

                    $data['eracuni_broj_ponude'] = $new_offer_number;
                    $invoice = $this->dominant_core_api($data, 'cIn'); // create invoice

                    $mail_poslan = 'Račun nije poslan na mail.';
                    if(isset($_POST['send_mail']) && $_POST['send_mail'] == 1)
                    {
                        $mail_poslan = 'Račun je poslan na mail ' . $reservation->ugovaratelj_email . '.';
                        $invoice_number = $invoice->response->result->number;
                        $data['document_number'] = $invoice_number;
                        $get_pdf_tmp_file = $this->dominant_core_api($data, 'gIn');

                        $pdfContent = base64_decode($get_pdf_tmp_file->response->result->pdfFile);
                        $tmpFilePath = tempnam(sys_get_temp_dir(), 'eracun_') . '.pdf';
                        file_put_contents($tmpFilePath, $pdfContent);

                        $_SESSION['pdf_racun_temp'] = $tmpFilePath;

                        $email_data = array(
                            'putovanje_naziv' => $reservation->putovanje_naziv,
                            'iznos_uplate' => $_POST['ukupni_iznos'],
                            'broj_rezervacije' => $reservation->id,
                            'uplatni_link' => get_permalink($dc_settings->stranica_naplate) . '/?rid=' . $reservation->id
                        );

                        $attachments = array();
                        $attachments['Račun br. ' . $invoice_number . '.pdf'] = $_SESSION['pdf_racun_temp'];

                        $shortcode->dc_send_mail(
                            $reservation->ugovaratelj_email, // primatelj
                            'Vaša uplata je zaprimljena', // naslov
                            'user/reservation_paid_online', // template
                            $email_data,
                            $attachments
                        );

                    }

                    $this->store_logs_data_new('rezervacija', $data['reservation_id'], 'Dodana je nova uplata u iznosu od ' . $_POST['ukupni_iznos'] . ' €. <br>Ponuda broj ' . $new_offer_number . '. <br>Račun broj ' . $invoice->response->result->number . '.<br>' . $mail_poslan);

                    $this->refresh_eracuni_payments(true, true);

                    $_SESSION['dc_success'] = 'Uspješno ste izvršili uplatu u iznosu od ' . $_POST['ukupni_iznos'] . '€ za putovanje "' . $reservation->putovanje_naziv . '".<br>Hvala!';

                }

                $shortcode->dc_reset_session();

            } else if( isset($_POST['contract']) && $_POST['contract'] == 'send_to_mail') {

                $rezervacija = $this->get_rezervacija($_POST['reservation_id']);

                // parametri se moraju definirati radi ugovora
                $parameters = array(
                    'reservation_id' => $_POST['reservation_id'],
                    'naziv' => $rezervacija->putovanje_naziv,
                    'sifra' => $rezervacija->putovanje_sifra,
                    'ukupni_iznos_putovanja' => $_POST['iznos_putovanja'],
                    'dc_naziv_skole' => $rezervacija->skola_naziv,
                    'dc_razred' => $rezervacija->razred_naziv,
                    'text_ugovora' => $rezervacija->putovanje_text_ugovora
                );

                // koristi se u ugovoru
                $dc_first_step_data = array(
                    'ime_putnika' => $rezervacija->putnik_ime,
                    'prezime_putnika' => $rezervacija->prezime_putnika,
                    'ime_ugovaratelja' => $rezervacija->ugovaratelj_ime,
                    'prezime_ugovaratelja' => $rezervacija->ugovaratelj_prezime,
                    'kontakt_ugovaratelja' => $rezervacija->ugovaratelj_kontakt,
                    'email_ugovaratelja' => $rezervacija->ugovaratelj_email,
                    'adresa_putnika' => $rezervacija->putnik_adresa,
                    'mjesto_putnika' => $rezervacija->putnik_mjesto,
                    'postanski_putnika' => $rezervacija->putnik_pb
                );

                // parametri kraj

                // generiraj ugovor
                $attachments = array();
                $potpis_image_url = wp_get_attachment_image_src($dc_settings->slika_potpisa_id, 'full');
                include_once(DC_REZERVACIJE_PATH . 'views/admin/ugovor-uplatnica/ugovor_pdf.php');
                $_SESSION['ugovor_file'] = $temp_filename; // preuzima se ugovor iz tmp file-a u ugovor_pdf.php

                // dodaj ugovor u attachment
                $attachments['Ugovor o putovanju ' . $_POST['reservation_id'] . '-' . $rezervacija->putovanje_sifra . '.pdf'] = $temp_filename;

                $shortcode = new Shortcode();
                $shortcode->dc_send_mail(
                    $_POST['contract_email'], // primatelj
                    'Kopija ugovora za putovanje ' . $rezervacija->putovanje_naziv . ' (' . $rezervacija->putovanje_sifra . ')', // naslov
                    'user/contract_copy',
                    $parameters,
                    $attachments
                );

                $this->store_logs_data_new('rezervacija', $_POST['reservation_id'], 'Poslana je kopija ugovora na mail ' .  $_POST['contract_email'] . '.');
                $_SESSION['dc_admin_success'] = 'Ugovor je uspješno poslan na mail ' . $_POST['contract_email'] . '.';
                wp_redirect($_SERVER['HTTP_REFERER']);
                exit();

            } else if( isset($_POST['invoice']) && $_POST['invoice'] == 'send_to_mail') {

                $parameters = array(
                    'licence' => $dc_settings->licenca,
                    'md5pass' => $dc_settings->eracuni_password,
                    'token' => $dc_settings->eracuni_token,
                    'document_number' => $_POST['invoice_number'],
                    'reservation_id' => $_POST['reservation_id']
                );

                $get_pdf_tmp_file = $this->dominant_core_api($parameters, 'gIn');
                $pdfContent = base64_decode($get_pdf_tmp_file->response->result->pdfFile);
                $tmpFilePath = tempnam(sys_get_temp_dir(), 'eracun_') . '.pdf';
                file_put_contents($tmpFilePath, $pdfContent);
                $attachments['Račun br. ' . $_POST['invoice_number'] . '.pdf'] = $tmpFilePath;

                $shortcode = new Shortcode();
                $shortcode->dc_send_mail(
                    $_POST['email'], // primatelj
                    'Kopija računa broj ' . $_POST['invoice_number'], // naslov
                    'user/invoice_copy',
                    $parameters,
                    $attachments
                );

                $this->store_logs_data_new('rezervacija', $_POST['reservation_id'], 'Poslana je kopija računa na mail ' .  $_POST['email'] . '.');
                $_SESSION['dc_admin_success'] = 'Račun je uspješno poslan na mail ' . $_POST['email'] . '.';
                wp_redirect($_SERVER['HTTP_REFERER']);
                exit();

            } else if( isset($_POST['invoice']) && $_POST['invoice'] == 'storno_invoice') {

                $parameters = array(
                    'licence' => $dc_settings->licenca,
                    'md5pass' => $dc_settings->eracuni_password,
                    'token' => $dc_settings->eracuni_token,
                    'number' => $_POST['invoice_id'],
                    'eracuni_posl_prostor' => $dc_settings->eracuni_posl_prostor
                );

                $storno = $this->dominant_core_api($parameters, 'cancIn');

                if($storno->response->status == 'ok') {

                    $update_storno_status = $this->wpdb->update($this->payments_table, array('storno' => 1), array('id' => $_POST['payment_id']));

                    $this->refresh_eracuni_payments(false, true);
                    $this->store_logs_data_new('rezervacija', $_GET['id'], 'Račun ' . $_POST['invoice_id'] . ' je storniran sa storno računom ' . $storno->response->result->number . '.');
                    $_SESSION['dc_admin_success'] = 'Račun ' . $_POST['invoice_id'] . ' je uspješno storniran sa storno računom ' . $storno->response->result->number . '.';

                } else {

                    $_SESSION['dc_admin_danger'] = 'Račun nije storniran. Greška: ' . $storno->response->description;

                }

                wp_redirect($_SERVER['HTTP_REFERER']);
                exit();

            }

        }
        return ''; // No form submission
    }

    public function get_settings()
    {

        $all_pages = get_pages(array(
            'post_type' => 'page', // Retrieve only pages
            'orderby' => 'title', // Order by title
            'order' => 'ASC', // Ascending order
            'post_status' => 'publish' // Retrieve only published pages
        ));

        $dc_postavke = $this->get_dcr_settings();

        $settings = new stdClass();
        $settings->dc_postavke = $dc_postavke;
        $settings->pages = $all_pages;

        return $settings;

    }

    public function dominant_core_api($data, $method)
    {

        $eracuni = new eRacuniClass();

        $url = 'https://dominant-core.com/dc-rezervacije/live-2.0/dc_api.php';
        $data['method'] = $method;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Accept: application/json',
        ));
        $response = curl_exec($ch);
        curl_close($ch);

        if($method == 'check_licence' || $method == 'wspay') {
            return json_decode($response);
        } else {
            return $eracuni->send_to_eracuni(json_decode($response)->message);
        }


    }

    public function get_dcr_settings()
    {
        $query = "SELECT * FROM {$this->postavke_table}";
        $result = $this->wpdb->get_row($query);

        return $result;
    }

    public function update_dcr_settings()
    {
        // update settings
        $settings_data = array(
            'naziv_tvrtke' => $_POST['naziv_tvrtke'],
            'adresa_tvrtke' => $_POST['adresa_tvrtke'],
            'postanski_broj_tvrtke' => $_POST['postanski_broj_tvrtke'],
            'mjesto_tvrtke' => $_POST['mjesto_tvrtke'],
            'banka_tvrtke' => $_POST['banka_tvrtke'],
            'iban_tvrtke' => $_POST['iban_tvrtke'],
            'ws_pay_test' => $_POST['ws_pay_test'],
            'admin_name' => $_POST['admin_name'],
            'admin_mail' => $_POST['admin_mail'],
            'copy_mail' => $_POST['copy_mail'],
            'stranica_rezervacije' => $_POST['stranica_rezervacije'],
            'stranica_naplate' => $_POST['stranica_naplate'],
            'radno_vrijeme_od' => $_POST['radno_vrijeme_od'],
            'radno_vrijeme_do' => $_POST['radno_vrijeme_do'],
            'rezervirano_sati' => $_POST['rezervirano_sati'],
            'slika_potpisa_id' => $_POST['slika_potpisa_id'],
            'eracuni_posl_prostor' => $_POST['eracuni_posl_prostor'],
            'eracuni_posl_prostor_2' => $_POST['eracuni_posl_prostor_2'],
            'licenca' => $_POST['licenca'],
            'varijabilni_text_ugovora_draft' => $_POST['varijabilni_text_ugovora_draft'],
            'prefix_broja' => $_POST['prefix_broja'],
            'prefix_broja_ostala' => $_POST['prefix_broja_ostala'],
            'eracuni_broj_artikla' => $_POST['eracuni_broj_artikla'],
            'active' => $_POST['active'],
            'active_message' => $_POST['active_message'],
            'opci_uvjeti' => $_POST['opci_uvjeti'],
            'post_type' => $_POST['stranica_putovanja'],
        );

        $ws_pay_secret = $_POST['ws_pay_secret'];
        if($ws_pay_secret != '****') {
            $settings_data['ws_pay_secret'] = $ws_pay_secret;
        }

        $eracuni_token = $_POST['eracuni_token'];
        if($eracuni_token != '****') {
            $settings_data['eracuni_token'] = $eracuni_token;
        }

        $eracuni_password = $_POST['eracuni_password'];
        if($eracuni_password != '****') {
            $settings_data['eracuni_password'] = $eracuni_password;
        }

        $where = array('id' => 1);
        $updated = $this->wpdb->update($this->postavke_table, $settings_data, $where);

        $this->store_logs_data_new('postavke', 1, 'Promijenje su postavke.');

        $_SESSION['dc_admin_success'] = 'Uspješno spremljeno';
        wp_redirect(admin_url('admin.php?page=dcr-postavke'));

    }

    public function potvrdena_rezervacija($rezervacija)
    {

        $circle_color = 'circle-red';
        if(in_array($rezervacija->status, array(4, 5))) {
            $circle_color = 'circle-green';
        } else {
            $current_time = current_time('mysql');
            if(
                $rezervacija->status != 3 &&
                /*($rezervacija->nacin_placanja == 2 OR $rezervacija->nacin_placanja == 4) &&*/
                $rezervacija->created_at >= date('Y-m-d H:i:s', strtotime('-' . $this->get_dcr_settings()->rezervirano_sati . 'HOURS', strtotime($current_time)))
            ) {
                $circle_color = 'circle-orange';
            }
        }

        return $circle_color;

    }

    public function get_payment_status($nacin_placanja)
    {
        switch ($nacin_placanja) {
            case 2:
                $message = 'Transakcijski';
                $status_color = 'status-danger';
                break;
            case 3:
                $message = 'Uplata - WsPay';
                $status_color = 'status-warning';
                break;
            case 4:
                $message = 'Osobni dolazak';
                $status_color = 'status-success';
                break;
            case 5:
                $message = 'WsPay na rate';
                $status_color = 'status-success';
                break;
            default:
                //$message = 'Rezervacija - WsPay'; - deaktivirano 1.4.2025. zbog promjene načina plaćanja (uklonjena rezervacija sa WsPayom)
                $message = 'Rezervacija';
                $status_color = 'status-info';
        }

        return '<span class="status ' . $status_color . '">' . $message . '</span>';
    }

    public function get_status($status)
    {
        switch ($status) {
            case 2:
                $message = 'Započeto ONLINE';
                $status_color = 'status-warning';
                break;
            case 3:
                $message = 'Odustao';
                $status_color = 'status-danger';
                break;
            case 4:
                $message = 'Djelomično plaćeno';
                $status_color = 'status-success';
                break;
            case 5:
                $message = 'Djelomično plaćeno';
                $status_color = 'status-success';
                break;
            case 6:
                $message = 'Plaćeno u cijelosti';
                $status_color = 'status-success-dark';
                break;
            default:
                $message = 'Čekanje uplate';
                $status_color = 'status-info';
        }

        return '<span class="status ' . $status_color . '">' . $message . '</span>';
    }

    public function dc_count_zauzeto_mjesta($putovanje_id)
    {

        $zauzeta_mjesta = $this->wpdb->prepare(
            "SELECT 
                 COUNT(putnici.id) AS zauzeta_mjesta
                 FROM {$this->putnici_table} AS putnici
                 JOIN {$this->putovanja_table} AS putovanje
                 JOIN {$this->rezervacije_table} AS rezervacije ON rezervacije.putnik_id = putnici.id 
                 WHERE putovanje.id = rezervacije.putovanje_id
                 AND putovanje.id = %d 
                 AND rezervacije.status IN (4, 5)
                 AND rezervacije.smece = 0",
            $putovanje_id
        );

        $rezervirana_mjesta = $this->wpdb->prepare(
            "SELECT COUNT(putnici.id) AS occupied_seats 
                FROM {$this->putnici_table} AS putnici
                JOIN {$this->putovanja_table} AS putovanje
                JOIN {$this->rezervacije_table} AS rezervacije ON rezervacije.putnik_id = putnici.id 
                WHERE putovanje.id = rezervacije.putovanje_id
                AND putovanje.id = %d
                AND rezervacije.smece = 0
                /*AND (rezervacije.nacin_placanja = 2 OR rezervacije.nacin_placanja = 4)*/
                AND rezervacije.status NOT IN (5, 4, 3) 
                AND rezervacije.created_at >= NOW() - INTERVAL " . $this->get_dcr_settings()->rezervirano_sati . " HOUR",
            $putovanje_id
        );

        $zauzeta_mjesta = $this->wpdb->get_var($zauzeta_mjesta);
        $rezervirana_mjesta = $this->wpdb->get_var($rezervirana_mjesta);

        return array(
            'zauzeta_mjesta' => $zauzeta_mjesta,
            'rezervirana_mjesta' => $rezervirana_mjesta
        );

    }

    public function dc_get_ukupno_mjesta($putovanje_id)
    {

        $ukupno_mjesta = $this->wpdb->prepare(
            "SELECT broj_putnika 
                FROM {$this->putovanja_table} 
                WHERE id = %d",
            $putovanje_id
        );

        $ukupno_mjesta = $this->wpdb->get_var($ukupno_mjesta);

        return $ukupno_mjesta;

    }

    function generateUniqueSifra($type = 'skolska') {
        $done = false;

        if ($type == 'skolska') {
            $prefix = $this->get_settings()->dc_postavke->prefix_broja;
            $min = 100;
            $max = 9999;
        } elseif ($type == 'izlet') {
            $prefix = 'IZL';
            $min = 1000;
            $max = 9999;
        } else {
            $prefix = $this->get_settings()->dc_postavke->prefix_broja_ostala;
            $min = 100;
            $max = 9999;
        }
        while (!$done) {
            $unique_sifra = $prefix . mt_rand($min, $max);

            $query = "SELECT COUNT(id) as count FROM {$this->putovanja_table} WHERE sifra = %s";
            $query = $this->wpdb->prepare($query, $unique_sifra);
            $result = $this->wpdb->get_row($query);

            if ($result && $result->count < 1) {
                $done = true;
            }
        }
        return $unique_sifra;
    }

    public function dc_insert_demo_putovanja($rows)
    {
        for($x = 0; $x <= $rows; $x++) {
            $putovanje_data = array(
                'naziv' => 'OŠ ' . rand(100, 999),
                'sifra' => $this->generateUniqueSifra(),
                'skola_id' => 1,
                'status' => 1,
                'ukupni_iznos' => 100,
                'ukupni_iznos_kartica' => 120,
                'akontacija' => 30,
                'broj_putnika' => 10,
                'created_at' => current_time('mysql')
            );
            $this->wpdb->insert($this->putovanja_table, $putovanje_data);
        }
    }

    function delete_rezervacija($rezervacija_id)
    {

        $current_status = $this->wpdb->get_var(
            $this->wpdb->prepare(
                "SELECT smece FROM {$this->rezervacije_table} WHERE id = %d",
                $rezervacija_id
            )
        );

        if ($current_status == 1) {
            $message = 'Rezervacija je vraćena iz smeća.';
        } else {
            $message = 'Rezervacija je premještena u smeće.';
        }

        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->rezervacije_table} SET smece = (1 - smece) WHERE id = %d",
                $rezervacija_id
            )
        );

        $this->store_logs_data_new('rezervacija', $rezervacija_id, $message);
        wp_redirect($_SERVER['HTTP_REFERER']); // povratak na prethodnu stranicu
        exit();

    }

    function reaktiviraj_rezervaciju($rezervacija_id)
    {

        $current_time = current_time('mysql');
        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->rezervacije_table} SET created_at = %s WHERE id = %d",
                $current_time,
                $rezervacija_id
            )
        );

        $this->store_logs_data_new('rezervacija', $rezervacija_id, 'Rezervacija je reaktivirana.');
        wp_redirect($_SERVER['HTTP_REFERER']); // povratak na prethodnu stranicu
        exit();

    }

    function delete_putnik($putnik_id) {

        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->putnici_table} SET status = (1 - status) WHERE id = %d",
                $putnik_id
            )
        );

        $_SESSION['dc_admin_success'] = 'Uspješno premješteno.';

        wp_redirect($_SERVER['HTTP_REFERER']); // povratak na prethodnu stranicu
        exit();

    }

    function delete_putovanje($putovanje_id)
    {

        $dc_settings = $this->get_settings()->dc_postavke;
        $table_data = $this->wpdb->prepare(
            "SELECT p.eracuni_number, p.sifra, s.naziv, s.adresa_skole, s.mjesto_skole, s.post_skole, p.ukupni_iznos, p.broj_putnika, p.putovanje_od, p.putovanje_do, p.status
                         FROM {$this->putovanja_table} AS p
                         INNER JOIN {$this->skole_table} AS s ON p.skola_id = s.id
                         WHERE p.id = %d",
            $putovanje_id
        );
        $get_data = $this->wpdb->get_row($table_data);

        //update eracuni
        $data = array(
            'licence' => $dc_settings->licenca,
            'md5pass' => $dc_settings->eracuni_password,
            'token' => $dc_settings->eracuni_token,
            'document_number' => $get_data->eracuni_number,
            'sifra_putovanja' => $get_data->sifra,
            'naziv' => $get_data->naziv,
            'naziv_skole' => $get_data->naziv,
            'mjesto_skole' => $get_data->mjesto_skole,
            'post_skole' => $get_data->post_skole,
            'adresa_skole' => $get_data->adresa_skole,
            'ukupni_iznos' => number_format($get_data->ukupni_iznos * $get_data->broj_putnika, 2, '.', ''),
            'broj_putnika' => str_replace('_', '', $get_data->broj_putnika),
            'putovanje_od' => $get_data->putovanje_od,
            'putovanje_do' => $get_data->putovanje_do,
            'status' => $get_data->status == '1' ? 'Cancelled' : 'Confirm'
        );

        $eracuni_tour = $this->dominant_core_api($data, 'uuTA');

        $this->wpdb->query(
            $this->wpdb->prepare(
                "UPDATE {$this->putovanja_table} SET status = (1 - status) WHERE id = %d",
                $putovanje_id
            )
        );

        if ($get_data->status == 1) {
            $message = 'Putovanje je premješteno u smeće.';
        } else {
            $message = 'Putovanje je vraćeno iz smeća.';
        }
        $this->store_logs_data_new('putovanje', $putovanje_id, $message);
        wp_redirect($_SERVER['HTTP_REFERER']); // povratak na prethodnu stranicu
        exit();

    }

    function get_all_superadmin_logs()
    {

        $per_page = 20;
        $offset = ($this->current_page - 1) * $per_page;

        if (isset($_GET['q']) && !empty($_GET['q'])) {

            $search = sanitize_text_field($_GET['q']);
            $words = preg_split('/\s+/', $search); // Split by whitespace

            $where_clauses = ["table_name LIKE 'a\\_%'"];
            $params = [];

            foreach ($words as $word) {
                $int_word = (int) $word;
                if (ctype_digit($word)) {
                    $where_clauses[] = "table_id = %d";
                    $params[] = $int_word;
                }
            }

            $where_sql = implode(' AND ', $where_clauses);

            $sql = "
                SELECT * FROM {$this->logovi_table}
                WHERE {$where_sql}
                ORDER BY id DESC
                LIMIT {$offset}, {$per_page}
            ";

            $prepared = $this->wpdb->prepare($sql, ...$params);
            $results = $this->wpdb->get_results($prepared);

            $count_query = "
                SELECT COUNT(DISTINCT id) 
                FROM {$this->logovi_table} 
                WHERE {$where_sql}
            ";
            $count_prepared = $this->wpdb->prepare($count_query, ...$params);
            $total_count = $this->wpdb->get_var($count_prepared);

            return array(
                'result' => $results,
                'total_count' => $total_count
            );

        } else {

            $query = "
                SELECT * FROM {$this->logovi_table} 
                WHERE table_name LIKE 'a\_%'
                ORDER BY id DESC
                LIMIT {$offset}, {$per_page}
            ";


            $query = $this->wpdb->prepare($query);
            $results = $this->wpdb->get_results($query);

            $count_query = "
                SELECT COUNT(DISTINCT id) 
                FROM {$this->logovi_table} 
                WHERE table_name LIKE 'a\_%'
                ORDER BY id DESC
            ";
            $total_count = $this->wpdb->get_var($count_query);

            return array(
                'result' => $results,
                'total_count' => $total_count
            );
        }

    }

    function get_logs($table_name, $table_id)
    {
        $query = "
            SELECT * FROM {$this->logovi_table} WHERE table_name = %s AND table_id = %d ORDER BY id DESC
        ";

        $query = $this->wpdb->prepare($query, array($table_name, $table_id));
        return $this->wpdb->get_results($query);

    }

    function store_logs_data($reservation_id, $message)
    {
        $current_user = wp_get_current_user();
        $username = ($current_user->ID) ? $current_user->user_login : null;

        $this->wpdb->insert(
            $this->log_table,
            array(
                'username' => $username,
                'rezervacija_id' => $reservation_id,
                'log' => $message
            )
        );
    }

    function store_logs_data_new($table_name, $table_id, $message)
    {
        $current_user = wp_get_current_user();
        $username = ($current_user->ID) ? $current_user->user_login : null;

        $this->wpdb->insert(
            $this->logovi_table,
            array(
                'username' => $username,
                'table_name' => $table_name,
                'table_id' => $table_id,
                'log' => $message
            )
        );
    }

    public function refresh_eracuni_payments($cron = false, $autoupdate = false) {


        $dc_settings = $this->get_settings()->dc_postavke;

        $ukupno_rezervacija = 0;
        $ukupno_uplata = 0;

        $data = array(
            'licence' => $dc_settings->licenca,
            'md5pass' => $dc_settings->eracuni_password,
            'token' => $dc_settings->eracuni_token,
            'issuedTimestampFrom' => date('Y-m-d H:i:s', strtotime($dc_settings->zadnje_osvjezeni_eracuni . ' -1 minute'))
        );

        $eracuni = $this->dominant_core_api($data, 'sIL')->response;

        if($eracuni->status == 'ok') {

            foreach($eracuni->result as $invoice) {

                 if(isset($invoice->PaymentRecords)) {

                     $documentID = $invoice->documentID;
                     $invoice_desc = $invoice->Items[0]->description;

                     if(isset($invoice->orderReference) || preg_match('/Broj rezervacije:\s*(\d+)/', $invoice_desc, $matches)) {

                         if(isset($invoice->orderReference)) {
                             $broj_rezervacije = $invoice->orderReference;
                         } else {
                             $broj_rezervacije = $matches[1];
                         }

                         $this->delete_all_payments($documentID);

                         foreach ($invoice->PaymentRecords as $paymentRecord) {

                             $this->wpdb->insert(
                                 $this->payments_table,
                                 array(
                                     'reservation_id' => $broj_rezervacije,
                                     'documentID' => $documentID,
                                     'invoice_number' => $invoice->number ? 'Račun br. ' . $invoice->number : NULL,
                                     'paymentDate' => $paymentRecord->paymentDate,
                                     'paymentAmount' => number_format($paymentRecord->paymentAmount, 2, '.', ''),
                                     'methodOfPayment' => $paymentRecord->methodOfPayment,
                                 )
                             );

                             $ukupno_uplata++;

                         }

                         $ukupno_rezervacija++;

                    }

                 }

            }

            if(!$autoupdate) {

                if($cron) {

                    $file = DC_REZERVACIJE_PATH . 'cron_output.txt';
                    $current_time = 'Pronađeno ukupno ' . $ukupno_uplata . ' novih uplata za ' . $ukupno_rezervacija . ' rezervacija od zadnjeg učitavanja koje je bilo ' . date('d.m.Y. H:i:s', strtotime($dc_settings->zadnje_osvjezeni_eracuni)) . "\n";
                    file_put_contents($file, $current_time, FILE_APPEND);

                } else {
                    $_SESSION['dc_admin_success'] = 'Uspješno osvježeno. Pronađeno ukupno ' . $ukupno_uplata . ' novih uplata za ' . $ukupno_rezervacija . ' rezervacija od zadnjeg učitavanja koje je bilo ' . date('d.m.Y. H:i:s', strtotime($dc_settings->zadnje_osvjezeni_eracuni));
                }

            }


        } else {

            if(!$autoupdate) {

                if ($cron) {

                    $file = DC_REZERVACIJE_PATH . 'cron_output.txt';
                    $current_time = 'Nema novih uplata. - ' . wp_date('Y-m-d H:i:s') . "\n";
                    file_put_contents($file, $current_time, FILE_APPEND);

                } else {
                    $_SESSION['dc_admin_success'] = 'Nema novih uplata od zadnjeg učitavanja koje je bilo ' . date('d.m.Y. H:i:s', strtotime($dc_settings->zadnje_osvjezeni_eracuni));
                }

            }

        }

        $settings_data = array(
            "zadnje_osvjezeni_eracuni" => wp_date('Y-m-d H:i:s')
        );
        $update_settings = $this->wpdb->update($this->postavke_table, $settings_data, array('id' => 1));

    }

    public function delete_all_payments($documentID)
    {

        $this->wpdb->query(
            $this->wpdb->prepare(
                "DELETE FROM $this->payments_table WHERE documentID = %s",
                $documentID
            )
        );

    }

    public function get_reservation_total_paid($reservation_id)
    {

        $sum_payment = $this->wpdb->get_var( $this->wpdb->prepare(
            "SELECT SUM(paymentAmount) FROM $this->payments_table WHERE reservation_id = %d",
            $reservation_id
        ) );

        return $sum_payment;

    }

}
