<?php $_SESSION['dc_admin_success'] = 'CSV datoteka je uspješno preuzeta.'; ?>
<form id="filter" method="get" style="display: none;">
    <div class="wrap">
        <h2>Uplate export</h2>
        <br class="clear">
        <div style="overflow-x: auto;">
            <table class="wp-list-table widefat striped" id="dc_data_table">
                <thead>
                <tr>
                    <th style="width: 80px;"># rezervacije</th>
                    <th style="width: 180px;">Putnik</th>
                    <th style="width: 180px;">Ugovaratelj</th>
                    <th style="width: 180px;">Broj eračuni dokumenta</th>
                    <th style="width: 80px;">Uplaćeno</th>
                    <th style="width: 80px;">Iznos putovanja</th>
                    <th style="width: 180px;"><center>Datum uplate</center></th>
                    <th>Naziv putovanja (šifra)</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($payments as $payment) {
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
                        </tr>
                    <?php } } ?>
                </tbody>
            </table>
        </div>
    </div>
</form>
<script>
    function exportToCSV(tableId, filename) {
        const csvRows = [];
        const table = document.getElementById(tableId);
        const rows = table.querySelectorAll('tr');

        rows.forEach(row => {
            const csvRow = [];
            const cells = row.querySelectorAll('td, th');

            cells.forEach(cell => {
                // Get the text content of the cell
                let cellContent = cell.textContent.trim();

                // Replace newlines with spaces or explicitly encode them
                cellContent = cellContent.replace(/\r?\n|\r/g, ' '); // Replace newlines with a space

                // Check if cell content contains commas or quotes
                if (cellContent.includes(',') || cellContent.includes('"')) {
                    // Wrap cell content within double quotes and escape existing quotes
                    cellContent = '"' + cellContent.replace(/"/g, '""') + '"';
                }

                csvRow.push(cellContent);
            });
            csvRows.push(csvRow.join(','));
        });

        const csvContent = '\uFEFF' + csvRows.join('\n'); // Prepend BOM to ensure UTF-8 encoding
        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        if (navigator.msSaveBlob) { // IE 10+
            navigator.msSaveBlob(blob, filename);
        } else {
            const link = document.createElement('a');
            if (link.download !== undefined) {
                const url = URL.createObjectURL(blob);
                link.setAttribute('href', url);
                link.setAttribute('download', filename);
                link.style.visibility = 'hidden';
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
            }
        }

        // Redirect to the new page after initiating the download
        window.location.href = document.referrer;
    }


    function getCurrentDate() {
        const now = new Date();
        const year = now.getFullYear();
        const month = String(now.getMonth() + 1).padStart(2, '0');
        const day = String(now.getDate()).padStart(2, '0');
        const hours = String(now.getHours()).padStart(2, '0');
        const minutes = String(now.getMinutes()).padStart(2, '0');

        return day + '.' + month + '.' + year + '. ' + hours + '.' + minutes;
    }

    exportToCSV('dc_data_table', 'Uplate ' + getCurrentDate() + '.csv')
</script>