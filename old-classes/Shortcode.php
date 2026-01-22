<?php

class Shortcode
{
    private $wpdb;
    private $rezervacije_table;
    private $id_uplate_table;
    private $skole_table;
    private $razredi_table;
    private $putovanja_table;
    private $putnici_table;
    private $postavke_table;
    private $offers_table;
    private $connection_table;
    public $dc_settings;

    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
        $this->rezervacije_table = $this->wpdb->prefix . 'dcr_rezervacije';
        $this->id_uplate_table = $this->wpdb->prefix . 'dcr_id_uplate';
        $this->skole_table = $this->wpdb->prefix . 'dcr_skole';
        $this->razredi_table = $this->wpdb->prefix . 'dcr_razredi';
        $this->putovanja_table = $this->wpdb->prefix . 'dcr_putovanja';
        $this->putnici_table = $this->wpdb->prefix . 'dcr_putnici';
        $this->postavke_table = $this->wpdb->prefix . 'dcr_postavke';
        $this->connection_table = $this->wpdb->prefix . 'dcr_connection';
        $this->offers_table = $this->wpdb->prefix . 'dcr_offers';

        $dc_settings = $this->wpdb->get_row("SELECT * FROM {$this->postavke_table}");
        $this->dc_settings = $dc_settings;

    }

    public function store_ostala_putovanja($post, $session, $putovanje)
    {
        $dc_first_step_data = $_SESSION['dc_first_step_data'];
        $admin = new Admin();
        $currentDate = current_time('mysql');
        $postoji_duplikat_rezervacija = false;

        $query_ugovaratelj = $this->wpdb->prepare("SELECT id FROM {$this->putnici_table} WHERE email = %s  AND tip = %s", $dc_first_step_data['email_glavni'], 'ugovaratelj');
        $result_ugovaratelj = $this->wpdb->get_var($query_ugovaratelj);

        if (!empty($result_ugovaratelj)) {

            $admin->store_logs_data_new('a_rezervacija', $dc_first_step_data['putovanje_id'], 'Rezultat provjere u putnika po oibu: ' . json_decode($result_ugovaratelj, true));

            // već postoji korisnik
            $novi_ugovaratelj_id = $result_ugovaratelj;

            // provjeri je li u zadnjih 24 sata registriran putnik za isto putovanje - pratimo broj putnika i broj putovanja
            $query_duplicate_entry = $this->wpdb->prepare(
                "SELECT rezervacija.id, rezervacija.broj_eracuni_ponude, rezervacija.created_at
                         FROM {$this->rezervacije_table} AS rezervacija
                         JOIN {$this->putovanja_table} AS putovanje ON rezervacija.putovanje_id = putovanje.id
                         JOIN {$this->putnici_table} AS putnik ON rezervacija.ugovaratelj_id = putnik.id
                         WHERE putovanje.id = %s 
                         AND putnik.id = %s
                         AND rezervacija.smece != 1
                         AND rezervacija.created_at >= NOW() - INTERVAL 1 DAY",
                $session['putovanje_id'],
                $novi_ugovaratelj_id
            );

            $admin->store_logs_data_new('a_rezervacija', $dc_first_step_data['putovanje_id'], 'Rezultat provjere u zadnja 24 sata: ' . json_decode($query_duplicate_entry, true));

            $result_duplicate_entry = $this->wpdb->get_row($query_duplicate_entry);

            if (!empty($result_duplicate_entry)) {

                $nova_rezervacija_id = $result_duplicate_entry->id;
                $broj_eracuni_ponude = $result_duplicate_entry->broj_eracuni_ponude;
                $_SESSION['eracuni_broj_ponude'] = $broj_eracuni_ponude;
                $eracuni_poziv_na_broj = '00 ' . $result_duplicate_entry->broj_eracuni_ponude;

                $postoji_duplikat_rezervacija = true;

                $admin->store_logs_data_new('rezervacija', $nova_rezervacija_id, 'Ponovljena rezervacija unutar 24 sata.');
                $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Ponovljena rezervacija unutar 24 sata. Prvi datum rezervacije: ' . $result_duplicate_entry->created_at);

            } else {
                $admin->store_logs_data_new('a_putovanje', $dc_first_step_data['putovanje_id'], 'Pronađen putnik ' . $novi_ugovaratelj_id . ' ali nije pronađena dupla rezervacija');
            }

        } else {

            // dodaj novog glavnog putnika
            $novi_ugovaratelj_data = array(
                'ime' => $dc_first_step_data['ime_glavni'],
                'prezime' => $dc_first_step_data['prezime_glavni'],
                'adresa' => $dc_first_step_data['adresa_glavni'],
                'pb' => $dc_first_step_data['postanski_glavni'],
                'mjesto' => $dc_first_step_data['mjesto_glavni'],
                'rodendan' => $dc_first_step_data['datum_glavni'],
                'spol' => $dc_first_step_data['spol_glavni'],
                'kontakt' => $dc_first_step_data['kontakt_glavni'],
                'created_at' => $currentDate,
                'tip' => 'ugovaratelj'
            );

            $novi_ugovaratelj = $this->wpdb->insert($this->putnici_table, $novi_ugovaratelj_data);
            $novi_ugovaratelj_id = $this->wpdb->insert_id;

        }

        if($postoji_duplikat_rezervacija === false) {

            $novi_dodatni_putnik_id = null;

            if(!empty($dc_first_step_data['ime_dodatni'])) {

                $dodatni_putnik_data = array(
                    'ime' => $dc_first_step_data['ime_dodatni'],
                    'prezime' => $dc_first_step_data['prezime_dodatni'],
                    'rodendan' => $dc_first_step_data['rodenje_dodatni'],
                    'created_at' => $currentDate,
                    'tip' => 'putnik'
                );

                $this->wpdb->insert($this->putnici_table, $dodatni_putnik_data);
                $novi_dodatni_putnik_id = $this->wpdb->insert_id;

            }

            // spremi novu rezervaciju
            $rezervacija_data = array(
                'putovanje_id' => $session['putovanje_id'],
                'ugovaratelj_id' => $novi_ugovaratelj_id,
                'putnik_id' => $novi_dodatni_putnik_id,
                'informacije' => $dc_first_step_data['informacije'],
                'nacin_placanja' => $_POST['nacin_placanja'],
                'status' => 1,
                'created_at' => $currentDate
            );

            $nova_rezervacija = $this->wpdb->insert($this->rezervacije_table, $rezervacija_data);
            $nova_rezervacija_id = $this->wpdb->insert_id;

        }

        $all_participants = $this->prepare_participants($dc_first_step_data);
        $parameters = $this->prepare_parameters_ostala($nova_rezervacija_id, $putovanje, $dc_first_step_data, $all_participants);

        $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'params - ' . json_encode($parameters));

        // ako ne postoji duplikat rezervacije onda se dodaje putnik na eračune
        if($postoji_duplikat_rezervacija === false) {

            $admin->dominant_core_api($parameters, 'uTA');

        }
        // ako ne postoji duplikat rezervacije onda se dodaje putnik na eračune kraj

        $this->finish_booking($putovanje, $nova_rezervacija_id, $postoji_duplikat_rezervacija, $dc_first_step_data, $eracuni_poziv_na_broj ?? null, $parameters);
        exit();

    }

    public function finish_booking($putovanje, $nova_rezervacija_id, $postoji_duplikat_rezervacija, $dc_first_step_data, $eracuni_poziv_na_broj, $parameters)
    {

        $admin = new Admin();

        $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, '$_SESSION[dc_first_step_data]: ' . json_encode($dc_first_step_data));

        // generiraj ugovor
        $attachments = array();
        $potpis_image_url = wp_get_attachment_image_src($this->dc_settings->slika_potpisa_id, 'full');

        if($putovanje->type == 'skolska') {
            include_once(DC_REZERVACIJE_PATH . 'views/admin/ugovor-uplatnica/ugovor_pdf.php');
        } else {
            include_once(DC_REZERVACIJE_PATH . 'views/admin/ugovor-uplatnica/ugovor_pdf_ostala.php');
        }

        $_SESSION['ugovor_file'] = $temp_filename; // preuzima se ugovor iz tmp file-a u ugovor_pdf.php

        // dodaj ugovor u attachment
        $attachments['Ugovor o putovanju ' . $nova_rezervacija_id . '-' . $putovanje->sifra . '.pdf'] = $temp_filename;
        //$admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Definiran ugovor za slanje: Ugovor o putovanju ' . $nova_rezervacija_id . '-' . $putovanje->sifra . '.pdf');

        // dodaj plan putovanja u attachment
        if (!empty($putovanje->id_plana_putovanja)) {
            $pdf_url = wp_get_attachment_url($putovanje->id_plana_putovanja);
            if ($pdf_url) {
                $file_path = str_replace(site_url(), ABSPATH, $pdf_url);
                $attachments['Plan putovanja - ' . $putovanje->sifra . '.pdf'] = $file_path;

                //$admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Definiran plan putovanja za slanje o putovanju: Plan putovanja - ' . $putovanje->sifra . '.pdf');
            }
        }

        if($_POST['nacin_placanja'] == 4) {

            // iznos putovanja
            $iznos_za_uplatu = $putovanje->ukupni_iznos;
            $parameters['methodOfPayment'] = 'BankTransfer';

        } else if($_POST['nacin_placanja'] == 3) {

            // ukupni iznos
            /*
             * 18.10.2025. - promijenjeno da ako se odabere kartica, cijena je uvijek kartična
             * $iznos_za_uplatu = $putovanje->ukupni_iznos;
             */

            $iznos_za_uplatu = $putovanje->ukupni_iznos_kartica;
            $parameters['methodOfPayment'] = 'CreditCard';

        } else if($_POST['nacin_placanja'] == 5) {

            // ukupni iznos kartica
            $iznos_za_uplatu = $putovanje->ukupni_iznos_kartica;
            $parameters['methodOfPayment'] = 'CreditCard';

        } else {

            // samo akontacija
            $iznos_za_uplatu = $putovanje->akontacija;
            // $parameters['methodOfPayment'] = 'CreditCard';  // uklonjeno 21.10.2025. i postavljen bank transfer
            $parameters['methodOfPayment'] = 'BankTransfer';

        }

        $_SESSION['iznos_za_uplatu'] = $iznos_za_uplatu;

        // generiranje ponude
        $parameters['iznos_za_uplatu'] = $iznos_za_uplatu;

        // ako ne postoji duplikat rezervacije, kreiraj novu ponudu i preuzmi pdf za slanje
        if($postoji_duplikat_rezervacija === false) {

            $offer = $admin->dominant_core_api($parameters, 'cOF');
            $var_eracuni_broj_ponude = $offer->response->result->number;
            $_SESSION['eracuni_broj_ponude'] = $var_eracuni_broj_ponude;

            $store_offer_id = $this->wpdb->insert($this->offers_table, array('reservation_id' => $nova_rezervacija_id, 'eracuni_offer_id' => $var_eracuni_broj_ponude));

            $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Kreirana nova ponuda: Ponuda br. ' . $var_eracuni_broj_ponude . ' New Offer Response: ' . json_encode($offer));

            // get pdf and save it to server
            $parameters['document_number'] = $var_eracuni_broj_ponude;
            $get_pdf_tmp_file = $admin->dominant_core_api($parameters, 'gOF');

            $pdfContent = base64_decode($get_pdf_tmp_file->response->result->pdfFile);
            $tmpFilePath = tempnam(sys_get_temp_dir(), 'eracun_') . '.pdf';
            file_put_contents($tmpFilePath, $pdfContent);
            // get pdf and save it to server end

            $var_naziv_ponude = 'Ponuda br. ' . $var_eracuni_broj_ponude;

            // spremi broj ponude u rezervaciju za update
            $update_rez_data = array(
                'broj_eracuni_ponude' =>  $var_eracuni_broj_ponude
            );
            $where_rez_data = array('id' => $nova_rezervacija_id);

            $updated = $this->wpdb->update($this->rezervacije_table, $update_rez_data, $where_rez_data);
            // spremi broj ponude u rezervaciju za update end

        } else {

            //$parameters['document_number'] = $broj_eracuni_ponude;

            $get_offer_number = $this->wpdb->prepare(
                "SELECT *
                         FROM {$this->rezervacije_table}
                         WHERE id = %s",
                $nova_rezervacija_id
            );

            $offer_number_2 = $this->wpdb->get_row($get_offer_number);

            $broj_eracuni_ponude = $_SESSION['eracuni_broj_ponude'] ?? $offer_number_2->broj_eracuni_ponude;
            $parameters['document_number'] =  $broj_eracuni_ponude;

            // update rezervaciju i ponudu
            $rezervacija_data = array(
                'razred_id' => $dc_first_step_data['razred_id'],
                'informacije' => $dc_first_step_data['informacije'],
                'nacin_placanja' => $_POST['nacin_placanja']
            );
            $where_rezer_data = array('id' => $nova_rezervacija_id);
            $update_rezervacija = $this->wpdb->update($this->rezervacije_table, $rezervacija_data, $where_rezer_data);

            $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Napravljen update ponude: Ponuda br. ' . $broj_eracuni_ponude);

            $updated_offer = $admin->dominant_core_api($parameters, 'uOF');

            $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Napravljen update ponude na eračunima: ' . json_encode($updated_offer));
            // update rezervaciju i ponudu kraj

            // ako postoji duplikat rezervacije, preuzmi postojeću ponudu i ponovno ju spremi za slanje
            $get_pdf_tmp_file = $admin->dominant_core_api($parameters, 'gOF');
            $pdfContent = base64_decode($get_pdf_tmp_file->response->result->pdfFile);
            $tmpFilePath = tempnam(sys_get_temp_dir(), 'eponuda_') . '.pdf';
            file_put_contents($tmpFilePath, $pdfContent);

            $var_naziv_ponude = 'Ponuda br. ' . $broj_eracuni_ponude;
            $var_eracuni_broj_ponude = $broj_eracuni_ponude;

        }

        $_SESSION['naziv_ponude'] = $var_naziv_ponude;
        $_SESSION['link_ponude'] = $tmpFilePath;

        if (file_exists($tmpFilePath) && filesize($tmpFilePath) > 0) {
            $attachments[$_SESSION['naziv_ponude'] . '.pdf'] = $tmpFilePath;
        }

        // generiraj kod za uplatu PDF417
        $i_ugovaratelja = $dc_first_step_data['ime_ugovaratelja'];
        $p_ugovaratelja = $dc_first_step_data['prezime_ugovaratelja'];
        $ime_ugovaratelja = $i_ugovaratelja . ' ' . $p_ugovaratelja;
        $adresa_ugovaratelja = $dc_first_step_data['adresa_putnika'];
        $postanski_broj = $dc_first_step_data['postanski_putnika'];
        $mjesto = $dc_first_step_data['mjesto_putnika'];
        $sifra_putovanja = $putovanje->sifra;
        $putovanje_naziv = $putovanje->naziv;
        $poziv_na_broj = '00 ' . $get_pdf_tmp_file->response->result->number ?? $eracuni_poziv_na_broj;
        $_SESSION['poziv_na_broj'] = $poziv_na_broj;

        include_once(DC_REZERVACIJE_PATH . 'views/admin/ugovor-uplatnica/uplatnica_img.php');

        // resetiraj i preusmjeri
        if(/*$_POST['nacin_placanja'] == 1 || */$_POST['nacin_placanja'] == 3 || $_POST['nacin_placanja'] == 5) {

            $_SESSION['rezervacija_id'] = $nova_rezervacija_id;
            $kontakt_ugovaratelja = $dc_first_step_data['kontakt_ugovaratelja'];
            $email_ugovaratelja = $dc_first_step_data['email_ugovaratelja'] ?? $dc_first_step_data['email_glavni'];
            $broj_rata = $_POST['dc_rate'];
            $naziv_kartice = $_POST['dc_kartica']; // AMEX • DINERS  • DINA • DISCOVER  • MASTERCARD  • MAESTRO

            $dc_response = $admin->dominant_core_api(array(
                'licence' => $this->dc_settings->licenca,
                'ws_pay_test' => $this->dc_settings->ws_pay_test,
                'i_ugovaratelja' => $i_ugovaratelja,
                'p_ugovaratelja' => $p_ugovaratelja,
                'kontakt_ugovaratelja' => $kontakt_ugovaratelja,
                'email_ugovaratelja' => $email_ugovaratelja,
                'adresa_ugovaratelja' => $adresa_ugovaratelja,
                'postanski_broj' => $postanski_broj,
                'mjesto' => $mjesto,
                'iznos_za_uplatu' => $iznos_za_uplatu,
                'broj_rata' => $broj_rata,
                'naziv_kartice' => $naziv_kartice,
                'stranica_naplate' => get_permalink($this->dc_settings->stranica_naplate),
                'secret_key' => $this->dc_settings->ws_pay_secret,
                'id_rezervacije' => $nova_rezervacija_id
            ), 'wspay');

            $admin->store_logs_data_new('rezervacija', $nova_rezervacija_id, 'Započeta uplata putovanja putem WsPay-a. Iznos za uplatu: ' . $iznos_za_uplatu . ' €.');
            $admin->store_logs_data_new('a_rezervacija', $nova_rezervacija_id, 'Započeta uplata putovanja putem WsPay-a. Iznos za uplatu: ' . $iznos_za_uplatu . ' €.');

            echo $dc_response->message;
            exit();

        } else {

            // pošalji mail administratoru
            $subject = 'Nova rezervacija #' . $nova_rezervacija_id;
            $email_data = array(
                'reservation_id' => $nova_rezervacija_id,
                'putovanje_naziv' => $putovanje_naziv,
                'za_uplatiti' => $iznos_za_uplatu,
                'status_uplate' => 'na_cekanju',
                'poziv_na_broj' => $poziv_na_broj,
                //'broj_rezervacije' => $broj_rezervacije,
                'broj_rezervacije' => $nova_rezervacija_id,
                'uplatni_link' => get_permalink($this->dc_settings->stranica_naplate) . '/?rid=' . $nova_rezervacija_id
            );

            // dc_send_mail($to = 'info@dominant-core.hr', $subject = 'Naslov', $template = 'default', $attachments = null, $is_html = true / false, $copy_mail)
            $this->dc_send_mail(
                $this->dc_settings->admin_mail,
                //'domagoj.rogosic1@gmail.com',
                $subject,
                'admin/reservation_success',
                $email_data,
                $attachments
            );

            // ako nije odabrana 4. opcija naplate nemoj slati kupcu ništa = OSOBNI DOLAZAK U POSLOVNICU
            //if($_POST['nacin_placanja'] != 4) {

            $email_ugovaratelja = $dc_first_step_data['email_ugovaratelja'] ?? $dc_first_step_data['email_glavni'];

            // pošalji mail korisniku
            $this->dc_send_mail(
                $email_ugovaratelja,
                $subject,
                'user/reservation_success',
                $email_data,
                $attachments
            );

            // }

            $_SESSION['dc_success_reservation'] = array(
                'reservation_id' => $nova_rezervacija_id,
                'id_rezervacije' => $nova_rezervacija_id,
                'akontacija' => $iznos_za_uplatu,
                'kontakt_email' => $email_ugovaratelja,
                'oib_putnika' => $dc_first_step_data['oib_putnika'],
                'nacin_placanja' => $_POST['nacin_placanja']
            );

            wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
            exit();

        }

    }

    public function handle_dc_form_submit() {
        if(isset($_POST['new_reservation'])) {

            $putovanje_id = (int) $_POST['putovanje_id'];
            $query_putovanje = $this->wpdb->prepare("SELECT type FROM {$this->putovanja_table} WHERE id = %d LIMIT 1", $putovanje_id);
            $putovanje = $this->wpdb->get_row($query_putovanje);

            if ($putovanje && $putovanje->type == 'skolska') {

                $query_putnik = $this->wpdb->prepare(
                    "SELECT id FROM {$this->putnici_table}
                     WHERE oib = %s
                       AND tip = 'putnik'
                     LIMIT 1",
                    $_POST['oib_putnika']
                );

                $postojeci_putnik_id = (int) $this->wpdb->get_var($query_putnik);

                if ($postojeci_putnik_id > 0) {
                    $duplikat_rez = (int) $this->wpdb->get_var(
                        $this->wpdb->prepare(
                            "SELECT id
                             FROM {$this->rezervacije_table}
                             WHERE putovanje_id = %d
                               AND putnik_id    = %d
                               AND smece != 1
                             LIMIT 1",
                            $putovanje_id,
                            $postojeci_putnik_id
                        )
                    );

                    if ($duplikat_rez > 0) {
                        $admin = new Admin();

                        $admin->store_logs_data_new(
                            'a_rezervacija_duplikat',
                            $duplikat_rez,
                            'Pokušaj duple prijave (rani check, new_reservation) za putnika ' . $postojeci_putnik_id . ' na putovanje ' . $putovanje_id
                        );

                        $_SESSION['dc_danger'] = 'Već ste prijavili ovog učenika na odabrano putovanje. Ako mislite da je greška, kontaktirajte agenciju.';
                        wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                        exit();
                    }
                }
            }

            // step 2 store to session
            $_SESSION['dc_first_step_data'] = $_POST;
            wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije) . '/?korak=3');
            exit();

        } else if(isset($_POST['store_new_reservation'])) {

            $currentDate = current_time('mysql');
            $dc_first_step_data = $_SESSION['dc_first_step_data'];
            $postoji_duplikat_rezervacija = false;

            // provjeri postoji li slobodnih mjesta prije inserta
            $admin = new Admin();
            $ukupno_mjesta = $admin->dc_get_ukupno_mjesta($dc_first_step_data['putovanje_id']);
            $zauzeto_mjesta = $admin->dc_count_zauzeto_mjesta($dc_first_step_data['putovanje_id']);
            $zauzeto_mjesta = $zauzeto_mjesta['zauzeta_mjesta'] + $zauzeto_mjesta['rezervirana_mjesta'];

            if($ukupno_mjesta > $zauzeto_mjesta) {

                $query = $this->wpdb->prepare("SELECT * FROM {$this->putovanja_table} WHERE id = %d", $dc_first_step_data['putovanje_id']);
                $putovanje = $this->wpdb->get_row($query);

                if($putovanje->type == 'ostala') {

                    $this->store_ostala_putovanja($_POST, $_SESSION, $putovanje);
                    exit();

                } else {

                    $query_putnik = $this->wpdb->prepare("SELECT id FROM {$this->putnici_table} WHERE oib_putnika = %s  AND tip = 'putnik'", $dc_first_step_data['oib_putnika']);
                    $result_putnik = $this->wpdb->get_var($query_putnik);

                    if (!empty($result_putnik)) {

                        $admin->store_logs_data_new('a_rezervacija', $dc_first_step_data['putovanje_id'], 'Rezultat provjere u putnika po oibu: ' . json_decode($result_putnik, true));

                        // već postoji korisnik
                        $novi_putnik_id = $result_putnik;

                    } else {

                        // dodaj novog putnika
                        $putnik_data = array(
                            'ime' => $dc_first_step_data['ime_putnika'],
                            'prezime' => $dc_first_step_data['prezime_putnika'],
                            'adresa' => $dc_first_step_data['adresa_putnika'],
                            'pb' => $dc_first_step_data['postanski_putnika'],
                            'mjesto' => $dc_first_step_data['mjesto_putnika'],
                            'oib_putnika' => $dc_first_step_data['oib_putnika'],
                            'rodendan' => $dc_first_step_data['rodenje_putnika'],
                            'spol' => $dc_first_step_data['spol_putnika'],
                            'vrsta_isprave' => $dc_first_step_data['vrsta_isprave_putnika'],
                            'broj_isprave' => $dc_first_step_data['broj_isprave_putnika'],
                            'broj_zdravstvene' => $dc_first_step_data['broj_zdravstvene_putnika'],
                            'isprava_vrijedi' => $dc_first_step_data['putna_vrijedi_do_putnika'],
                            'kontakt' => $dc_first_step_data['kontakt_putnika'],
                            'created_at' => $currentDate,
                            'tip' => 'putnik'
                        );

                        $novi_putnik = $this->wpdb->insert($this->putnici_table, $putnik_data);
                        $novi_putnik_id = $this->wpdb->insert_id;

                    }

                    $putovanje_id = (int) $dc_first_step_data['putovanje_id'];
                    $novi_putnik_id = (int) $novi_putnik_id;

                    $duplikat_rez = (int) $this->wpdb->get_var(
                        $this->wpdb->prepare(
                            "SELECT id
                             FROM {$this->rezervacije_table}
                             WHERE putovanje_id = %d
                               AND putnik_id    = %d
                               AND smece != 1
                             LIMIT 1",
                            $putovanje_id,
                            $novi_putnik_id
                        )
                    );

                    if ($duplikat_rez > 0) {

                        $admin->store_logs_data_new(
                            'a_rezervacija_duplikat',
                            $duplikat_rez,
                            'Pokušaj duple prijave (školska) za putnika ' . $novi_putnik_id . ' na putovanje ' . $putovanje_id
                        );

                        $_SESSION['dc_danger'] = 'Već ste prijavili ovog učenika na odabrano putovanje. Ako mislite da je greška, kontaktirajte agenciju.';

                        wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                        exit();

                    }

                    if($postoji_duplikat_rezervacija === false) {
                        $query_email_ugovaratelj = $this->wpdb->prepare("SELECT id FROM {$this->putnici_table} WHERE email = %s  AND tip = 'ugovaratelj'", $dc_first_step_data['email_ugovaratelja']);
                        $result_email_ugovaratelj = $this->wpdb->get_var($query_email_ugovaratelj);

                        if ($result_email_ugovaratelj > 0) {

                            $novi_ugovaratelj_id = $result_email_ugovaratelj;

                        } else {

                            $ugovaratelj_data = array(
                                'ime' => $dc_first_step_data['ime_ugovaratelja'],
                                'prezime' => $dc_first_step_data['prezime_ugovaratelja'],
                                'kontakt' => $dc_first_step_data['kontakt_ugovaratelja'],
                                'email' => $dc_first_step_data['email_ugovaratelja'],
                                'created_at' => $currentDate,
                                'tip' => 'ugovaratelj'
                            );

                            $novi_ugovaratelj = $this->wpdb->insert($this->putnici_table, $ugovaratelj_data);
                            $novi_ugovaratelj_id = $this->wpdb->insert_id;

                        }

                        // spremi novu rezervaciju
                        //$broj_rezervacije = $this->generateUniqueReservationID();
                        $rezervacija_data = array(
                            'putovanje_id' => $dc_first_step_data['putovanje_id'],
                            'skola_id' => $dc_first_step_data['skola_id'],
                            'razred_id' => $dc_first_step_data['razred_id'],
                            'ugovaratelj_id' => $novi_ugovaratelj_id,
                            'putnik_id' => $novi_putnik_id,
                            'informacije' => $dc_first_step_data['informacije'],
                            'nacin_placanja' => $_POST['nacin_placanja'],
                            'status' => 1,
                            //'broj_rezervacije' => $broj_rezervacije,
                            'created_at' => $currentDate
                        );

                        $nova_rezervacija = $this->wpdb->insert($this->rezervacije_table, $rezervacija_data);

                        if ($nova_rezervacija === false) {
                            $last_error = $this->wpdb->last_error;

                            // 1) DUPLICATE ENTRY – UNIQUE (putovanje_id, putnik_id)
                            if (strpos($last_error, 'uniq_putovanje_putnik') !== false || strpos($last_error, 'Duplicate entry') !== false) {

                                $existing = $this->wpdb->get_row(
                                    $this->wpdb->prepare(
                                        "SELECT id, broj_eracuni_ponude 
                                         FROM {$this->rezervacije_table}
                                         WHERE putovanje_id = %d
                                           AND putnik_id    = %d
                                           AND smece != 1
                                         LIMIT 1",
                                        $dc_first_step_data['putovanje_id'],
                                        $novi_putnik_id
                                    )
                                );

                                if ($existing) {
                                    $nova_rezervacija_id = (int) $existing->id;

                                    $admin->store_logs_data_new(
                                        'a_rezervacija_duplikat',
                                        $nova_rezervacija_id,
                                        'MySQL UNIQUE duplicate (putovanje_id, putnik_id) – vraćam postojeću rezervaciju.'
                                    );

                                    $_SESSION['dc_danger'] = 'Već postoji prijava za ovo školsko putovanje za odabrano dijete.';
                                    wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                                    exit();

                                } else {
                                    $admin->store_logs_data_new(
                                        'a_rezervacija_error',
                                        0,
                                        'Duplicate entry na UNIQUE, ali SELECT ne vraća rezervaciju. Error: ' . $last_error
                                    );

                                    $_SESSION['dc_danger'] = 'Došlo je do greške pri obradi rezervacije. Molimo, pokušajte ponovno ili kontaktirajte agenciju.';
                                    wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                                    exit();
                                }

                            } else {
                                // 2) neka druga DB greška
                                $admin->store_logs_data_new(
                                    'a_rezervacija_error',
                                    0,
                                    'Greška pri insertu rezervacije (školska): ' . $last_error
                                );

                                $_SESSION['dc_danger'] = 'Došlo je do tehničke greške pri pohrani rezervacije. Molimo, pokušajte ponovno ili kontaktirajte agenciju.';
                                wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                                exit();
                            }

                        } else {
                            $nova_rezervacija_id = (int) $this->wpdb->insert_id;

                            if ($nova_rezervacija_id <= 0) {
                                $admin->store_logs_data_new(
                                    'a_rezervacija_error',
                                    0,
                                    'Insert rezervacije vratio insert_id <= 0 (školska).'
                                );

                                $_SESSION['dc_danger'] = 'Došlo je do greške pri pohrani rezervacije. Molimo, pokušajte ponovno ili kontaktirajte agenciju.';
                                wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                                exit();
                            }
                        }

                    }

                    $all_participants = $this->prepare_participants($dc_first_step_data);
                    $parameters = $this->prepare_parameters($nova_rezervacija_id, $putovanje, $dc_first_step_data, $all_participants);

                    // ako ne postoji duplikat rezervacije onda se dodaje putnik na eračune
                    if($postoji_duplikat_rezervacija === false) {

                        $admin->dominant_core_api($parameters, 'uTA');

                    }
                    // ako ne postoji duplikat rezervacije onda se dodaje putnik na eračune kraj

                    $eracuni_numer = $eracuni_poziv_na_broj ?? null;
                    $this->finish_booking($putovanje, $nova_rezervacija_id, $postoji_duplikat_rezervacija, $dc_first_step_data, $eracuni_numer, $parameters);
                    exit();

                }

            } else {

                $this->dc_reset_session();
                $_SESSION['dc_danger'] = 'Trenutno nema slobodnih mjesta za odabrano putovanje. Molimo, kontaktirajte nas. Hvala.';
                wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                exit();

            }

        } else if ( isset($_POST['putovanje_id']) && ! empty($_POST['putovanje_id']) ) {

    $putovanje_id = (int) $_POST['putovanje_id'];
    $current_date = wp_date('Y-m-d');

    $query  = "SELECT id, broj_putnika
               FROM {$this->putovanja_table}
               WHERE id = %d AND status = %d AND putovanje_do >= %s";

    $result = $this->wpdb->get_row(
        $this->wpdb->prepare($query, $putovanje_id, 1, $current_date),
        OBJECT
    );

    if ( $result !== null ) {

        $admin          = new Admin();
        $zauzeto_mjesta = $admin->dc_count_zauzeto_mjesta( $result->id );
        $zauzeto_mjesta = $zauzeto_mjesta['zauzeta_mjesta'] + $zauzeto_mjesta['rezervirana_mjesta'];
        $slobodno_mjesta = $result->broj_putnika;

        if ( $slobodno_mjesta > $zauzeto_mjesta ) {

            $_SESSION['putovanje_id'] = $result->id;
            wp_redirect( get_permalink( $this->dc_settings->stranica_rezervacije ) . '/?korak=2' );
            exit;

        } else {

            $this->dc_reset_session();
            $_SESSION['dc_danger'] = 'Trenutno nema slobodnih mjesta za odabrano putovanje. Molimo, kontaktirajte nas. Hvala.';
            wp_redirect( get_permalink( $this->dc_settings->stranica_rezervacije ) );
            exit;
        }

    } else {

        $this->dc_reset_session();
        $_SESSION['dc_danger'] = 'Nismo pronašli aktivno putovanje za odabranu stavku. Molimo, kontaktirajte nas.';
        wp_redirect( get_permalink( $this->dc_settings->stranica_rezervacije ) );
        exit;
    }
            
        } else if(isset($_POST['sifra'])) {

            $current_date = wp_date('Y-m-d');
            $query = "SELECT id, broj_putnika FROM {$this->putovanja_table} WHERE sifra = %s AND status = %d AND putovanje_do >= %s";
            $result = $this->wpdb->get_row($this->wpdb->prepare($query, $_POST['sifra'], 1, $current_date), OBJECT);

            if($result !== null) {

                $admin = new Admin();
                $zauzeto_mjesta = $admin->dc_count_zauzeto_mjesta($result->id);
                $zauzeto_mjesta = $zauzeto_mjesta['zauzeta_mjesta'] + $zauzeto_mjesta['rezervirana_mjesta'];
                $slobodno_mjesta = $result->broj_putnika;

                if($slobodno_mjesta > $zauzeto_mjesta) {

                    $_SESSION['putovanje_id'] = $result->id;
                    wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije) . '/?korak=2');
                    exit();

                } else {

                    $this->dc_reset_session();
                    $_SESSION['dc_danger'] = 'Trenutno nema slobodnih mjesta za odabrano putovanje. Molimo, kontaktirajte nas. Hvala.';
                    wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                    exit();

                }

            } else {

                $this->dc_reset_session();
                $_SESSION['dc_danger'] = 'Nismo pronašli putovanje sa šifrom ' . $_POST['sifra'] . ' ili je već realizirano.';
                wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
                exit();

            }

        } else if(isset($_POST['amount'])) {

            $admin = new Admin();
            $id_rezervacije = $_POST['reservation_id'];
            $rezervirano_sati = $this->dc_settings->rezervirano_sati;
            $iznos_za_uplatu = $_POST['amount'];
            $_SESSION['iznos_za_uplatu'] = $iznos_za_uplatu;

            $query = "
                    SELECT 
                        rezervacija.*,
                        putovanje.status AS putovanje_status,
                        putovanje.sifra AS putovanje_sifra,
                        putovanje.naziv AS putovanje_naziv,
                        putovanje.ukupni_iznos as putovanje_iznos,
                        putovanje.ukupni_iznos_kartica as putovanje_iznos_kartica,
                        putovanje.eracuni_number AS eracuni_ta_booking_ref_number,
                        putnik.ime AS putnik_ime,
                        putnik.prezime AS putnik_prezime,
                        putnik.adresa AS putnik_adresa,
                        putnik.pb AS putnik_pb,
                        putnik.mjesto AS putnik_mjesto,
                        ugovaratelj.ime AS ugovaratelj_ime,
                        ugovaratelj.prezime AS ugovaratelj_prezime,
                        ugovaratelj.email AS ugovaratelj_email,
                        ugovaratelj.kontakt AS ugovaratelj_kontakt
                    FROM 
                        {$this->rezervacije_table} AS rezervacija
                    JOIN 
                        {$this->putnici_table} AS ugovaratelj ON rezervacija.ugovaratelj_id = ugovaratelj.id
                    JOIN 
                        {$this->putnici_table} AS putnik ON rezervacija.putnik_id = putnik.id
                    JOIN 
                        {$this->putovanja_table} AS putovanje ON rezervacija.putovanje_id = putovanje.id
                    WHERE 
                        rezervacija.id = %d 
                        AND rezervacija.smece = %d
                    LIMIT 1
                ";

            $reservation = $this->wpdb->get_row($this->wpdb->prepare($query, $id_rezervacije, 0));
            $_SESSION['rezervacija_id'] = $reservation->id;

            if($reservation) {

                if($reservation->putovanje_status > 0) {

                    $current_time = current_time('mysql');

                    if($reservation->status > 3 || $reservation->created_at >= date('Y-m-d H:i:s', strtotime('-' . $rezervirano_sati . 'HOURS', strtotime($current_time)))) {

                        $count_query = $this->wpdb->prepare("
                            SELECT COUNT(*) AS total
                            FROM 
                                {$this->id_uplate_table} AS id_uplate
                            WHERE 
                                id_uplate.reservation_id = %s
                        ", $reservation->id);

                        $total_results_res = $this->wpdb->get_var($count_query);
                        $next_number = $total_results_res + 1;

                        $nova_uplata_data = array(
                            'reservation_id' => $reservation->id
                        );

                        $nova_uplata_id = $this->wpdb->insert($this->id_uplate_table, $nova_uplata_data);

                        $data = array(
                            'licence' => $this->dc_settings->licenca,
                            'md5pass' => $this->dc_settings->eracuni_password,
                            'token' => $this->dc_settings->eracuni_token,
                            'eracuni_broj_artikla' => $this->dc_settings->eracuni_broj_artikla,
                            'ime_ugovaratelja' => $reservation->ugovaratelj_ime,
                            'prezime_ugovaratelja' => $reservation->ugovaratelj_prezime,
                            'adresa_ugovaratelja' => $reservation->putnik_adresa,
                            'post_ugovaratelja' => $reservation->putnik_pb,
                            'mjesto_ugovaratelja' => $reservation->putnik_mjesto,
                            'number' => $reservation->eracuni_ta_booking_ref_number, //ta booking ref number
                            'naziv' => $reservation->putovanje_naziv,
                            'sifra' => $reservation->putovanje_sifra,
                            'ime_prezime_putnika' => $reservation->putnik_ime . ' ' . $reservation->putnik_prezime,
                            'iznos_za_uplatu' => $iznos_za_uplatu,
                            'ukupni_iznos_putovanja' => $reservation->nacin_placanja == 5 ? $reservation->putovanje_iznos_kartica : $reservation->putovanje_iznos,
                            'reservation_id' => $reservation->id,
                            'methodOfPayment' => 'CreditCard',
                            'eracuni_posl_prostor' => $this->dc_settings->eracuni_posl_prostor,
                        );

                        $offer = $admin->dominant_core_api($data, 'cOF');
                        $_SESSION['eracuni_broj_ponude'] = $offer->response->result->number;
                        $store_offer_id = $this->wpdb->insert($this->offers_table, array('reservation_id' => $reservation->id, 'eracuni_offer_id' => $_SESSION['eracuni_broj_ponude']));

                        $i_ugovaratelja = $reservation->ugovaratelj_ime;
                        $p_ugovaratelja = $reservation->ugovaratelj_prezime;
                        $nova_rezervacija_id = $reservation->id . ' - ' . $next_number;
                        $kontakt_ugovaratelja = $reservation->ugovaratelj_kontakt;
                        $email_ugovaratelja = $reservation->ugovaratelj_email;
                        $adresa_ugovaratelja = $reservation->putnik_adresa;
                        $postanski_broj = $reservation->putnik_pb;
                        $mjesto = $reservation->putnik_mjesto;
                        $broj_rata = 'undefined';

                        $dc_response = $admin->dominant_core_api(array(
                            'licence' => $this->dc_settings->licenca,
                            'ws_pay_test' => $this->dc_settings->ws_pay_test,
                            'i_ugovaratelja' => $i_ugovaratelja,
                            'p_ugovaratelja' => $p_ugovaratelja,
                            'kontakt_ugovaratelja' => $kontakt_ugovaratelja,
                            'email_ugovaratelja' => $email_ugovaratelja,
                            'adresa_ugovaratelja' => $adresa_ugovaratelja,
                            'postanski_broj' => $postanski_broj,
                            'mjesto' => $mjesto,
                            'iznos_za_uplatu' => $iznos_za_uplatu,
                            'broj_rata' => $broj_rata,
                            'stranica_naplate' => get_permalink($this->dc_settings->stranica_naplate),
                            'secret_key' => $this->dc_settings->ws_pay_secret,
                            'id_rezervacije' => $nova_rezervacija_id
                        ), 'wspay');

                        $admin->store_logs_data_new('rezervacija', $id_rezervacije, 'Započeta uplata putem naplatnog linka. Iznos za uplatu: ' . $iznos_za_uplatu . ' €');
                        $admin->store_logs_data_new('a_rezervacija', $id_rezervacije, 'Započeta uplata putem naplatnog linka. Iznos za uplatu: ' . $iznos_za_uplatu . ' €. Broj ponude: ' . $_SESSION['eracuni_broj_ponude'] .  ' e-racuni response: ' . json_encode($offer));

                        echo $dc_response->message;
                        exit();

                    } else {

                        // provjeri postoji li slobodnih mjesta prije reaktivacije putovanja
                        $ukupno_mjesta = $admin->dc_get_ukupno_mjesta($reservation->putovanje_id);
                        $zauzeto_mjesta = $admin->dc_count_zauzeto_mjesta($reservation->putovanje_id);
                        $zauzeto_mjesta = $zauzeto_mjesta['zauzeta_mjesta'] + $zauzeto_mjesta['rezervirana_mjesta'];

                        if($ukupno_mjesta > $zauzeto_mjesta) {

                            $admin->store_logs_data_new('rezervacija', $reservation->id, 'Izvršena reaktivacija. Prethodno rezervirano: ' . date('d.m.Y. H:i:s', strtotime($reservation->created_at)));

                            // reaktiviraj putovanje
                            $current_time = current_time('mysql');
                            $this->wpdb->query(
                                $this->wpdb->prepare(
                                    "UPDATE {$this->rezervacije_table} SET created_at = %s WHERE id = %d",
                                    $current_time,
                                    $reservation->id
                                )
                            );

                            // ponovno pokreni plaćanje
                            echo '<form style="display: none;" id="auto-submit-form" method="POST" action="' . get_permalink($this->dc_settings->stranica_naplate) . '">';
                            echo '<input type="hidden" name="reservation_id" value="' . $reservation->id . '">';
                            echo '<input type="hidden" name="amount" value="' . $iznos_za_uplatu . '">';
                            echo '</form>';

                            // automatski submit formu da korisnik ništa ne zna
                            echo '<script type="text/javascript">';
                            echo 'document.getElementById("auto-submit-form").submit();';
                            echo '</script>';
                            exit;

                        } else {

                            // ako nema više slobodnih mjesta prikaži grešku
                            $_SESSION['dc_danger'] = 'Rezervacija broj ' . $reservation->id . ' bila rezervirana ' . $rezervirano_sati . ' sata i trenutno je istekla. Trenutno nemamo slobodnih mjesta za ovo putovanje. Molimo da nas kontaktirate za više informacija. Hvala.';
                            wp_redirect(get_permalink($this->dc_settings->stranica_naplate));
                            exit();
                        }

                    }

                } else {

                    $_SESSION['dc_danger'] = 'Ovo putovanje nije aktivno i nije moguće vršiti uplatu. Molimo, kontaktirajte nas za dodatne informacije. Hvala.';
                    wp_redirect(get_permalink($this->dc_settings->stranica_naplate));
                    exit();

                }

            } else {

                $_SESSION['dc_danger'] = 'Broj rezervacije koji ste upisali nije pronađen. Pokušajte ponovno ili nas kontaktirajte.';
                wp_redirect(get_permalink($this->dc_settings->stranica_naplate));
                exit();

            }

        } else if(isset($_GET['act']) && $_GET['act'] == 'odustani') {

            $this->dc_reset_session();

            $_SESSION['dc_warning'] = 'Odustali ste od rezervacije.';
            wp_redirect(get_permalink($this->dc_settings->stranica_rezervacije));
            exit();

        } else if(isset($_GET['Success']) && $_GET['Success'] == 1 && isset($_GET['ApprovalCode'])) {

            if (isset($_SESSION['rezervacija_id']) && isset($_SESSION['iznos_za_uplatu'])) {

                $this->uplaceno_online();

            }
        } else if(isset($_GET['ErrorCodes']) && $_GET['ErrorCodes'] == 'E00012') {

            $broj_rezervacije = preg_replace('/\D/', '', $_GET['ShoppingCartID']);

            $_SESSION['dc_danger'] = 'Za rezervaciju broj ' . $broj_rezervacije . ' već postoje uplate. Ako želite uplatiti dodatni iznos, upišite ga ispod i nastavite sa plaćanjem.';
            wp_redirect(get_permalink($this->dc_settings->stranica_naplate) . '?rid=' . $broj_rezervacije);
            exit();

        }
    }

    public function prepare_participants($dc_first_step_data)
    {
        $participants_query = $this->wpdb->prepare(
            "SELECT putnici.* 
                            FROM {$this->putnici_table} AS putnici 
                            LEFT JOIN {$this->rezervacije_table} AS rezervacije ON putnici.id = rezervacije.putnik_id
                            WHERE rezervacije.putovanje_id = %d
                            GROUP BY putnici.oib_putnika",
            $dc_first_step_data['putovanje_id']
        );
        $participants = $this->wpdb->get_results($participants_query);
        $all_participants = array();
        foreach ($participants as $participant) {
            $all_participants[] = array(
                "position" => $participant->id,
                "surnameAndName" => $participant->ime . ' ' . $participant->prezime . ' (OIB: ' . $participant->oib_putnika . ')',
            );
        }

        return $all_participants;
    }

    public function prepare_parameters($nova_rezervacija_id, $putovanje, $dc_first_step_data, $all_participants)
    {
        // parametri se moraju definirati radi ugovora
        $parameters = array(
            'licence' => $this->dc_settings->licenca,
            'md5pass' => $this->dc_settings->eracuni_password,
            'token' => $this->dc_settings->eracuni_token,
            'eracuni_broj_artikla' => $this->dc_settings->eracuni_broj_artikla,
            'reservation_id' => $nova_rezervacija_id,
            'number' => $putovanje->eracuni_number,
            'naziv' => $putovanje->naziv,
            'sifra' => $putovanje->sifra,
            'ukupni_iznos_putovanja' => $_POST['ukupni_iznos_putovanja'],
            'ime_prezime_putnika' => $dc_first_step_data['ime_putnika'] . ' ' . $dc_first_step_data['prezime_putnika'],
            'ime_ugovaratelja' => $dc_first_step_data['ime_ugovaratelja'],
            'prezime_ugovaratelja' => $dc_first_step_data['prezime_ugovaratelja'],
            'kontakt' => $dc_first_step_data['kontakt_ugovaratelja'],
            'email' => $dc_first_step_data['email_ugovaratelja'],
            'adresa_ugovaratelja' => $dc_first_step_data['adresa_putnika'],
            'mjesto_ugovaratelja' => $dc_first_step_data['mjesto_putnika'],
            'post_ugovaratelja' => $dc_first_step_data['postanski_putnika'],
            'all_participants' => $all_participants ?? null,
            'dc_naziv_skole' => $dc_first_step_data['dc_naziv_skole'],
            'dc_razred' => $dc_first_step_data['dc_razred'],
            'text_ugovora' => $putovanje->text_ugovora,
            'eracuni_posl_prostor' => $this->dc_settings->eracuni_posl_prostor,
        );
        // parametri kraj

        return $parameters;
    }

    public function prepare_parameters_ostala($nova_rezervacija_id, $putovanje, $dc_first_step_data, $all_participants)
    {
        // parametri se moraju definirati radi ugovora
        $parameters = array(
            'licence' => $this->dc_settings->licenca,
            'md5pass' => $this->dc_settings->eracuni_password,
            'token' => $this->dc_settings->eracuni_token,
            'eracuni_broj_artikla' => $this->dc_settings->eracuni_broj_artikla,
            'reservation_id' => $nova_rezervacija_id,
            'number' => $putovanje->eracuni_number,
            'naziv' => $putovanje->naziv,
            'sifra' => $putovanje->sifra,
            'ukupni_iznos_putovanja' => $_POST['ukupni_iznos_putovanja'],
            'ime_prezime_putnika' => $dc_first_step_data['ime_glavni'] . ' ' . $dc_first_step_data['prezime_glavni'],
            'ime_ugovaratelja' => $dc_first_step_data['ime_glavni'],
            'prezime_ugovaratelja' => $dc_first_step_data['prezime_glavni'],
            'kontakt' => $dc_first_step_data['kontakt_glavni'],
            'email' => $dc_first_step_data['email_glavni'],
            'adresa_ugovaratelja' => $dc_first_step_data['adresa_glavni'],
            'mjesto_ugovaratelja' => $dc_first_step_data['mjesto_glavni'],
            'post_ugovaratelja' => $dc_first_step_data['postanski_glavni'],
            'all_participants' => $all_participants ?? null,
            /*'dc_naziv_skole' => $dc_first_step_data['dc_naziv_skole'],
            'dc_razred' => $dc_first_step_data['dc_razred'],*/
            'text_ugovora' => $putovanje->text_ugovora,
            'eracuni_posl_prostor' => $this->dc_settings->eracuni_posl_prostor,
        );
        // parametri kraj

        return $parameters;
    }

    function generateUniqueReservationID() {
        $done = false;
        while (!$done) {
            $unique_id = mt_rand(1000, 9999);

            $query = "SELECT COUNT(id) as count FROM {$this->rezervacije_table} WHERE broj_rezervacije = %s";
            $query = $this->wpdb->prepare($query, $unique_id);
            $result = $this->wpdb->get_row($query);

            if ($result && $result->count < 1) {
                $done = true;
            }
        }
        return $unique_id;
    }

    public function dc_reset_session()
    {
        // ukloni poslane dokumente
        /*if (isset($_SESSION['ugovor_file']) && file_exists($_SESSION['ugovor_file'])) {
            unlink($_SESSION['ugovor_file']);
        }

        if (isset($_SESSION['link_ponude']) && file_exists($_SESSION['link_ponude'])) {
            unlink($_SESSION['link_ponude']);

            $fileWithoutExtension = substr($_SESSION['link_ponude'], 0, -4);
            if (file_exists($fileWithoutExtension)) {
                unlink($fileWithoutExtension);
            }
        }
        // ukloni poslane dokumente kraj

        unset($_SESSION['dc_success_reservation']);
        unset($_SESSION['putovanje_id']);
        unset($_SESSION['ugovor_file']);
        unset($_SESSION['iznos_za_uplatu']);
        unset($_SESSION['dc_first_step_data']);
        unset($_SESSION['rezervacija_id']);
        unset($_SESSION['poziv_na_broj']);
        unset($_SESSION['naziv_ponude']);
        unset($_SESSION['link_ponude']);
        unset($_SESSION['eracuni_broj_ponude']);*/
    }

    public function uplaceno_online()
    {

        $admin = new Admin();
        $rezervacija = $admin->get_rezervacija($_SESSION['rezervacija_id']);

        $parameters = array (
            'licence' => $this->dc_settings->licenca,
            'md5pass' => $this->dc_settings->eracuni_password,
            'token' => $this->dc_settings->eracuni_token,
            'reservation_id' => $rezervacija->id,
            'ime_prezime_ugovaratelja' => $rezervacija->ugovaratelj_ime . ' ' . $rezervacija->ugovaratelj_prezime,
            'email_ugovaratelja' => $rezervacija->ugovaratelj_email,
            'kontakt_ugovaratelja' => $rezervacija->ugovaratelj_kontakt,
            'ime_prezime_putnika' => $rezervacija->putnik_ime . ' ' . $rezervacija->putnik_prezime,
            'naziv' => $rezervacija->putovanje_naziv,
            'iznos_za_uplatu' => $_SESSION['iznos_za_uplatu'],
            'sifra' => $rezervacija->putovanje_sifra,
            'eracuni_broj_ponude' => $_SESSION['eracuni_broj_ponude'] ?? $rezervacija->broj_eracuni_ponude,
            'ta_booking_number' => $rezervacija->putovanje_ta_booking_ref,
            'eracuni_posl_prostor' => $this->dc_settings->eracuni_posl_prostor,
            'eracuni_broj_artikla' => $this->dc_settings->eracuni_broj_artikla,
            'methodOfPayment' => 'CreditCard'
        );

        $admin->store_logs_data_new('a_rezervacija', $rezervacija->id, 'Parametri za slanje eracunima: ' . json_encode($parameters));
        $admin->store_logs_data_new('a_rezervacija', $rezervacija->id, 'WsPay prošao. WsPay info: ' . json_encode($_GET));

        //$uplata = $admin->dominant_core_api($parameters, 'oAP')->message; // dodavanje uplate na ponudu
        $uplata = $admin->dominant_core_api($parameters, 'oAP'); // dodavanje uplate na ponudu

        $admin->store_logs_data_new('a_rezervacija', $rezervacija->id, 'Dodana uplata na ponudu ' . $rezervacija->broj_eracuni_ponude . ' e-racuni Response: ' . json_encode($uplata));
        //$admin->dominant_core_api($parameters, 'taAP')->message; // dodavanje uplate na rezervaciju

        //$advance_invoice = $admin->dominant_core_api($parameters, 'cIn')->message;
        $advance_invoice = $admin->dominant_core_api($parameters, 'cIn');

        if($advance_invoice) {

            $admin->store_logs_data_new('a_rezervacija', $rezervacija->id, 'Dodan račun za ponudu ' . $rezervacija->broj_eracuni_ponude . ' e-racuni Response ' . json_encode($advance_invoice));

            $advance_invoice_obj = $advance_invoice;
            if($advance_invoice_obj->response->status == 'ok') {

                $eracuni_broj_racuna = $advance_invoice_obj->response->result->number;

                $tekst_placeno = 'Kartično plaćeno ' . $_SESSION['iznos_za_uplatu'] . ' € po računu broj ' . $eracuni_broj_racuna;
                $admin->store_logs_data_new('rezervacija', $rezervacija->id, $tekst_placeno);

                $rezervacija_data = array(
                    'status' => 4, // status 4 = placeno online
                    'updated_at' => current_time('mysql')
                );
                $where = array('id' => $_SESSION['rezervacija_id']);
                $updated = $this->wpdb->update($this->rezervacije_table, $rezervacija_data, $where);

                // preuzmi PDF račun
                // $_SESSION['pdf_racun_temp'] = $eracuni->get_pdf($eracuni_broj_racuna, 'AdvanceInvoiceGetPDF');

                // get pdf and save it to server
                $parameters['document_number'] = $eracuni_broj_racuna;
                $get_pdf_tmp_file = $admin->dominant_core_api($parameters, 'gIn');

                $pdfContent = base64_decode($get_pdf_tmp_file->response->result->pdfFile);
                $tmpFilePath = tempnam(sys_get_temp_dir(), 'eracun_') . '.pdf';
                file_put_contents($tmpFilePath, $pdfContent);

                $_SESSION['pdf_racun_temp'] = $tmpFilePath;
                // get pdf and save it to server end

                $email_data = array(
                    'putovanje_naziv' => $rezervacija->putovanje_naziv,
                    'iznos_uplate' => $_SESSION['iznos_za_uplatu'],
                    'broj_rezervacije' => $rezervacija->id,
                    'uplatni_link' => get_permalink($this->dc_settings->stranica_naplate) . '/?rid=' . $rezervacija->id
                );

                $attachments = array();
                $attachments['Račun br. ' . $eracuni_broj_racuna . '.pdf'] = $_SESSION['pdf_racun_temp'];
                if(isset($_SESSION['ugovor_file'])) {
                    $attachments['Ugovor o putovanju ' . $rezervacija->id . '-' . $rezervacija->putovanje_sifra . '.pdf'] = $_SESSION['ugovor_file'];
                }

                $query_putovanje = $this->wpdb->prepare("SELECT * FROM {$this->putovanja_table} WHERE id = %d", $rezervacija->putovanje_id);
                $putovanje_result = $this->wpdb->get_row($query_putovanje);

                // dodaj plan putovanja u attachment
                if (!empty($putovanje_result->id_plana_putovanja)) {
                    $pdf_url_putovanje = wp_get_attachment_url($putovanje_result->id_plana_putovanja);
                    if ($pdf_url_putovanje) {
                        $file_path_putovanje = str_replace(site_url(), ABSPATH, $pdf_url_putovanje);
                        $attachments['Plan putovanja - ' . $putovanje_result->sifra . '.pdf'] = $file_path_putovanje;
                    }
                }

                // mail administratoru
                $subject = 'Nova rezervacija #' . $rezervacija->id;
                $email_data_admin = array(
                    'reservation_id' => $rezervacija->id,
                    'putovanje_naziv' => $rezervacija->putovanje_naziv,
                    'za_uplatiti' => $_SESSION['iznos_za_uplatu'],
                    'status_uplate' => 'na_cekanju',
                    'poziv_na_broj' => $_SESSION['poziv_na_broj'],
                    //'broj_rezervacije' => $broj_rezervacije,
                    'broj_rezervacije' => $rezervacija->id,
                    'uplatni_link' => get_permalink($this->dc_settings->stranica_naplate) . '/?rid=' . $rezervacija->id
                );

                $this->dc_send_mail(
                    $this->dc_settings->admin_mail,
                    //'domagoj.rogosic1@gmail.com',
                    $subject,
                    'admin/reservation_success',
                    $email_data_admin,
                    $attachments
                );
                // mail administratoru end

                // mail ugovaratelju
                $this->dc_send_mail(
                    $rezervacija->ugovaratelj_email, // primatelj
                    'Vaša uplata je zaprimljena | eridan.hr', // naslov
                    'user/reservation_paid_online', // template
                    $email_data,
                    $attachments
                );
                // mail ugovaratelju end

                $_SESSION['dc_success'] = 'Uspješno ste izvršili uplatu u iznosu od ' . $_SESSION['iznos_za_uplatu'] . '€ za putovanje "' . $rezervacija->putovanje_naziv . '".<br>Hvala!';

                $this->dc_reset_session();

                wp_redirect(get_permalink($this->dc_settings->stranica_naplate));
                exit();
            }
        } else {
            $admin->store_logs_data_new('a_rezervacija', $rezervacija->id, 'NIJE DODAN račun za ponudu ' . $rezervacija->broj_eracuni_ponude . ' - ' . json_encode($advance_invoice));
        }
    }

    public function dc_send_mail($to = 'info@dominant-core.hr', $subject = 'Naslov', $template = 'default', $data = null, $attachments = null, $is_html = true)
    {

        $domain = parse_url(home_url());
        $domain = preg_replace('/^www\./', '', $domain);

        $headers = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . $this->dc_settings->admin_name . ' <rezervacije@' . $domain['host'] . '>',
            'Reply-To: ' . $this->dc_settings->admin_mail,
            'X-Mailer: PHP/' . phpversion(),
        );

        // Check if copy_name exists and is a valid email address
        if (!empty($this->dc_settings->copy_name) && is_email($this->dc_settings->copy_name)) {
            $headers[] = 'Cc: ' . $this->dc_settings->copy_name;
        }

        ob_start();
        if($is_html) {
            include(DC_REZERVACIJE_PATH . 'views/email-template/header.php');
        }

        include(DC_REZERVACIJE_PATH . 'views/email-template/' . $template . '.php');

        if($is_html) {
            include(DC_REZERVACIJE_PATH . 'views/email-template/footer.php');
        }

        $message = ob_get_clean();

        return wp_mail($to, $subject, $message, $headers, $attachments);
    }

}