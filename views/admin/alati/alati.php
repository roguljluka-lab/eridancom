<?php
?>

<div class="wrap">
    <h1>DC rezervacije – Alati</h1>
    <p>Provjera rezervacija bez izdane ponude u eRačunima u zadnjih 7 dana.</p>

    <form method="post">
        <?php wp_nonce_field('dcr_tools_check_offers', 'dcr_tools_nonce'); ?>
        <p>
            <input type="submit" name="dcr_tools_check_offers" class="button button-primary" value="Provjeri ponude">
        </p>
    </form>

    <?php if (!empty($report)) : ?>
        <div class="notice notice-info">
            <p><strong>Rezultat provjere:</strong></p>
            <ul>
                <li>Pronađeno rezervacija bez ponude: <?php echo esc_html(count($report['found_ids'])); ?></li>
                <li>Uspješno kreirane ponude: <?php echo esc_html(count($report['created_ids'])); ?></li>
                <li>Preskočeno (ponuda već postoji): <?php echo esc_html(count($report['skipped_ids'])); ?></li>
                <li>Neuspješno: <?php echo esc_html(count($report['failed'])); ?></li>
            </ul>
        </div>

        <?php if (!empty($report['created_ids'])) : ?>
            <h3>Kreirane ponude</h3>
            <p><?php echo esc_html(implode(', ', $report['created_ids'])); ?></p>
        <?php endif; ?>

        <?php if (!empty($report['skipped_ids'])) : ?>
            <h3>Preskočeno</h3>
            <p><?php echo esc_html(implode(', ', $report['skipped_ids'])); ?></p>
        <?php endif; ?>

        <?php if (!empty($report['failed'])) : ?>
            <h3>Neuspješno</h3>
            <ul>
                <?php foreach ($report['failed'] as $reservation_id => $message) : ?>
                    <li>
                        <?php echo esc_html($reservation_id . ': ' . $message); ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    <?php endif; ?>
</div>
