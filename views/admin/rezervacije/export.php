<?php $_SESSION['dc_admin_success'] = 'CSV datoteka je uspješno preuzeta.'; ?>
<form id="filter" method="get" style="display: none;">
    <div class="wrap">
        <h2>Rezervacije export</h2>
        <br class="clear">
        <div style="overflow-x: auto;">
            <table class="wp-list-table widefat striped" id="dc_data_table">
                <thead>
                <tr>
                    <th>ID rezervacije</th>
                    <th>Datum rezervacije</th>
                    <th>Status rezervacije</th>
                    <th>Naziv putovanja</th>
                    <th>Šifra putovanja</th>
                    <th>Broj ponude</th>
                    <th>Uplaćeno</th>
                    <th>Preostalo</th>
                    <th>Putnik</th>
                    <th>Ugovaratelj</th>
                    <th>Škola</th>
                    <th>Razred</th>
                    <th>Način plaćanja</th>
                    <th>Status uplate</th>
                    <th>Školska godina</th>
                    <th>Kontakt broj</th>
                    <th>OIB putnika</th>
                    <th>Adresa</th>
                    <th>Poštanski broj</th>
                    <th>Mjesto</th>
                    <th>Spol</th>
                    <th>Datum rođenja</th>
                    <th>Vrsta isprave</th>
                    <th>Broj isprave</th>
                    <th>Isprava vrijedi do</th>
                    <th>Datum rezervacije</th>
                    <th>Kontakt ugovaratelja</th>
                    <th>Email ugovaratelja</th>
                    <th>Iznos putovanja (akontacija)</th>
                    <th>Iznos putovanja (gotovina)</th>
                    <th>Iznos putovanja (kartica)</th>
                    <th>Razrednik</th>
                    <th>Informacije</th>
                    <th>Admin info</th>
                </tr>
                </thead>
                <tbody>
                <?php if(count($rezervacije) > 0) : ?>

                    <?php foreach ($rezervacije['result'] as $rezervacija) : ?>
                    <?php
                        $ukupni_iznos_putovanja = $rezervacija->nacin_placanja == 5 ? $rezervacija->putovanje_ukupni_iznos_kartica : $rezervacija->putovanje_ukupni_iznos;
                        $uplaceno = $AdminClass->get_reservation_total_paid($rezervacija->id) ?? 0;
                        $preostalo = $ukupni_iznos_putovanja - $uplaceno;
                    ?>
                        <tr>
                            <td><?php echo $rezervacija->id; ?></td>
                            <td><?php echo date('d.m.Y. H:i', strtotime($rezervacija->created_at)); ?></td>
                            <td>
                                <?php if($rezervacija->smece == 0) : ?>
                                    <?php echo $AdminClass->potvrdena_rezervacija($rezervacija) == 'circle-green' ? 'Plaćeno' : ($AdminClass->potvrdena_rezervacija($rezervacija) == 'circle-red' ? 'Isteklo' : 'Rezervirano'); ?>
                                <?php else : ?>
                                    Izbrisano
                                <?php endif; ?>
                            </td>
                            <td><?php echo $rezervacija->putovanje_naziv; ?></td>
                            <td><?php echo $rezervacija->putovanje_sifra; ?></td>
                            <td><?php echo $rezervacija->broj_eracuni_ponude; ?></td>
                            <td><?php echo $AdminClass->get_reservation_total_paid($rezervacija->id); ?></td>
                            <td><?php echo $preostalo; ?></td>
                            <td><?php echo $rezervacija->putnik_ime . ' ' . $rezervacija->putnik_prezime; ?></td>
                            <td><?php echo $rezervacija->ugovaratelj_ime . ' ' . $rezervacija->ugovaratelj_prezime; ?></td>
                            <td><?php echo $rezervacija->skola_naziv; ?></td>
                            <td><?php echo $rezervacija->razred_naziv; ?></td>
                            <td><?php echo $AdminClass->get_payment_status($rezervacija->nacin_placanja); ?></td>
                            <td><?php echo $AdminClass->get_status($rezervacija->status); ?></td>
                            <td><?php echo substr($rezervacija->razred_sk_godina, 0, 4) . '. / ' . substr($rezervacija->razred_sk_godina, 4, 4) . '.'; ?></td>
                            <td><?php echo $rezervacija->putnik_kontakt; ?></td>
                            <td><?php echo $rezervacija->putnik_oib; ?></td>
                            <td><?php echo $rezervacija->putnik_adresa; ?></td>
                            <td><?php echo $rezervacija->putnik_pb; ?></td>
                            <td><?php echo $rezervacija->putnik_mjesto; ?></td>
                            <td><?php echo $rezervacija->putnik_spol = 'musko' ? 'Muško' : 'Žensko'; ?></td>
                            <td><?php echo $rezervacija->putnik_rodendan; ?></td>
                            <td><?php echo ucfirst($rezervacija->putnik_vrsta_isprave); ?></td>
                            <td><?php echo $rezervacija->putnik_broj_isprave; ?></td>
                            <td><?php echo $rezervacija->putnik_isprava_vrijedi; ?></td>
                            <td><?php echo date('d.m.Y. H:i', strtotime($rezervacija->created_at)); ?></td>
                            <td><?php echo $rezervacija->ugovaratelj_kontakt; ?></td>
                            <td><?php echo $rezervacija->ugovaratelj_email; ?></td>
                            <td><?php echo $rezervacija->putovanje_akontacija; ?> €</td>
                            <td><?php echo $rezervacija->putovanje_ukupni_iznos; ?> €</td>
                            <td><?php echo $rezervacija->putovanje_ukupni_iznos_kartica; ?> €</td>
                            <td><?php echo $rezervacija->razred_razrednik; ?></td>
                            <td><?php echo $rezervacija->informacije; ?></td>
                            <td><?php echo $rezervacija->admin_info; ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
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

    exportToCSV('dc_data_table', 'Rezervacije ' + getCurrentDate() + '.csv')
</script>