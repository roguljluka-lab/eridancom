<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>

<?php include_once (DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>

<div class="wrap">

    <h1 class="wp-heading-inline">Sve uplate</h1>
    <hr class="wp-header-end">
    <br class="clear">

    <form id="filter" method="get">
        <input type="hidden" name="page" value="dcr-uplate">
        <input type="text" name="q" placeholder="Upišite pojam" value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>" autocomplete="off">
        <button class="button" type="submit">Filtriraj</button>
        <p>Filtrirajte da biste mogli ukloniti uplatu iz rezervacije.</p>
    </form>

    <form method="get" style="margin-bottom: 2.5rem;">
        <div class="actions bulkactions dc_collapse">
            <span class="dc-float-right">
                <a href="<?php echo admin_url('admin.php?page=dcr-uplate&export=csv'); ?>" class="page-title-action">Izvezi u excel</a>
            </span>
        </div>
    </form>

    <br class="clear">

    <div style="overflow-x: auto;">
        <table class="wp-list-table widefat striped" style="min-width: 800px;">
            <thead>
            <tr>
                <th style="width: 80px;"># rezervacije</th>
                <th style="width: 180px;">Putnik</th>
                <th style="width: 180px;">Ugovaratelj</th>
                <th style="width: 180px;">Broj eračuni dokumenta</th>
                <th style="width: 120px;">Uplaćeno</th>
                <th style="width: 120px;">Iznos putovanja</th>
                <th style="width: 180px;"><center>Datum uplate</center></th>
                <th>Naziv putovanja (šifra)</th>
                <?php if(isset($_GET['q']) && !empty($_GET['q'])) : ?>
                    <th>Ukloni</th>
                <?php endif; ?>
            </tr>
            </thead>

            <tbody>

            <?php foreach ($payments['result'] as $payment) {
                if($payment->putovanje_id > 0) {
                    ?>

                    <tr>
                        <td>
                            <a href="<?php echo admin_url('admin.php?page=dcr&action=uredi&id=' . $payment->reservation_id); ?>">
                                # <?php echo $payment->reservation_id; ?>
                            </a>
                        </td>
                        <td>
                            <?php echo $payment->putnik_ime . ' ' . $payment->putnik_prezime; ?>
                        </td>
                        <td>
                            <?php echo $payment->ime . ' ' . $payment->prezime; ?>
                        </td>
                        <td>
                            <?php echo $payment->invoice_number; ?>
                        </td>
                        <td style="text-align: right; padding-right: 35px;">
                            <?php echo $payment->paymentAmount; ?> €
                        </td>
                        <td style="text-align: right; padding-right: 35px;">
                            <?php
                            $iznos_putovanja = $payment->nacin_placanja == 5 ? $payment->ukupni_iznos_kartica : $payment->ukupni_iznos;
                            echo $iznos_putovanja;
                            ?> €
                        </td>
                        <td>
                            <center><?php echo date('d.m.Y.', strtotime($payment->paymentDate)); ?></center>
                        </td>
                        <td class="title column-title page-title">
                            <a href="<?php echo admin_url('admin.php?page=dcr&filter=res&or=rezervacije.id&so=desc&sg=-1&s=-1&r=-1&p=' . $payment->putovanje_id . '&sp=-1&su=-1&np=-1&od&do&q='); ?>">
                                <?php echo $payment->naziv; ?> (<?php echo $payment->sifra; ?>)
                            </a>
                        </td>

                        <?php if(isset($_GET['q']) && !empty($_GET['q'])) : ?>
                            <td>
                                <span class="dashicons dashicons-trash remove_payment" data-payment-id="<?php echo $payment->id; ?>" data-reservation-id="<?php echo $payment->reservation_id; ?>" data-amount="<?php echo $payment->paymentAmount; ?>" style="color: red; cursor: pointer;"></span>
                            </td>
                        <?php endif; ?>

                    </tr>
                <?php } } ?>
            </tbody>
        </table>
    </div>
    <?php
    $total_count = $payments['total_count'];
    $total_pages = ceil($total_count / 50);
    if($total_pages > 1) : ?>
        <form id="filter" method="get">
            <input type="hidden" name="page" value="dcr-uplate">
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