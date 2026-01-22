<?php // IZLETI
global $wpdb; // IZLETI

$putovanja_table = $wpdb->prefix . 'dcr_putovanja'; // IZLETI
$skole_table = $wpdb->prefix . 'dcr_skole'; // IZLETI
$razredi_table = $wpdb->prefix . 'dcr_razredi'; // IZLETI
$connection_table = $wpdb->prefix . 'dcr_connection'; // IZLETI
$current_date    = wp_date('Y-m-d'); // IZLETI

// IZLETI
// sva aktivna buduća putovanja - izleti // IZLETI
$putovanja = $wpdb->get_results( // IZLETI
    $wpdb->prepare( // IZLETI
        "SELECT p.id, p.sifra, p.naziv, p.putovanje_od, p.putovanje_do,
            s.naziv AS skola_naziv,
            GROUP_CONCAT(r.naziv ORDER BY r.naziv SEPARATOR ', ') AS razredi_naziv
         FROM {$putovanja_table} p
         LEFT JOIN {$connection_table} ct ON p.id = ct.dcr_table_id AND ct.dcr_table = %s
         LEFT JOIN {$razredi_table} r ON ct.belongs_to = r.id
         LEFT JOIN {$skole_table} s ON p.skola_id = s.id
         WHERE p.status = %d AND p.putovanje_do >= %s AND p.type = %s
         GROUP BY p.id
         ORDER BY p.putovanje_od ASC", // IZLETI
        $putovanja_table, // IZLETI
        1, // IZLETI
        $current_date, // IZLETI
        'izlet' // IZLETI
    ) // IZLETI
); // IZLETI
?> <!-- IZLETI -->
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post"> <!-- IZLETI -->
    <div class="dc-row"> <!-- IZLETI -->
        <div class="dc-col-12"> <!-- IZLETI -->
            <?php include_once (DC_REZERVACIJE_PATH . 'views/errors/errors.php'); ?> <!-- IZLETI -->

            <label for="putovanje_id" class="dc-label"> <!-- IZLETI -->
                Odaberite izlet <!-- IZLETI -->
            </label> <!-- IZLETI -->

            <select name="putovanje_id" id="putovanje_id" class="dc-form-control" required> <!-- IZLETI -->
                <option value="">-- Odaberite izlet --</option> <!-- IZLETI -->

                <?php if ( ! empty( $putovanja ) ) : ?> <!-- IZLETI -->
                    <?php foreach ( $putovanja as $p ) : ?> <!-- IZLETI -->
                        <?php
                        $skola_naziv = $p->skola_naziv ?? ''; // IZLETI
                        $razredi_naziv = $p->razredi_naziv ?? ''; // IZLETI
                        $skola_razred = ''; // IZLETI
                        if ( ! empty( $skola_naziv ) && ! empty( $razredi_naziv ) ) { // IZLETI
                            $skola_razred = $skola_naziv . ', ' . $razredi_naziv; // IZLETI
                        } elseif ( ! empty( $skola_naziv ) ) { // IZLETI
                            $skola_razred = $skola_naziv; // IZLETI
                        } elseif ( ! empty( $razredi_naziv ) ) { // IZLETI
                            $skola_razred = $razredi_naziv; // IZLETI
                        } // IZLETI

                        $label_parts = array_filter( array( // IZLETI
                            $skola_razred, // IZLETI
                            $p->naziv, // IZLETI
                            $p->sifra, // IZLETI
                        ) ); // IZLETI
                        $label = strtoupper( implode( ' - ', $label_parts ) ); // IZLETI
                        ?> <!-- IZLETI -->
                        <option value="<?php echo esc_attr( $p->id ); ?>"> <!-- IZLETI -->
                            <?php echo esc_html( $label ); ?> <!-- IZLETI -->
                        </option> <!-- IZLETI -->
                    <?php endforeach; ?> <!-- IZLETI -->
                <?php endif; ?> <!-- IZLETI -->
            </select> <!-- IZLETI -->
        </div> <!-- IZLETI -->
    </div> <!-- IZLETI -->
    <div class="dc-row"> <!-- IZLETI -->
        <div class="dc-col-6">&nbsp;</div> <!-- IZLETI -->
        <div class="dc-col-6"> <!-- IZLETI -->
            <button type="submit" class="nd_travel_width_100_percentage nd_travel_border_width_0 nd_travel_cursor_pointer nd_travel_margin_top_10 nd_travel_font_family_poppins nd_travel_letter_spacing_1_important dc-mt-3 "> <!-- IZLETI -->
                Sljedeći korak <!-- IZLETI -->
            </button> <!-- IZLETI -->
        </div> <!-- IZLETI -->
    </div> <!-- IZLETI -->
</form> <!-- IZLETI -->
