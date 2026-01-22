<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>
<form id="filter" method="get" style="margin-bottom: 2.5rem;">
    <div class="wrap">
        <h1 class="wp-heading-inline">Rezervacije - smeće</h1>
        <?php //echo '<pre>'; //print_r($filter); //echo '</pre>'; ?>
        <br class="clear">
            <ul class="subsubsub">
                <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr&type=skolska&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'skolska' ? 'class="current"' : ''; ?> aria-current="page">Školske rezervacije</a> |</li>
                <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr&type=ostala&filter=res&or=rezervacije.id&so=desc&p=-1&sg=-1&s=-1&r=-1&su=-1&np=-1&q&od&do&pa=1'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'ostala' ? 'class="current"' : ''; ?> aria-current="page">Ostale rezervacije</a> |</li>
                <li class="publish"><a href="<?php echo admin_url('admin.php?page=dcr&action=smece'); ?>" class="current">Smeće</a></li>
            </ul>
        <br class="clear">
        <div style="overflow-x: auto;">
            <table class="wp-list-table widefat striped" id="dc_data_table">
                <thead>
                <tr>
                    <th style="width: 100px">ID</th>
                    <th>Putnik</th>
                    <th>Ugovaratelj</th>
                    <th>Putovanje / šifra</th>
                    <th>Status rezervacije</th>
                    <th>Način plaćanja</th>
                    <th>Škola</th>
                    <th>Razred</th>
                    <th>Datum rezervacije</th>
                </tr>
                </thead>
                <tbody>
                <?php //echo '<pre>'; //print_r($rezervacije); //echo '</pre>';?>
                <?php if(count($rezervacije) > 0) : ?>

                    <?php foreach ($rezervacije['result'] as $rezervacija) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo admin_url('admin.php?page=dcr&action=uredi&id=' . $rezervacija->id); ?>">
                                    <strong># <?php echo $rezervacija->id; ?></strong>
                                </a>
                            </td>
                            <td>
                                <?php echo $rezervacija->putnik_ime; ?> <?php echo $rezervacija->putnik_prezime; ?>
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
                                <strong>
                                    <a href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=' . $rezervacija->putovanje_id . '&sp=-1&su=-1&np=-1&od&do&q'); ?>">
                                        <?php echo $rezervacija->putovanje_naziv; ?>
                                        (<?php echo $rezervacija->putovanje_sifra; ?>)
                                    </a>
                                </strong>
                            </td>
                            <td>
                                <?php echo $AdminClass->get_status($rezervacija->status); ?>
                            </td>
                            <td>
                                <?php echo $AdminClass->get_payment_status($rezervacija->nacin_placanja); ?>
                            </td>
                            <td>
                                <?php echo $rezervacija->skola_naziv ?? '/'; ?>
                            </td>
                            <td>
                                <?php echo $rezervacija->razred_naziv ?? '/'; ?>
                            </td>
                            <td>
                                <?php echo date('d.m.Y. H:i', strtotime($rezervacija->created_at)); ?>
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
        $total_count = $rezervacije['total_count'];
        $total_pages = ceil($total_count / 20);
        if($total_count > 1) : ?>
            <div class="tablenav-pages dc-mt-3">
                <span class="pagination-links">
                    <span class="dc-minus-page tablenav-pages-navspan button <?php echo isset($_GET['pa']) && $_GET['pa'] > 1 ? '' : 'disabled'; ?>">«</span>
                    <span class="paging-input">
                        <input type="text" id="pa" name="pa" value="<?php echo isset($_GET['pa']) ? $_GET['pa'] : 1 ?>" size="1">
                        <span class="tablenav-paging-text"> od <span class="total-pages"><?php echo $total_pages; ?></span></span>
                    </span>
                    <span class="dc-plus-page tablenav-pages-navspan button <?php echo isset($_GET['pa']) && $_GET['pa'] < $total_pages ? '' : 'disabled'; ?>">»</span>
                </span>
            </div>
        <?php endif; ?>
    </div>
</form>