<form action="#" method="post" id="dc_payment_form">
    <div class="dc-row">
        <div class="dc-col-12">
            <?php include_once (DC_REZERVACIJE_PATH . 'views/errors/errors.php'); ?>
        </div>
    </div>
    <div class="dc-row">
        <div class="dc-col-12">
            <h2>Izvršite novu uplatu</h2>
            <p>Možete izvršiti uplatu sredstava online kreditnom karticom. Upišite broj rezervacije i željeni iznos za uplatu.</p>
            <label for="reservation_id" class="dc-label">
                Broj rezervacije
            </label>
            <input type="text" class="dc-form-control validate" name="reservation_id" id="reservation_id" placeholder="Upišite broj rezervacije" value="<?php echo $_GET['rid'] ?? ''; ?>">
        </div>
    </div>
    <div class="dc-row dc-mt-3">
        <div class="dc-col-12">
            <label for="amount" class="dc-label">
                Iznos za uplatu (€)
            </label>
            <input type="text" class="dc-form-control validate" name="amount" id="amount" value="<?php echo $_GET['iznos'] ?? ''; ?>">
        </div>
    </div>
    <div class="dc-row dc-mt-3">
        <div class="dc-col-12">
            <div class="dc-scrollbox">
                <?php echo $settings->dc_postavke->opci_uvjeti ? nl2br($settings->dc_postavke->opci_uvjeti) : ''; ?>
            </div>
        </div>
    </div>
    <div class="dc-row dc-mt-3">
        <div class="dc-col-12">
            <label class="dc-chck-custom-checkbox" for="privola_3">
                <input type="checkbox" class="validate" id="privola_3">
                <span class="dc-chck-checkmark"></span>
                <span class="dc-chck-label-text">Pročitao/la sam i slažem se sa <a href="https://eridan.hr/opci-uvjeti/" target="_blank">općim uvjetima korištenja</a> web stranice eridan.hr.</span>
            </label>
        </div>
    </div>
    <div class="dc-row">
        <div class="dc-col-6">&nbsp;</div>
        <div class="dc-col-6">
            <button type="submit" class="nd_travel_width_100_percentage nd_travel_border_width_0 nd_travel_cursor_pointer nd_travel_margin_top_10 nd_travel_font_family_poppins nd_travel_letter_spacing_1_important dc-mt-3 ">
                Izvrši uplatu
            </button>
        </div>
    </div>
</form>