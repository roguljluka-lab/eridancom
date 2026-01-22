<div class="wrap">
    <h2>Razredi <small><?php echo $razredi[0]->skola_naziv; ?></small></h2>
    <br class="clear">
    <table class="wp-list-table widefat fixed striped">
        <thead>
        <tr>
            <th>Razred</th>
            <th>Školska godina</th>
            <th>Razrednik</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($razredi as $razred) : ?>
            <tr>
                <td><?php echo $razred->naziv; ?></td>
                <td>
                    <?php
                        $year1 = substr($razred->sk_godina, 0, 4);
                        $year2 = substr($razred->sk_godina, 4, 4);
                        echo $year1 . ". / " . $year2 . ".";
                    ?>
                </td>
                <td><?php echo $razred->razrednik; ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>