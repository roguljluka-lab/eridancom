<?php isset($_SESSION['dc_first_step_data']) ? $dc_first_step_data = $_SESSION['dc_first_step_data'] : '' ?>
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" id="reservation_form">
    <input type="hidden" name="new_reservation" value="true">
    <input type="hidden" name="putovanje_id" value="<?php echo $putovanje->id; ?>">
    <input type="hidden" name="putovanje_naziv" value="<?php echo $putovanje->naziv; ?>">
    <div class="dc-row">
        <div class="dc-col-12 dc-px-0 dc-shadow">
            <div class="dc-row">
                <div class="dc-col-12">
                    <h2 class="dc-mb-4">
                        Upišite podatke i nastavite dalje...
                    </h2>
                </div>
            </div>
            <div class="dc-row">
                <div class="dc-col-12">
                    <div class="dc-mb-3">
                        <label class="dc-label">Naziv putovanja</label>
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->naziv; ?>" readonly="readonly">
                    </div>
                </div>
            </div>
            <div class="dc-row dc-mt-4">
                <div class="dc-col-12">
                    <h4 class="elementor-heading-title elementor-size-default dc-underline">
                        <span>
                            Glavni putnik
                        </span>
                    </h4>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="ime_glavni" class="dc-label">Ime *</label>
                        <input type="text" name="ime_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['ime_glavni'] : ''; ?>" class="dc-form-control validate" id="ime_glavni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="prezime_glavni" class="dc-label">Prezime *</label>
                        <input type="text" name="prezime_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['prezime_glavni'] : ''; ?>" class="dc-form-control validate" id="prezime_glavni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="kontakt_glavni" class="dc-label">Kontakt broj *</label>
                        <input type="text" name="kontakt_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['kontakt_glavni'] : ''; ?>" class="dc-form-control validate" id="kontakt_glavni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="email_glavni" class="dc-label">Email adresa *</label>
                        <input type="text" name="email_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['email_glavni'] : ''; ?>" class="dc-form-control validate" id="email_glavni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="datum_glavni" class="dc-label">Datum rođenja *</label>
                        <input type="text" name="datum_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['datum_glavni'] : ''; ?>" class="dc-form-control validate" id="datum_glavni" placeholder="dd.mm.yyyy." style="letter-spacing: 2px">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="adresa_glavni" class="dc-label">Adresa i kućni broj *</label>
                        <input type="text" name="adresa_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['adresa_glavni'] : ''; ?>" class="dc-form-control validate" id="adresa_glavni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="postanski_glavni" class="dc-label">Poštanski broj *</label>
                        <input type="text" name="postanski_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['postanski_glavni'] : ''; ?>" class="dc-form-control validate" id="postanski_glavni" placeholder="" style="letter-spacing: 2px">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="mjesto_glavni" class="dc-label">Grad / mjesto *</label>
                        <input type="text" name="mjesto_glavni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['mjesto_glavni'] : ''; ?>" class="dc-form-control validate" id="mjesto_glavni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <label for="spol_glavni" class="dc-label">Spol *</label>
                    <select name="spol_glavni" class="dc-form-control dc-mb-3 dc-select validate" id="spol_glavni">
                        <option value="0">Odaberi spol putnika</option>
                        <option value="musko" <?php echo isset($dc_first_step_data) && $dc_first_step_data['spol_glavni'] == 'musko' ? 'selected' : ''; ?>>Muško</option>
                        <option value="zensko" <?php echo isset($dc_first_step_data) && $dc_first_step_data['spol_glavni'] == 'zensko' ? 'selected' : ''; ?>>Žensko</option>
                    </select>
                </div>
                <div class="dc-col-12">
                    <hr class="dc-my-4">
                </div>
            </div>
            <div class="dc-row">
                <div class="dc-col-12">
                    <h4 class="elementor-heading-title elementor-size-default dc-underline">
                        <span>
                            Dodatni putnik
                        </span>
                    </h4>
                    <p>Ako imate dodatnog putnika, sva polja su obavezna za upisati.</p>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="ime_dodatni" class="dc-label">Ime</label>
                        <input type="text" name="ime_dodatni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['ime_dodatni'] : ''; ?>" class="dc-form-control" id="ime_dodatni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="prezime_dodatni" class="dc-label">Prezime</label>
                        <input type="text" name="prezime_dodatni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['prezime_dodatni'] : ''; ?>" class="dc-form-control" id="prezime_dodatni" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="rodenje_dodatni" class="dc-label">Datum rođenja</label>
                        <input type="text" name="rodenje_dodatni" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['rodenje_dodatni'] : ''; ?>" class="dc-form-control" id="rodenje_dodatni" placeholder="dd.mm.yyyy." style="letter-spacing: 2px">
                    </div>
                </div>

                <div class="dc-col-12">
                    <div class="dc-mb-3">
                        <label for="informacije" class="dc-label">Dodatne informacije / poruka</label>
                        <textarea name="informacije" id="informacije" value="<?php echo isset($dc_first_step_data) ? nl2br($dc_first_step_data['informacije']) : ''; ?>" cols="30" rows="6"></textarea>
                    </div>
                </div>
            </div>
            <div class="dc-row mandatory" style="display: none;">
                <div class="dc-col-12">
                    <p class="dc-alert dc-alert-danger">Polja označena crvenom bojom su obavezna. Molimo da ih popunite.</p>
                </div>
            </div>
            <div class="dc-row dc-mt-4">
                <div class="dc-col-6 dc-mb-3">
                    <a href="<?php echo get_permalink(); ?>?act=odustani" class="dc-button dc-button-warning" id="odustani_btn">Odustani</a>
                </div>
                <div class="dc-col-6">
                    <a class="dc-button dc-button-info" id="reservation_button" style="width: 100%;">
                        Sljedeći korak
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>