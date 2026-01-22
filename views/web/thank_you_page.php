<?php
$admin = new Admin();
$dc_settings = $admin->get_dcr_settings();

$data = $_SESSION['dc_success_reservation'];

?>

<div class="dc-row">

    <?php if($data['nacin_placanja'] == 4) : ?>

        <div class="dc-col-6">
            <h3>Rezervacija je uspješno zaprimljena!</h3>
            <p>
                Vaša prijava je rezervirana naredna <?php echo $dc_settings->rezervirano_sati; ?> sata.<br><br>
                Ako trebate dodatne informacije možete nas kontaktirati putem email adrese <?php echo $dc_settings->admin_mail; ?>. <br>
                Radno vrijeme agencije je svaki radni dan od <b><?php echo $dc_settings->radno_vrijeme_od; ?></b> do <b><?php echo $dc_settings->radno_vrijeme_do; ?></b> sati.
            </p>
            <p>Hvala na poslanoj prijavi.</p>
        </div>

    <?php else : ?>

        <div class="dc-col-6">
            <h3>Rezervacija je uspješno zaprimljena!</h3>
            <p>Na mail ugovaratelja smo poslali sve detalje o prijavi na putovanje. <!--Uskoro će vas kontaktirati netko od zaposlenika agencije.--></p>
            <p>Hvala na poslanoj prijavi.</p>
        </div>
        <div class="dc-col-6">
            <h4>Podaci za uplatu</h4>
            <div class="dc-row">
                <div class="dc-col-6 dc-p-0">
                    <p>
                        Naziv tvrtke<br>
                        <b>
                            <?php echo $dc_settings->naziv_tvrtke; ?><br>
                            <?php echo $dc_settings->adresa_tvrtke; ?><br>
                            <?php echo $dc_settings->postanski_broj_tvrtke; ?> <?php echo $dc_settings->mjesto_tvrtke; ?>
                        </b>
                    </p>
                    <p>
                        Iznos akontacije za uplatu<br>
                        <b><?php echo $data['akontacija']; ?> €</b>
                    </p>
                    <p>
                        IBAN<br>
                        <b><?php echo $dc_settings->iban_tvrtke; ?><br><?php echo $dc_settings->banka_tvrtke; ?></b>
                    </p>
                    <p>
                        Model i poziv na broj primatelja<br>
                        <b>HR<?php echo $_SESSION['poziv_na_broj'] ?></b>
                    </p>
                </div>
                <div class="dc-col-6 dc-p-0">
                    <p>Skenirajte kod radi brže i jednostavnije uplate mobilnom aplikacijom.</p>
                    <p><img src="<?php echo wp_upload_dir()['baseurl'] . '/barcode/' . md5($data['reservation_id']); ?>.png" alt="" width="100%"></p>
                </div>
            </div>
        </div>
    
    <?php endif; ?>

</div>
<?php
// Nakon prikaza thank you ekrana, očisti session da se ekran ne prikazuje iznova
unset(
    $_SESSION['dc_success_reservation'],
    $_SESSION['poziv_na_broj'],
    $_SESSION['dc_first_step_data'],
    $_SESSION['putovanje_id']
);
?>

