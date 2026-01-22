<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>

<?php include_once (DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>

<div class="wrap">
    <h1 class="wp-heading-inline">Putnici / ugovaratelji</h1>
    <hr class="wp-header-end">
    <ul class="subsubsub">
        <li class="all"><a href="<?php echo admin_url('admin.php?page=dcr-putnici'); ?>" <?php if(!isset($_GET['action'])) { echo 'class="current"'; } ?> aria-current="page">Putnici / Ugovaratelji</a> |</li>
        <li class="publish"><a href="<?php echo admin_url('admin.php?page=dcr-putnici&action=smece'); ?>" <?php if(isset($_GET['action']) && $_GET['action'] == 'smece') { echo 'class="current"'; } ?>>Smeće</a></li>
    </ul>
    <br class="clear">
    <div style="overflow-x: auto;">
        <table class="wp-list-table widefat striped" style="min-width: 1280px;">
            <thead>
            <tr>
                <th>Ime i prezime (ID)</th>
                <th>Tip</th>
                <th>Kontakt broj<br>Email</th>
                <th>Adresa</th>
                <th>OIB</th>
                <th>Spol</th>
                <th>Datum rođenja</th>
                <th>Vrsta isprave</th>
                <th>Broj isprave<br> Vrijedi do</th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ($putnici['result'] as $putnik) : ?>
                <tr>
                    <td class="title column-title page-title">
                        <div class="circle">
                            <strong>
                                <a class="row-title" href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=-1&sp=-1&su=-1&np=-1&od&do&q=' . $putnik->oib_putnika); ?>"><?php echo $putnik->ime; ?> <?php echo $putnik->prezime; ?> (<?php echo $putnik->id; ?>)</a>
                            </strong>
                        </div>
                        <div class="row-actions">
                            <span class="edit"><a href="<?php echo admin_url('admin.php?page=dcr-putnici&action=uredi&id=' . $putnik->id); ?>">Uredi</a></span>  |
                            <span class="trash">
                                <a href="<?php echo admin_url('admin.php?page=dcr-putnici&action=smece&id=' . $putnik->id); ?>" class="submitdelete">
                                    U smeće
                                </a>
                            </span>
                        </div>
                    </td>
                    <td><?php echo ucfirst($putnik->tip ?? ''); ?></td>
                    <td><?php echo $putnik->kontakt; ?><br><?php echo $putnik->email; ?></td>
                    <td><?php echo $putnik->adresa; ?><br><?php echo $putnik->pb; ?>, <?php echo $putnik->mjesto; ?></td>
                    <td><?php echo $putnik->oib_putnika; ?></td>
                    <td><?php echo $putnik->spol == 'musko' ? 'Muško' : 'Žensko'; ?></td>
                    <td><?php echo $putnik->rodendan; ?></td>
                    <td><?php echo ucfirst($putnik->vrsta_isprave ?? ''); ?></td>
                    <td><?php echo $putnik->broj_isprave; ?><br><?php echo $putnik->isprava_vrijedi; ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php
        $total_count = $putnici['total_count'];
        $total_pages = ceil($total_count / 50);
        if($total_pages > 1) : ?>
            <form id="filter" method="get">
                <input type="hidden" name="page" value="dcr-putnici">
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