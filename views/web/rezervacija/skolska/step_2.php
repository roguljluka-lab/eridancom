<?php isset($_SESSION['dc_first_step_data']) ? $dc_first_step_data = $_SESSION['dc_first_step_data'] : '' ?>
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" id="reservation_form">
    <input type="hidden" name="new_reservation" value="true">
    <input type="hidden" name="putovanje_id" value="<?php echo $putovanje->id; ?>">
    <input type="hidden" name="putovanje_naziv" value="<?php echo $putovanje->naziv; ?>">
    <input type="hidden" name="skola_id" value="<?php echo $putovanje->skola_id; ?>">
    <input type="hidden" name="dc_naziv_skole" value="<?php echo $putovanje->skola_naziv; ?>">
    <input type="hidden" name="dc_razred" id="dc_razred" value="">
    <div class="dc-row">
        <div class="dc-col-12 dc-px-0 dc-shadow">
            <div class="dc-row">
                <div class="dc-col-12">
                    <h2 class="dc-mb-4">
                        Upišite podatke i nastavite dalje...
                    </h2>
                    <h4 class="elementor-heading-title elementor-size-default dc-underline">
                        <span>
                            Razredni odjel
                        </span>
                    </h4>
                </div>
            </div>
            <div class="dc-row">
                <div class="dc-col-12">
                    <div class="dc-mb-3">
                        <label class="dc-label">Naziv putovanja</label>
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->naziv; ?>" readonly="readonly">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label class="dc-label">Šifra putovanja</label>
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->sifra; ?>" readonly="readonly">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label class="dc-label">Škola</label>
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->skola_naziv; ?>" readonly="readonly">
                    </div>
                </div>
            </div>
            <div class="dc-row">
                <div class="dc-col-6">
                    <label for="razred" class="dc-label">Razred *</label>
                    <select class="dc-form-control dc-mb-3 dc-select validate" id="razred" name="razred_id">
                        <option value="0" data-razrednik="Odaberi razred">Odaberi razred</option>
                        <?php foreach(json_decode($putovanje->razredi, true) as $razred) : ?>
                            <option value="<?php echo $razred['id']; ?>" data-razrednik="<?php echo $razred['razrednik']; ?>" <?php echo isset($dc_first_step_data) && $dc_first_step_data['razred_id'] == $razred['id'] ? 'selected' : ''; ?>><?php echo $razred['naziv']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="razrednik" class="dc-label">Razrednik</label>
                        <input type="text" class="dc-form-control" id="razrednik" name="razrednik" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['razrednik'] : ''; ?>" placeholder="Odaberi razred" readonly="readonly">
                    </div>
                </div>
            </div>
            <div class="dc-row dc-mt-4">
                <div class="dc-col-12">
                    <h4 class="elementor-heading-title elementor-size-default dc-underline">
                        <span>
                            Informacije o ugovaratelju
                        </span>
                    </h4>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="ime_ugovaratelja" class="dc-label">Ime ugovaratelja *</label>
                        <input type="text" name="ime_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['ime_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="ime_ugovaratelja" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="prezime_ugovaratelja" class="dc-label">Prezime ugovaratelja *</label>
                        <input type="text" name="prezime_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['prezime_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="prezime_ugovaratelja" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="kontakt_ugovaratelja" class="dc-label">Kontakt broj ugovaratelja *</label>
                        <input type="text" name="kontakt_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['kontakt_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="kontakt_ugovaratelja" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="email_ugovaratelja" class="dc-label">Email adresa ugovaratelja *</label>
                        <input type="text" name="email_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['email_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="email_ugovaratelja" placeholder="">
                    </div>
                </div>
            </div>
            <div class="dc-row dc-mt-4">
                <div class="dc-col-12">
                    <h4 class="elementor-heading-title elementor-size-default dc-underline">
                        <span>
                            Podaci o putniku
                        </span>
                    </h4>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="ime_putnika" class="dc-label">Ime putnika *</label>
                        <input type="text" name="ime_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['ime_putnika'] : ''; ?>" class="dc-form-control validate" id="ime_putnika" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="prezime_putnika" class="dc-label">Prezime putnika *</label>
                        <input type="text" name="prezime_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['prezime_putnika'] : ''; ?>" class="dc-form-control validate" id="prezime_putnika" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="adresa_putnika" class="dc-label">Adresa i kućni broj putnika *</label>
                        <input type="text" name="adresa_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['adresa_putnika'] : ''; ?>" class="dc-form-control validate" id="adresa_putnika" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="postanski_putnika" class="dc-label">Poštanski broj putnika *</label>
                        <input type="text" name="postanski_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['postanski_putnika'] : ''; ?>" class="dc-form-control validate" id="postanski_putnika" placeholder="" style="letter-spacing: 2px">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="mjesto_putnika" class="dc-label">Grad / mjesto putnika *</label>
                        <input type="text" name="mjesto_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['mjesto_putnika'] : ''; ?>" class="dc-form-control validate" id="mjesto_putnika" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="oib_putnika" class="dc-label">OIB putnika *</label>
                        <input type="text" name="oib_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['oib_putnika'] : ''; ?>" class="dc-form-control validate" id="oib_putnika" placeholder="" style="letter-spacing: 2px">
                    </div>
                </div>
                <div class="dc-col-12">
                    <hr class="dc-my-4">
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="rodenje_putnika" class="dc-label">Datum rođenja putnika *</label>
                        <input type="text" name="rodenje_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['rodenje_putnika'] : ''; ?>" class="dc-form-control validate" id="rodenje_putnika" placeholder="dd.mm.yyyy." style="letter-spacing: 2px">
                    </div>
                </div>
                <div class="dc-col-6">
                    <label for="spol_putnika" class="dc-label">Spol putnika *</label>
                    <select name="spol_putnika" class="dc-form-control dc-mb-3 dc-select validate" id="spol_putnika">
                        <option value="0">Odaberi spol putnika</option>
                        <option value="musko" <?php echo isset($dc_first_step_data) && $dc_first_step_data['spol_putnika'] == 'musko' ? 'selected' : ''; ?>>Muško</option>
                        <option value="zensko" <?php echo isset($dc_first_step_data) && $dc_first_step_data['spol_putnika'] == 'zensko' ? 'selected' : ''; ?>>Žensko</option>
                    </select>
                </div>
                <div class="dc-col-12">
                    <hr class="dc-my-4">
                </div>
                <div class="dc-col-6">
                    <div class="dc-mb-3">
                        <label for="kontakt_putnika" class="dc-label">Kontakt broj putnika</label>
                        <input type="text" name="kontakt_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['kontakt_putnika'] : ''; ?>" class="dc-form-control" id="kontakt_putnika" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6">
                    <label for="vrsta_isprave" class="dc-label">Vrsta putne isprave putnika *</label>
                    <select name="vrsta_isprave_putnika" class="dc-form-control dc-mb-3 dc-select validate" id="vrsta_isprave">
                        <option value="0">Odaberi vrstu putne isprave</option>
                        <option value="nema_putne_isprave" data-vrsta_isprave="nema_putne_isprave" <?php echo isset($dc_first_step_data) && $dc_first_step_data['vrsta_isprave_putnika'] == 'nema_putne_isprave' ? 'selected' : ''; ?>>Nema putne isprave</option>
                        <option value="osobna" data-vrsta_isprave="osobna" <?php echo isset($dc_first_step_data) && $dc_first_step_data['vrsta_isprave_putnika'] == 'osobna' ? 'selected' : ''; ?>>Osobna iskaznica</option>
                        <option value="putovnica" data-vrsta_isprave="putovnica" <?php echo isset($dc_first_step_data) && $dc_first_step_data['vrsta_isprave_putnika'] == 'putovnica' ? 'selected' : ''; ?>>Putovnica</option>
                    </select>
                </div>
                <div class="dc-col-6 putna_vrijedi_do_putnika_col">
                    <div class="dc-mb-3">
                        <label for="putna_vrijedi_do_putnika" class="dc-label">Putna isprava vrijedi do *</label>
                        <input type="text" name="putna_vrijedi_do_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['putna_vrijedi_do_putnika'] : ''; ?>" class="dc-form-control validate" id="putna_vrijedi_do_putnika" placeholder="" style="letter-spacing: 2px">
                    </div>
                </div>
                <div class="dc-col-6 broj_isprave_putnika_col">
                    <div class="dc-mb-3">
                        <label for="broj_isprave_putnika" class="dc-label">Broj putne isprave *</label>
                        <input type="text" name="broj_isprave_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['broj_isprave_putnika'] : ''; ?>" class="dc-form-control validate" id="broj_isprave_putnika" placeholder="">
                    </div>
                </div>
                <div class="dc-col-6 broj_zdravstvene_putnika_col" style="display: none;">
                    <div class="dc-mb-3">
                        <label for="broj_zdravstvene_putnika" class="dc-label">Broj zdravstvene iskaznice *</label>
                        <input type="text" name="broj_zdravstvene_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['broj_zdravstvene_putnika'] : ''; ?>" class="dc-form-control" id="broj_zdravstvene_putnika" placeholder="">
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