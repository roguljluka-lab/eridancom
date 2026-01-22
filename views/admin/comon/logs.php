<table class="wp-list-table widefat striped dc_data_table">
    <tbody>
    <tr>
        <td colspan="2">
            <h2 style="margin: 0; text-align: center">LOGOVI</h2>
        </td>
    </tr>
    <?php foreach($logs as $log) : ?>
        <tr>
            <td colspan="2">
                <?php
                echo !empty($log->username) ? '<b>' . $log->username . ' - </b>' : '';
                echo date('d.m.Y. H:i', strtotime($log->created_at)) . '<br>' . $log->log;
                ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>