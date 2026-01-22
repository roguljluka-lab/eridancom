<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post">
    <div class="dc-row">
        <div class="dc-col-12">
            <?php include_once (DC_REZERVACIJE_PATH . 'views/errors/errors.php'); ?>

            <label for="sifra" class="dc-label">
                Šifra putovanja
            </label>
            <input type="text" class="dc-form-control" name="sifra" id="sifra" placeholder="Upišite šifru putovanja" value="<?php echo $_GET['sifra'] ?? ''; ?>">
        </div>
    </div>
    <div class="dc-row">
        <div class="dc-col-6">&nbsp;</div>
        <div class="dc-col-6">
            <button type="submit" class="nd_travel_width_100_percentage nd_travel_border_width_0 nd_travel_cursor_pointer nd_travel_margin_top_10 nd_travel_font_family_poppins nd_travel_letter_spacing_1_important dc-mt-3 ">
                Sljedeći korak
            </button>
        </div>
    </div>
</form>