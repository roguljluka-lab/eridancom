<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline">Lista putovanja</h1>

    <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=dodaj-skolsko'); ?>" class="page-title-action">Dodaj školsko putovanje</a> <!-- IZLETI -->
    <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=dodaj-izlet'); ?>" class="page-title-action">Dodaj izlet</a> <!-- IZLETI -->
    <a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=dodaj-ostalo'); ?>" class="page-title-action">Dodaj ostalo putovanje</a> <!-- IZLETI -->
    <hr class="wp-header-end">

    <br class="clear">

    <form id="filter" method="get" style="margin-bottom: 2.5rem;">
        <div class="actions bulkactions dc_collapse">
            <input type="hidden" name="page" value="dcr">
            <input type="hidden" name="filter" value="res">
            <input type="hidden" name="or" value="rezervacije.id">
            <input type="hidden" name="so" value="desc">
            <input type="hidden" name="sg" value="-1">
            <input type="hidden" name="s" value="-1">
            <input type="hidden" name="r" value="-1">
            <input type="hidden" name="su" value="-1">
            <input type="hidden" name="np" value="-1">
            <input type="hidden" name="q" value="">
            <input type="hidden" name="od" value="">
            <input type="hidden" name="do" value="">
                <select name="p" class="filter-tour_name filter_tour_name_putovanja">
                    <option value="-1">Naziv putovanja (šifra)</option>
                    <?php foreach($filter['putovanja'] as $filter_putovanje) : ?>
                        <option value="<?php echo $filter_putovanje['id']; ?>" <?php echo isset($_GET['p']) && $filter_putovanje['id'] == $_GET['p'] ? 'selected=""' : ''; ?>><?php echo $filter_putovanje['naziv']; ?> (<?php echo $filter_putovanje['sifra']; ?>)</option>
                    <?php endforeach; ?>
                </select>
        </div>
    </form>

    <ul class="subsubsub">
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja'); ?>" <?php echo !isset($_GET['type']) ? 'class="current"' : ''; ?> aria-current="page">Sva putovanja</a> |</li>
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&type=skolska'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'skolska' ? 'class="current"' : ''; ?> aria-current="page">Školska putovanja</a> |</li> <!-- IZLETI -->
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&type=izlet'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'izlet' ? 'class="current"' : ''; ?> aria-current="page">Izleti</a> |</li> <!-- IZLETI -->
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&type=ostala'); ?>" <?php echo isset($_GET['type']) && $_GET['type'] == 'ostala' ? 'class="current"' : ''; ?> aria-current="page">Ostala putovanja</a> |</li> <!-- IZLETI -->
        <li class="publish"><a href="<?php echo admin_url('admin.php?page=dcr-putovanja&action=smece'); ?>">Smeće</a></li>
    </ul>
    <br class="clear">

    <div style="overflow-x: auto;">
        <table class="wp-list-table widefat striped" style="min-width: 800px;">
            <thead>
            <tr>
                <th>Naziv (šifra)</th>
                <th>Kapacitet</th>
                <th>Akontacija</th>
                <th>Iznos</th>
                <th>Termin</th>
                <th>Škola</th>
                <th>Razredi</th>
                <th>eRačuni šifra</th>
                <th>Datum</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($putovanja['result'] as $putovanje) : ?>
                <tr>
                    <td class="title column-title page-title">
                        <a class="row-title" href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=' . $putovanje->id . '&sp=-1&su=-1&np=-1&od&do&q='); ?>"><span class="dashicons dashicons-search"></span></a>
                        <?php echo $putovanje->naziv; ?> (<?php echo $putovanje->sifra; ?>)
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
                        <span class="capacity capacity-orange" title="REZERVIRANO"><?php echo $kapacitet['rezervirana_mjesta']; ?></span>
                        <span class="capacity capacity-grey" title="SLOBODNIH MJESTA"><?php echo $putovanje->broj_putnika - $kapacitet['rezervirana_mjesta'] - $kapacitet['zauzeta_mjesta']; ?></span>
                        <span class="capacity capacity-blue" title="BROJ MJESTA"><?php echo $putovanje->broj_putnika; ?></span>
                    </td>
                    <td><?php echo $putovanje->akontacija; ?> €</td>
                    <td>
                        Gotovina: <?php echo $putovanje->ukupni_iznos; ?> €<br>
                        Na rate: <?php echo $putovanje->ukupni_iznos_kartica; ?> €
                    </td>
                    <td>
                        Polazak: <?php echo date('d.m.Y.', strtotime($putovanje->putovanje_od)); ?><br>
                        Povratak: <?php echo date('d.m.Y.', strtotime($putovanje->putovanje_do)); ?>
                    </td>
                    <td><?php echo json_decode($putovanje->skola)->naziv ?? '/'; ?></td>
                    <td>
                        <?php foreach(json_decode($putovanje->razredi, true) as $razred) : ?>
                            <?php echo $razred['naziv'] ?? '/'; ?>,
                        <?php endforeach; ?>
                    </td>
                    <td>
                        <?php echo $putovanje->eracuni_number; ?>
                    </td>
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
    $total_pages = ceil($total_count / 50);
    if($total_pages > 1) : ?>
        <form id="filter" method="get">
            <input type="hidden" name="page" value="dcr-putovanja">
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
        </form>
    <?php endif; ?>
    <div class="clear"></div>
</div>
