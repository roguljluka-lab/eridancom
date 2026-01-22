<style>
    .form-table {
        min-width: 400px;
    }
    .form-table td {
        padding: 10px !important;
    }
    #setting-error-tgmpa {
        display: none !important;
    }
    .dc_payments td, .dc_payments th {
        text-align: center;
    }
</style>
<?php include_once (DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>

<div class="wrap">
    <?php  //echo '<pre>'; //print_r($rezervacija); //echo '</pre>';

    $zdravstvena_iskaznica = false;
    if($rezervacija->putnik_vrsta_isprave == "nema_putne_isprave") {
        $zdravstvena_iskaznica = true;
    }

    $iznos_putovanja = $rezervacija->nacin_placanja == 5 ? $rezervacija->putovanje_ukupni_iznos_kartica : $rezervacija->putovanje_ukupni_iznos;

    ?>

    <hr class="wp-header-end">
    <h1 class="wp-heading-inline" style="margin-bottom: 1rem;">Rezervacija #<?php echo $rezervacija->id; ?></h1>
    <?php if($AdminClass->potvrdena_rezervacija($rezervacija) == 'circle-red') : ?>
        <a href="<?php echo admin_url('admin.php?page=dcr&action=reaktiviraj&id=' . $rezervacija->id); ?>" class="page-title-action">Rezerviraj ponovno (na <?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h)</a>
    <?php endif; ?>
    <a href="<?php echo admin_url('admin.php?page=dcr&action=smece&id=' . $rezervacija->id); ?>" class="page-title-action"><?php echo $rezervacija->smece == 1 ? 'VRATI' : 'U smeće'; ?></a>

    <form name="post" action="#" method="post">
        <div id="postbox-container-2" class="postbox-container dc-admin-col">
            <table class="wp-list-table widefat striped dc_data_table">
                <tbody>
                <tr>
                    <td colspan="2">
                        <h2 style="margin: 0; text-align: center">REZERVACIJA</h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Naziv putovanja</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->putovanje_naziv; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Šifra</b>
                    </td>
                    <td>
                        <strong><?php echo $rezervacija->putovanje_sifra; ?></strong>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Datum rezervacije</b>
                    </td>
                    <td>
                        <?php echo date('d.m.Y. H:i', strtotime($rezervacija->created_at)); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Način plaćanja</b>
                    </td>
                    <td>
                        <?php echo $AdminClass->get_payment_status($rezervacija->nacin_placanja); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Iznos putovanja</b>
                    </td>
                    <td>
                        <?php
                        echo number_format($iznos_putovanja, 2);
                        ?> EUR</td>
                </tr>
                <tr>
                    <td>
                        <b>Popust</b>
                    </td>
                    <td>
                        <input type="text" name="popust" id="popust" value="<?php echo number_format($rezervacija->popust, 2); ?>">
                        <input type="submit" name="update_reservation" id="update_reservation" class="button button-primary button-large" value="Spremi" onclick="return confirmSubmit();">
                    </td>
                </tr>
                <tr style="background: #c8ffc8">
                    <td>
                        <b>Sveukupno</b>
                    </td>
                    <td>
                        <b>
                            <?php
                            // $sveukupi_iznos = $iznos_putovanja - ($iznos_putovanja * ($rezervacija->popust / 100));
                            $sveukupi_iznos = $iznos_putovanja - $rezervacija->popust;
                            echo number_format($sveukupi_iznos, 2);
                            ?> EUR
                        </b>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="wp-list-table widefat striped dc_data_table" style="margin-top: 1rem;">
                <tbody>
                <tr>
                    <td colspan="2">
                        <h2 style="margin: 0; text-align: center">UGOVARATELJ <a href="<?php echo admin_url('admin.php?page=dcr-putnici&action=uredi&id=' . $rezervacija->ugovaratelj_id); ?>" class="dc-edit-button" style="float: right">Promijeni</a></h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Ime i prezime ugovaratelja</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->ugovaratelj_ime . ' ' . $rezervacija->ugovaratelj_prezime; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Email ugovaratelja</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->ugovaratelj_email; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Kontakt broj ugovaratelja</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->ugovaratelj_kontakt; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <h2 style="margin: 0; text-align: center"">PUTNIK <a href="<?php echo admin_url('admin.php?page=dcr-putnici&action=uredi&id=' . $rezervacija->putnik_id); ?>" class="dc-edit-button" style="float: right">Promijeni</a></h2>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Ime i prezime putnika</b>
                    </td>
                    <td><?php echo $rezervacija->putnik_ime . ' ' . $rezervacija->putnik_prezime; ?></td>
                </tr>
                <tr>
                    <td>
                        Kontakt broj putnika
                    </td>
                    <td>
                        <?php echo $rezervacija->putnik_kontakt; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Škola</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->skola_naziv; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Školska godina</b>
                    </td>
                    <td>
                        <?php echo substr($rezervacija->razred_sk_godina, 0, 4) . '. / ' . substr($rezervacija->razred_sk_godina, 4, 4) . '.'; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Razred</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->razred_naziv; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Razrednik</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->razred_razrednik; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Adresa putnika</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->putnik_adresa . '<br>' . $rezervacija->putnik_pb . ' ' . $rezervacija->putnik_mjesto; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>OIB putnika</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->putnik_oib; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Datum rođenja putnika</b>
                    </td>
                    <td>
                        <?php echo $rezervacija->putnik_rodendan; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Spol putnika</b>
                    </td>
                    <td>
                        <?php
                        switch ($rezervacija->putnik_spol) {
                            case "zensko":
                                echo "Žensko";
                                break;
                            default:
                                echo "Muško";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Isprava putnika</b>
                    </td>
                    <td>
                        <?php if($zdravstvena_iskaznica) : ?>
                            Zdravstvena iskaznica
                        <?php else : ?>
                            <?php echo ucfirst($rezervacija->putnik_vrsta_isprave); ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Broj isprave</b>
                    </td>
                    <td>
                        <?php if($zdravstvena_iskaznica) : ?>
                            <?php echo $rezervacija->putnik_broj_zdravstvene; ?>
                        <?php else : ?>
                            <?php echo $rezervacija->putnik_broj_isprave; ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php if(!$zdravstvena_iskaznica) : ?>
                    <tr>
                        <td>
                            <b>Isprava vrijedi do</b>
                        </td>
                        <td>
                            <?php echo $rezervacija->putnik_isprava_vrijedi; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td>
                        <b>Broj eracuni ponude</b>
                    </td>
                    <td>
                        <?php echo nl2br($rezervacija->broj_eracuni_ponude); ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <b>Dodatne informacije</b><br>
                        <?php echo nl2br($rezervacija->informacije); ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Kopija ugovora</b>
                    </td>
                    <td>
                        <input type="hidden" id="iznos_putovanja" name="iznos_putovanja" value="<?php echo number_format($iznos_putovanja, 2); ?>">
                        <input type="hidden" id="reservation_id" name="reservation_id" value="<?php echo $rezervacija->id; ?>">
                        <input type="text" id="contract_email" name="contract_email" value="<?php echo $rezervacija->ugovaratelj_email; ?>">
                        <span class="page-title-action submit_contract">Pošalji</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="postbox-container-2" class="postbox-container dc-admin-col">
            <table class="wp-list-table widefat striped dc_data_table">
                <tbody>
                <tr>
                    <td colspan="4">
                        <h2 style="margin: 0; text-align: center">Nova uplata</h2>
                    </td>
                </tr>
                <tr class="td_to_new_row_hide">
                    <td>
                        <b>Iznos</b>
                    </td>
                    <td>
                        <b>Tip dokumenta</b>
                    </td>
                    <td>
                        <b>Vrsta plaćanja</b>
                    </td>
                    <td>
                        <b>Spremi</b>
                    </td>
                </tr>
                <tr class="td_to_new_row">
                    <td>
                        <input type="hidden" id="ukupni_iznos_putovanja" value="<?php echo $sveukupi_iznos; ?>">
                        <input type="text" id="ukupni_iznos" class="validate">
                    </td>
                    <td>
                        <select name="document_type" id="document_type">
                            <option value="offer_invoice">Ponuda i račun</option>
                            <option value="just_offer">Samo ponuda</option>
                        </select>
                    </td>
                    <td>
                        <select name="methodOfPayment" id="methodOfPayment">
                            <option value="Cash" <?php echo $rezervacija->status == 1 ? 'selected=""' : ''; ?>>Gotovina</option>
                            <option value="CreditCard" <?php echo $rezervacija->status == 2 ? 'selected=""' : ''; ?>>POS uređaj</option>
                        </select>
                    </td>
                    <td>
                        <span class="page-title-action submit_offer_invoice">+ Dodaj</span>
                    </td>
                </tr>
                <tr class="posalji_mail_tr">
                    <td colspan="2">
                        <input type="checkbox" name="send_mail" id="send_mail" value="1" checked="">
                        <label for="send_mail">Pošalji račun na mail ugovaratelja</label>
                    </td>
                </tr>
                </tbody>
            </table>
            <table class="wp-list-table widefat striped dc_data_table dc_payments" style="margin-top: 1rem;">
                <tbody>
                <tr>
                    <td colspan="4">
                        <h2 style="margin: 0; text-align: center">Izdani  računi</h2>
                    </td>
                </tr>
                <?php

                $preostali_iznos = $sveukupi_iznos; // ako nema uplata onda je prikazan sveukupni iznos
                $show_storno_btn = false;

                if(count($payments) > 0) : ?>
                    <?php
                    $total_paid = 0;
                    ?>
                    <tr>
                        <th style="font-weight: 700;">Datum</th>
                        <th style="font-weight: 700;">Broj eračuna</th>
                        <th style="font-weight: 700;">Način plaćanja</th>
                        <th style="font-weight: 700;">Iznos</th>
                    </tr>
                    <?php foreach($payments as $payment) : ?>
                        <?php
                        $total_paid += $payment->paymentAmount;
                        $preostali_iznos = $sveukupi_iznos - $total_paid;
                        ?>
                        <tr class="dc-inv" data-payment-id="<?php echo $payment->id; ?>" data-invoice-number="<?php echo explode('. ', $payment->invoice_number)[1]; ?>" data-reservation-id="<?php echo $rezervacija->id; ?>">
                            <td><?php echo date('d.m.Y.', strtotime($payment->paymentDate)); ?></td>
                            <td><?php echo $payment->invoice_number; ?> <span class="dashicons dashicons-email dc-send-button"></span></td>
                            <td><?php echo $payment->methodOfPayment; ?></td>
                            <td style="text-align: right;">
                                <?php if($payment->paymentAmount > 0 && $payment->storno != 1) : $show_storno_btn = true; ?>
                                    <span class="storno-btn storno_btn dc-storno-col" style="margin-right: 10px;"><b><span class="dashicons dashicons-trash"></span></b></span>
                                <?php endif; ?>
                                <?php echo $payment->paymentAmount; ?> EUR
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="3" style="text-align: right; font-weight: 700;">Ukupno naplaćeno:</td>
                        <td style="text-align: right; font-weight: 700;"><?php echo number_format($total_paid, 2); ?> EUR</td>
                    </tr>
                    <tr>
                        <td style="text-align: left;"><b><?php  if(count($payments) > 0 && $show_storno_btn) { ?><span class="storno_btn"><span id="storno_invoices"><span class="dashicons dashicons-trash"></span> Storniraj račune</span></span><?php } ?></b></td>
                        <td colspan="2" style="text-align: right; font-weight: 700;">Preostalo za naplatu:</td>
                        <td style="text-align: right; font-weight: 700;"><?php echo number_format($preostali_iznos, 2); ?> EUR</td>
                    </tr>
                <?php else : ?>
                    <tr>
                        <td colspan="3">Nema zavedenih uplata za ovu rezervaciju.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
            <table class="wp-list-table widefat striped dc_data_table" style="margin-top: 1rem;">
                <tbody>
                <tr>
                    <td colspan="2">
                        <h2 style="margin: 0; text-align: center">NAPLATNI LINK</h2>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <a href="<?php echo get_permalink($dc_settings->dc_postavke->stranica_naplate); ?>?rid=<?php echo $rezervacija->id; ?>&iznos=<?php echo number_format($preostali_iznos, 2); ?>" target="_blank"><?php echo get_permalink($dc_settings->dc_postavke->stranica_naplate); ?>?rid=<?php echo $rezervacija->id; ?>&iznos=<?php echo number_format($preostali_iznos, 2); ?></a>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
        <div id="postbox-container-2" class="postbox-container dc-admin-col">
            <table class="wp-list-table widefat striped dc_data_table" style="margin-bottom: 1rem;">
                <input type="hidden" name="rezervacija_id" value="<?php echo $rezervacija->id; ?>">
                <tr>
                    <td colspan="2">
                        <h2 style="margin: 0; text-align: center">
                            ADMINISTRACIJA
                        </h2>
                        <?php
                        echo $handle_new_trip;
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Status rezervacije</b>
                    </td>
                    <td>
                        <select name="status">
                            <option value="1" <?php echo $rezervacija->status == 1 ? 'selected=""' : ''; ?>>Čekanje uplate</option>
                            <option value="2" <?php echo $rezervacija->status == 2 ? 'selected=""' : ''; ?>>Započeto online</option>
                            <option value="3" <?php echo $rezervacija->status == 3 ? 'selected=""' : ''; ?>>Odustao</option>
                            <option value="4" <?php echo $rezervacija->status == 4 ? 'selected=""' : ''; ?>>Djelomično plaćeno</option>
                            <option value="6" <?php echo $rezervacija->status == 6 ? 'selected=""' : ''; ?>>Plaćeno u cijelosti</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <b>Admin napomene</b>
                    </td>
                    <td>
                        <textarea name="admin_info" id="admin_info" cols="30" rows="10"><?php echo $rezervacija->admin_info; ?></textarea>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <input type="submit" name="update_reservation" id="update_reservation" class="button button-primary button-large" value="Spremi promjene" onclick="return confirmSubmit();">
                    </td>
                </tr>
            </table>
            <?php include_once(DC_REZERVACIJE_PATH . 'views/admin/comon/logs.php'); ?>
        </div>
    </form>
</div>

