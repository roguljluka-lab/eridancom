<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline">Lista putovanja</h1>
    <hr class="wp-header-end">

    <ul class="subsubsub">
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja'); ?>">Sva putovanja</a> |</li>
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&type=skolska'); ?>">Školska putovanja</a> |</li> <!-- IZLETI -->
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&type=izlet'); ?>">Izleti</a> |</li> <!-- IZLETI -->
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&type=ostala'); ?>">Ostala putovanja</a> |</li> <!-- IZLETI -->
        <li class="publish"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=smece'); ?>" class="current">Smeće</a></li>
    </ul>
    <br class="clear">
    <div style="overflow-x: auto;">
        <table class="wp-list-table widefat striped" style="min-width: 800px;">
            <thead>
            <tr>
                <th>Naziv</th>
                <th>Kapacitet</th>
                <th>Škola</th>
                <th>Razredi</th>
                <th>Šifra</th>
                <th>Datum</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($putovanja['result'] as $putovanje) : ?>
                <tr>
                    <td class="title column-title page-title">
                        <strong>
                            <a class="row-title" href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=' . $putovanje->id . '&sp=' . $putovanje->id . '&su=-1&np=-1&od&do&q'); ?>"><?php echo $putovanje->naziv; ?></a>
                        </strong>
                        <div class="row-actions">
                            <span class="edit">
                                <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=uredi&id=' . $putovanje->id); ?>">Uredi</a> |
                            </span>
                            <span class="trash">
                                <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=smece&id=' . $putovanje->id); ?>" class="submitdelete"><?php echo $putovanje->status == 1 ? 'Smeće' : 'Vrati'; ?></a>
                            </span>
                        </div>
                    </td>
                    <td>
                        <?php $kapacitet = $AdminClass->dc_count_zauzeto_mjesta($putovanje->id); ?>
                        <span class="capacity capacity-green" title="PLAĆENO"><?php echo $kapacitet['zauzeta_mjesta']; ?></span>
                        <span class="capacity capacity-orange" title="REZERVIRANO 24h"><?php echo $kapacitet['rezervirana_mjesta']; ?></span>
                        <span class="capacity capacity-blue" title="BROJ MJESTA"><?php echo $putovanje->broj_putnika; ?></span>
                    </td>
                    <td><?php echo json_decode($putovanje->skola)->naziv; ?></td>
                    <td>
                        <?php foreach(json_decode($putovanje->razredi, true) as $razred) : ?>
                            <?php echo $razred['naziv']; ?>,
                        <?php endforeach; ?>
                    </td>
                    <td><?php echo $putovanje->sifra; ?></td>
                    <td class="date column-date">
                        <?php echo date('d.m.Y. H:i', strtotime($putovanje->created_at)) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
    $total_count = $putovanja['total_count'];
    $total_pages = ceil($total_count / 20);
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
    <div class="clear"></div>
</div>
