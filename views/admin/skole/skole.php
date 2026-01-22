<div class="wrap">
    <h2>Škole i razredi</h2>
    <br class="clear">
    <div style="overflow-x: auto;">
        <table class="wp-list-table widefat fixed striped" style="min-width: 680px;">
            <thead>
            <tr>
                <th>Naziv</th>
                <th>Adresa</th>
                <th>Mjesto</th>
                <th>Datum</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($skole as $skola) : ?>
                <tr>
                    <td><strong><a href="<?php echo admin_url('admin.php?page=dcr-skole&skola_id=' . $skola->id); ?>"><?php echo $skola->naziv; ?></a></strong></td>
                    <td><?php echo $skola->adresa_skole; ?></td>
                    <td><?php echo $skola->post_skole . ' ' . $skola->mjesto_skole; ?></td>
                    <td><?php echo date('d.m.Y. H:i', strtotime($skola->created_at)); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>