<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>

<?php include_once (DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>

<form id="filter" method="get" style="margin-bottom: 2.5rem;">
    <div class="wrap">
        <h2>Školske rezervacije</h2>
        <?php //echo '<pre>'; //print_r($filter); //echo '</pre>'; ?>
        <span class="dc_filter_btn button">Prikaži filtere</span>
        <div class="alignleft actions bulkactions dc_collapse">
            <input type="hidden" name="page" value="dcr">
            <input type="hidden" name="filter" value="res">
            <input type="hidden" name="type" value="<?php echo $_GET['type'] ?? ''; ?>">
            <input type="hidden" name="or" id="or" value="<?php echo isset($_GET['or']) ? $_GET['or'] : 'rezervacije.id'; ?>">
            <input type="hidden" name="so" id="so" value="<?php echo isset($_GET['so']) ? $_GET['so'] : 'desc'; ?>">

            <p>
                <select name="p" class="filter-tour_name">
                    <option value="-1">Naziv putovanja (šifra)</option>
                    <?php foreach($filter['putovanja'] as $filter_putovanje) : ?>
                        <option value="<?php echo $filter_putovanje['id']; ?>" <?php echo isset($_GET['p']) && $filter_putovanje['id'] == $_GET['p'] ? 'selected=""' : ''; ?>><?php echo $filter_putovanje['naziv']; ?> (<?php echo $filter_putovanje['sifra']; ?>)</option>
                    <?php endforeach; ?>
                </select>

                <select name="sg" id="bulk-action-selector-top" class="filter_sk_godina">
                    <option value="-1">Školska godina</option>
                    <?php foreach($filter['skolske_godine'] as $sk_godina => $razred_id) : ?>
                        <option value="<?php echo $sk_godina; ?>" <?php echo isset($_GET['sg']) && $sk_godina == $_GET['sg'] ? 'selected=""' : ''; ?>><?php echo substr($sk_godina, 0, 4) . '. / ' . substr($sk_godina, 4, 4) . '.'; ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="s" class="filter-school">
                    <option value="-1">Naziv škole</option>
                    <?php foreach($filter['skole'] as $skola) : ?>
                        <option value="<?php echo $skola['id']; ?>" <?php echo isset($_GET['s']) && $skola['id'] == $_GET['s'] ? 'selected=""' : ''; ?>><?php echo $skola['naziv']; ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="r" class="filter-grade">
                    <option value="-1">Razred</option>
                    <?php foreach($filter['razredi'] as $razred) : ?>
                        <option value="<?php echo $razred['naziv']; ?>" <?php echo isset($_GET['r']) && $razred['naziv'] == $_GET['r'] ? 'selected=""' : ''; ?>><?php echo $razred['naziv']; ?></option>
                    <?php endforeach; ?>
                </select>

                <select name="su">
                    <option value="-1">Status rezervacije</option>
                    <option value="1" <?php echo isset($_GET['su']) && 1 == $_GET['su'] ? 'selected=""' : ''; ?>>Čekanje uplate</option>
                    <option value="2" <?php echo isset($_GET['su']) && 2 == $_GET['su'] ? 'selected=""' : ''; ?>>Započeto online</option>
                    <option value="3" <?php echo isset($_GET['su']) && 3 == $_GET['su'] ? 'selected=""' : ''; ?>>Odustao</option>
                    <option value="4" <?php echo isset($_GET['su']) && 4 == $_GET['su'] ? 'selected=""' : ''; ?>>Djelomično plaćeno</option>
                    <option value="6" <?php echo isset($_GET['su']) && 6 == $_GET['su'] ? 'selected=""' : ''; ?>>Plaćeno u cijelosti</option>
                </select>

                <select name="np">
                    <option value="-1">Način plaćanja</option>
                    <option value="1" <?php echo isset($_GET['np']) && 1 == $_GET['np'] ? 'selected=""' : ''; ?>>Rezervacija</option>
                    <option value="2" <?php echo isset($_GET['np']) && 2 == $_GET['np'] ? 'selected=""' : ''; ?>>Transakcijski</option>
                    <option value="3" <?php echo isset($_GET['np']) && 3 == $_GET['np'] ? 'selected=""' : ''; ?>>Uplata - WsPay</option>
                    <option value="4" <?php echo isset($_GET['np']) && 4 == $_GET['np'] ? 'selected=""' : ''; ?>>Osobni dolazak</option>
                </select>

                <input type="text" name="q" placeholder="Upišite pojam" value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>" autocomplete="off">

                OD <input type="date" name="od" placeholder="od" value="<?php echo isset($_GET['od']) ? $_GET['od'] : ''; ?>">
                DO <input type="date" name="do" placeholder="do" value="<?php echo isset($_GET['do']) ? $_GET['do'] : ''; ?>">


                <input type="submit" class="dc_apply_button button" value="Primijeni">
                <?php if(isset($_GET['filter'])) : ?>
                    <a href="<?php echo admin_url('admin.php?page=dcr'); ?>">Poništi filter</a>
                <?php endif; ?>
            </p>
            <hr>
            <p class="dc-float-right">
                <?php if(isset($_GET['filter'])) : ?>
                    <span class="page-title-action export_filter_btn">Izvezi u excel</span>
                <?php else : ?>
                    <a href="<?php echo admin_url('admin.php?page=dcr&export=csv'); ?>" class="page-title-action">Izvezi u excel</a>
                <?php endif; ?>
                <a href="<?php echo admin_url('admin.php?page=dcr&eracuni=refresh'); ?>" class="page-title-action">Osvježi uplate</a>
            </p>
            <hr>

        </div>

        <br class="clear">
        <p>

        <span class="dc-float-right">
            <input type="text" id="searchInput" placeholder="Brzi filter...">
        </span>
        </p>

        <?php
        $putovanje_id = isset($_GET['p']) && $_GET['p'] > 0 ? $_GET['p'] : (isset($_GET['sp']) ? $_GET['sp'] : null);
        $putovanje = $AdminClass->get_putovanje($putovanje_id);
        $total_count = $rezervacije['total_count'];
        ?>

        <?php if(isset($_GET['filter']) && ((isset($_GET['p']) && $_GET['p'] > 0) || (isset($_GET['sp']) && $_GET['sp'] > 0))) : ?>

            <?php if($dc_settings->dc_postavke->rezervirano_sati > 0) : ?>
                <?php
                $kapacitet = $AdminClass->dc_count_zauzeto_mjesta($putovanje_id);
                $isteklih_rezervacija = $total_count -  $kapacitet['rezervirana_mjesta'] - $kapacitet['zauzeta_mjesta'];
                ?>

                <p>
                    <span class="dc_status_icons">Plaćeno / djelomično plaćeno</span> <span class="capacity capacity-green dc-mr-3" title="PLAĆENO"><?php echo $kapacitet['zauzeta_mjesta']; ?></span>
                    <span class="dc_status_icons">Rezervirano</span> (<?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h) <span class="capacity capacity-orange dc-mr-3" title="REZERVIRANO <?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h"><?php echo $kapacitet['rezervirana_mjesta']; ?></span>
                    <span class="dc_status_icons">Istekle rezervacije</span> (<?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h) <span class="capacity capacity-red dc-mr-3" title="ISTEKLE REZERVACIJE"><?php echo $isteklih_rezervacija; ?></span></span>
                    <span class="dc_status_icons">Preostalo slobodno <span class="capacity capacity-grey dc-mr-3" title="SLOBODNIH MJESTA"><?php echo $putovanje->broj_putnika - $kapacitet['zauzeta_mjesta'] - $kapacitet['rezervirana_mjesta']; ?></span></span>
                    <span class="dc_status_icons">Max. broj mjesta <span class="capacity capacity-blue dc-mr-3" title="BROJ MJESTA"><?php echo $putovanje->broj_putnika; ?></span></span>
                </p>
            <?php endif; ?>


        <?php else: ?>
            <?php if($dc_settings->dc_postavke->rezervirano_sati > 0) : ?>
                <?php
                    $kapacitet = $AdminClass->dc_count_zauzeto_mjesta($putovanje_id);
                    $isteklih_rezervacija = $total_count -  $kapacitet['rezervirana_mjesta'] - $kapacitet['zauzeta_mjesta'];
                ?>
                <p>
                    <span class="dc_status_icons">Plaćeno / djelomično plaćeno</span> <span class="capacity capacity-green dc-mr-3" title="PLAĆENO"></span>
                    <span class="dc_status_icons">Rezervirano</span> (<?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h) <span class="capacity capacity-orange dc-mr-3" title="REZERVIRANO <?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h"></span>
                    <span class="dc_status_icons">Istekle rezervacije</span> (<?php echo $dc_settings->dc_postavke->rezervirano_sati; ?>h) <span class="capacity capacity-red dc-mr-3" title="ISTEKLE REZERVACIJE"></span>
                </p>
                <ul class="subsubsub">
                    <!--li class="all"><a href="<?php echo admin_url('admin.php?page=dcr&type=all&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'); ?>"  <?php echo !isset($_GET['type']) ? 'class="current"' : ''; ?> aria-current="page">Sve rezervacije</a> |</li-->
                    <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr&type=skolska&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'skolska' ? 'class="current"' : ''; ?> aria-current="page">Školske rezervacije</a> |</li> <!-- IZLETI -->
                    <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr&type=izlet&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'izlet' ? 'class="current"' : ''; ?> aria-current="page">Izleti</a> |</li> <!-- IZLETI -->
                    <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr&type=ostala&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'ostala' ? 'class="current"' : ''; ?> aria-current="page">Ostale rezervacije</a> |</li> <!-- IZLETI -->
                    <li class="publish"><a href="<?php echo admin_url('admin.php?page=dcr&action=smece'); ?>">Smeće</a></li> |
                    Zadnji put osvježene uplate: <?php echo date('d.m.Y. H:i:s', strtotime($dc_settings->dc_postavke->zadnje_osvjezeni_eracuni)); ?>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
        <br class="clear">
        <div style="overflow-x: auto;">
            <table class="wp-list-table widefat striped" id="dc_data_table">
                <thead>
                <tr>
                    <th style="display: none;">Potvrđene rezervacije</th>
                    <th class="sortable">
                        <a class="reorder" data-order="putovanje.naziv">
                            <span>Putovanje / šifra</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th class="sortable">
                        <a class="reorder" data-order="putnik.ime">
                            <span>Putnik</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th class="sortable">
                        <a class="reorder" data-order="ugovaratelj.ime">
                            <span>Ugovaratelj</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th>Škola</th>
                    <th class="sortable">
                        <a class="reorder" data-order="razred.naziv">
                            <span>Razred</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th class="sortable">
                        <a class="reorder" data-order="rezervacije.nacin_placanja">
                            <span>Način plaćanja</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th class="sortable">
                        <a class="reorder" data-order="rezervacije.status">
                            <span>Status rezervacije</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th style="display: none;">Školska godina</th>
                    <th style="display: none;">Kontakt broj</th>
                    <th style="display: none;">OIB putnika</th>
                    <th style="display: none;">Adresa</th>
                    <th style="display: none;">Poštanski broj</th>
                    <th style="display: none;">Mjesto</th>
                    <th style="display: none;">Spol</th>
                    <th style="display: none;">Datum rođenja</th>
                    <th style="display: none;">Vrsta isprave</th>
                    <th style="display: none;">Broj isprave</th>
                    <th style="display: none;">Isprava vrijedi do</th>
                    <th>Uplaćeno / Preostalo</th>
                    <th class="sortable">
                        <a class="reorder" data-order="rezervacije.created_at">
                            <span>Datum rezervacije</span>
                            <span class="sorting-indicators">
                        <span class="sorting-indicator asc" aria-hidden="true"></span>
                        <span class="sorting-indicator desc" aria-hidden="true"></span>
                    </span>
                        </a>
                    </th>
                    <th style="display: none;">Kontakt ugovaratelja</th>
                    <th style="display: none;">Email ugovaratelja</th>
                    <th style="display: none;">Iznos putovanja (akontacija)</th>
                    <th style="display: none;">Iznos putovanja (gotovina)</th>
                    <th style="display: none;">Iznos putovanja (kartica)</th>
                    <th style="display: none;">Razrednik</th>
                    <th class="sortable">
                        <a class="reorder" data-order="rezervacije.broj_rezervacije">
                            <span>ID</span>
                            <span class="sorting-indicators">
                                <span class="sorting-indicator asc" aria-hidden="true"></span>
                                <span class="sorting-indicator desc" aria-hidden="true"></span>
                            </span>
                        </a>
                    </th>
                    <th style="display: none;">Informacije</th>
                    <th style="display: none;">Admin info</th>
                </tr>
                </thead>
                <tbody>
                <?php //echo '<pre>'; //print_r($rezervacije); //echo '</pre>';?>
                <?php if($total_count > 0) : ?>

                    <?php foreach ($rezervacije['result'] as $rezervacija) : ?>
                        <tr <?php echo $rezervacija->smece == 1 ? 'style="background: #fff0f0;' : ''; ?>">
                        <td style="display: none;">
                            <?php if($rezervacija->smece == 0) : ?>
                                <?php echo $AdminClass->potvrdena_rezervacija($rezervacija) == 'circle-green' ? 'Plaćeno' : ($AdminClass->potvrdena_rezervacija($rezervacija) == 'circle-red' ? 'Isteklo' : 'Rezervirano'); ?>
                            <?php else : ?>
                                SMEĆE
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="circle">
                                <?php if($rezervacija->smece == 0) :
                                    if(isset($kapacitet)) {
                                        $circle_color = $AdminClass->potvrdena_rezervacija($rezervacija);
                                        echo '<span class="' . $circle_color . ' dc-mr-3"></span>';
                                    }
                                    ?>

                                <?php else : ?>
                                    <span class="dashicons dashicons-trash"> </span>
                                <?php endif; ?>
                                <a class="row-title" href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=' . $rezervacija->putovanje_id . '&sp=-1&su=-1&np=-1&od&do&q'); ?>"><span class="dashicons dashicons-search"></span></a>
                                <?php echo $rezervacija->putovanje_naziv; ?>
                                ( <?php echo $rezervacija->putovanje_sifra; ?> )
                            </div>
                            <div class="row-actions hide_from_excel">
                                <?php if(isset($circle_color) && $circle_color == 'circle-red') : ?>
                                    <span class="edit">
                                            <a href="<?php echo admin_url('admin.php?page=dcr&action=reaktiviraj&id=' . $rezervacija->id); ?>"><b>REAKTIVIRAJ PONOVNO NA 32 SATA</b></a> |
                                        </span>
                                <?php endif; ?>
                                <span class="edit">
                                        <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=uredi&id=' . $rezervacija->putovanje_id); ?>">Uredi putovanje</a> |
                                    </span>
                                <span class="trash">
                                        <a href="<?php echo admin_url('admin.php?page=dcr&action=smece&id=' . $rezervacija->id); ?>" class="submitdelete">
                                            U smeće
                                        </a>
                                    </span>
                            </div>
                        </td>
                        <td>
                            <a class="row-title" href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=-1&sp=-1&su=-1&np=-1&od&do&q=' . $rezervacija->putnik_oib); ?>"><span class="dashicons dashicons-search"></span></a>
                            <a href="<?php echo admin_url('admin.php?page=dcr&action=uredi&id=' . $rezervacija->id); ?>">
                                <?php echo $rezervacija->putnik_ime; ?> <?php echo $rezervacija->putnik_prezime; ?>
                            </a>
                            <?php if(!empty($rezervacija->informacije)) : ?>
                                <span class="info-icon" title="<?php echo $rezervacija->informacije; ?>"></span>
                            <?php endif; ?>
                            <?php if(!empty($rezervacija->admin_info)) : ?>
                                <span class="admin-info-icon" title="<?php echo $rezervacija->admin_info; ?>"></span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php echo $rezervacija->ugovaratelj_ime . ' ' . $rezervacija->ugovaratelj_prezime; ?>
                        </td>
                        <td>
                            <?php echo $rezervacija->skola_naziv; ?>
                        </td>
                        <td>
                            <?php echo $rezervacija->razred_naziv; ?>
                        </td>
                        <td>
                            <?php echo $AdminClass->get_payment_status($rezervacija->nacin_placanja); ?>
                        </td>
                        <td>
                            <?php echo $AdminClass->get_status($rezervacija->status); ?>
                        </td>
                        <td style="display: none;">
                            <?php echo substr($rezervacija->razred_sk_godina, 0, 4) . '. / ' . substr($rezervacija->razred_sk_godina, 4, 4) . '.'; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_kontakt; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_oib; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_adresa; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_pb; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_mjesto; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_spol = 'musko' ? 'Muško' : 'Žensko'; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_rodendan; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo ucfirst($rezervacija->putnik_vrsta_isprave); ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_broj_isprave; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putnik_isprava_vrijedi; ?>
                        </td>
                        <td>
                            <?php
                            $ukupni_iznos_putovanja = $rezervacija->nacin_placanja == 5 ? $rezervacija->putovanje_ukupni_iznos_kartica : $rezervacija->putovanje_ukupni_iznos;
                            if($rezervacija->popust > 0) {
                                $ukupni_iznos_putovanja = $ukupni_iznos_putovanja - ($ukupni_iznos_putovanja * ($rezervacija->popust / 100));
                            }
                            $uplaceno = $AdminClass->get_reservation_total_paid($rezervacija->id) ?? 0;
                            $preostalo = $ukupni_iznos_putovanja - $uplaceno;

                            $status_uplaceno = 'success';
                            if($uplaceno == 0) {
                                $status_uplaceno = 'info';
                            } else if($uplaceno > 0 && $uplaceno < $preostalo) {
                                $status_uplaceno = 'warning';
                            } else if ($uplaceno > $ukupni_iznos_putovanja) {
                                $status_uplaceno = 'danger';
                            }

                            echo '<span class="status status-' . $status_uplaceno . '">' . number_format($uplaceno, 2) . ' € / ' . number_format($preostalo, 2) . ' €</span>';

                            ?>
                        </td>
                        <td>
                            <?php echo date('d.m.Y. H:i', strtotime($rezervacija->created_at)); ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->ugovaratelj_kontakt; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->ugovaratelj_email; ?>
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putovanje_akontacija; ?> €
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putovanje_ukupni_iznos; ?> €
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->putovanje_ukupni_iznos_kartica; ?> €
                        </td>
                        <td style="display: none;">
                            <?php echo $rezervacija->razred_razrednik; ?>
                        </td>
                        <td>
                            <?php echo $rezervacija->id; ?>
                        </td>
                        <?php if(!empty($rezervacija->informacije)) : ?>
                            <td style="display: none;">
                                <?php echo $rezervacija->informacije; ?>
                            </td>
                        <?php endif; ?>
                        <?php if(!empty($rezervacija->admin_info)) : ?>
                            <td style="display: none;">
                                <?php echo $rezervacija->admin_info; ?>
                            </td>
                        <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>

                <?php else: ?>
                    <tr>
                        <td colspan="10">Nema rezervacija prema zadanom filteru.</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?php
        $total_pages = ceil($total_count / 50);
        if($total_pages > 1) : ?>
            <div class="tablenav-pages dc-mt-3" style="text-align: center;">
                <span class="pagination-links">
                    <span class="dc-minus-page tablenav-pages-navspan button <?php echo isset($_GET['pa']) && $_GET['pa'] > 1 ? '' : 'disabled'; ?>">«</span>
                    <span class="paging-input">
                        <input type="text" id="pa" name="pa" value="<?php echo isset($_GET['pa']) ? $_GET['pa'] : 1 ?>" size="1">
                        <span class="tablenav-paging-text"> od <span class="total-pages"><?php echo $total_pages; ?></span> (Ukupno <?php echo $total_count; ?> rezultata)</span>
                    </span>
                    <span class="dc-plus-page tablenav-pages-navspan button <?php echo isset($_GET['pa']) && ($_GET['pa'] < $total_pages) ? '' : (isset($_GET['pa']) ? 'disabled' : ''); ?>">»</span>
                </span>
            </div>
        <?php endif; ?>
    </div>
</form>
