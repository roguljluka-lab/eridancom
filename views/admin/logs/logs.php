<style>
    #setting-error-tgmpa {
        display: none !important;
    }
    pre {
        display: none;
    }
    .dc_btn_log_sh {
        font-weight: 700;
        cursor: pointer;
    }
</style>

<?php include_once (DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>

<div class="wrap">
    <h1 class="wp-heading-inline">DevLogs</h1>
    <hr class="wp-header-end">
    <br class="clear">
    <form method="get" style="margin-bottom: 1em;">
        <input type="hidden" name="page" value="dcr-devlogs">
        <input type="text" name="q" placeholder="Upišite ID rezervacije" value="<?php echo isset($_GET['q']) ? $_GET['q'] : ''; ?>" autocomplete="off">
        <button class="button" type="submit">Filtriraj</button>
    </form>
    <?php echo 'Ukupno ' . $logs['total_count'] . ' redova'; ?>

    <div style="overflow-x: auto;">
        <table class="wp-list-table widefat striped" style="min-width: 1280px;">
            <thead>
            <tr>
                <th style="width: 100px">ID / Datum</th>
                <th>LOG</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($logs['result'] as $log) : ?>
                <?php
                    $json_start = strpos($log->log, '{');
                    $prefix = trim(substr($log->log, 0, $json_start));
                    $json_str = substr($log->log, $json_start);
                    $data = json_decode($json_str, true);
                ?>

                <tr>
                    <td>
                        <a href="<?php echo admin_url('admin.php?page=dcr&action=uredi&id=' . $log->table_id); ?>">
                            <b># <?php echo $log->table_id; ?></b>
                        </a>
                        <?php echo date('d.m.Y. H:i:s', strtotime($log->created_at)); ?>
                    </td>
                    <td>
                        <?php
                        echo !empty($log->username) ? '<b>' . $log->username . ' - </b>' : '';
                        if($data) {

                            echo '<span class="dc_btn_log_sh">' . $prefix . '</span><br>';

                            $printed_value = false;
                            foreach ($data as $key => $value) {
                                if (is_array($value)) {
                                    echo '<pre style="line-height: 1.2em;">';
                                    echo str_replace("    ", "  ", print_r($value, true));
                                    echo '</pre>';
                                    $printed_value = true;
                                }
                            }
                            
                            if (!$printed_value) {
                                echo '<pre style="line-height: 1.2em;">';
                                echo str_replace("    ", "  ", print_r($data, true));
                                echo '</pre>';
                                $printed_value = false;
                            }
                            
                        } else {
                            echo '<span class="dc_btn_log_sh">' . $log->log . '</span><br>';
                        }
                        ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <div class="clear"></div>
        <?php
        $total_count = $logs['total_count'];
        $total_pages = ceil($total_count / 20);
        if($total_pages > 1) : ?>
            <form id="filter" method="get">
                <input type="hidden" name="page" value="dcr-devlogs">
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
    </div>