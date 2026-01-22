<?php // IZLETI
isset($_SESSION['dc_first_step_data']) ? $dc_first_step_data = $_SESSION['dc_first_step_data'] : ''; // IZLETI
?> <!-- IZLETI -->
<form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" id="reservation_form"> <!-- IZLETI -->
    <input type="hidden" name="new_reservation" value="true"> <!-- IZLETI -->
    <input type="hidden" name="putovanje_id" value="<?php echo $putovanje->id; ?>"> <!-- IZLETI -->
    <input type="hidden" name="putovanje_naziv" value="<?php echo $putovanje->naziv; ?>"> <!-- IZLETI -->
    <input type="hidden" name="skola_id" value="<?php echo $putovanje->skola_id; ?>"> <!-- IZLETI -->
    <input type="hidden" name="dc_naziv_skole" value="<?php echo $putovanje->skola_naziv; ?>"> <!-- IZLETI -->
    <input type="hidden" name="dc_razred" id="dc_razred" value=""> <!-- IZLETI -->
    <input type="hidden" name="razrednik" value="RAZREDNIK"> <!-- IZLETI -->
    <div class="dc-row"> <!-- IZLETI -->
        <div class="dc-col-12 dc-px-0 dc-shadow"> <!-- IZLETI -->
            <div class="dc-row"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <h2 class="dc-mb-4"> <!-- IZLETI -->
                        Upišite podatke i nastavite dalje... <!-- IZLETI -->
                    </h2> <!-- IZLETI -->
                    <h4 class="elementor-heading-title elementor-size-default dc-underline"> <!-- IZLETI -->
                        <span> <!-- IZLETI -->
                            Izlet <!-- IZLETI -->
                        </span> <!-- IZLETI -->
                    </h4> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label class="dc-label">Naziv izleta</label> <!-- IZLETI -->
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->naziv; ?>" readonly="readonly"> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label class="dc-label">Šifra izleta</label> <!-- IZLETI -->
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->sifra; ?>" readonly="readonly"> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label class="dc-label">Škola</label> <!-- IZLETI -->
                        <input type="text" class="dc-form-control" disabled="disabled" placeholder="<?php echo $putovanje->skola_naziv; ?>" readonly="readonly"> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row"> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <label for="razred" class="dc-label">Razred *</label> <!-- IZLETI -->
                    <select class="dc-form-control dc-mb-3 dc-select validate" id="razred" name="razred_id"> <!-- IZLETI -->
                        <option value="0">Odaberi razred</option> <!-- IZLETI -->
                        <?php $razredi = json_decode($putovanje->razredi, true); ?> <!-- IZLETI -->
                        <?php if (!empty($razredi)) : ?> <!-- IZLETI -->
                            <?php foreach($razredi as $razred) : ?> <!-- IZLETI -->
                                <option value="<?php echo $razred['id']; ?>" <?php echo isset($dc_first_step_data) && $dc_first_step_data['razred_id'] == $razred['id'] ? 'selected' : ''; ?>><?php echo $razred['naziv']; ?></option> <!-- IZLETI -->
                            <?php endforeach; ?> <!-- IZLETI -->
                        <?php endif; ?> <!-- IZLETI -->
                    </select> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row dc-mt-4"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <h4 class="elementor-heading-title elementor-size-default dc-underline"> <!-- IZLETI -->
                        <span> <!-- IZLETI -->
                            Informacije o ugovaratelju <!-- IZLETI -->
                        </span> <!-- IZLETI -->
                    </h4> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="ime_ugovaratelja" class="dc-label">Ime ugovaratelja *</label> <!-- IZLETI -->
                        <input type="text" name="ime_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['ime_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="ime_ugovaratelja" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="prezime_ugovaratelja" class="dc-label">Prezime ugovaratelja *</label> <!-- IZLETI -->
                        <input type="text" name="prezime_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['prezime_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="prezime_ugovaratelja" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="kontakt_ugovaratelja" class="dc-label">Kontakt broj ugovaratelja *</label> <!-- IZLETI -->
                        <input type="text" name="kontakt_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['kontakt_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="kontakt_ugovaratelja" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="email_ugovaratelja" class="dc-label">Email adresa ugovaratelja *</label> <!-- IZLETI -->
                        <input type="text" name="email_ugovaratelja" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['email_ugovaratelja'] : ''; ?>" class="dc-form-control validate" id="email_ugovaratelja" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row dc-mt-4"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <h4 class="elementor-heading-title elementor-size-default dc-underline"> <!-- IZLETI -->
                        <span> <!-- IZLETI -->
                            Podaci o putniku <!-- IZLETI -->
                        </span> <!-- IZLETI -->
                    </h4> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="ime_putnika" class="dc-label">Ime putnika *</label> <!-- IZLETI -->
                        <input type="text" name="ime_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['ime_putnika'] : ''; ?>" class="dc-form-control validate" id="ime_putnika" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="prezime_putnika" class="dc-label">Prezime putnika *</label> <!-- IZLETI -->
                        <input type="text" name="prezime_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['prezime_putnika'] : ''; ?>" class="dc-form-control validate" id="prezime_putnika" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="adresa_putnika" class="dc-label">Adresa i kućni broj putnika *</label> <!-- IZLETI -->
                        <input type="text" name="adresa_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['adresa_putnika'] : ''; ?>" class="dc-form-control validate" id="adresa_putnika" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="postanski_putnika" class="dc-label">Poštanski broj putnika *</label> <!-- IZLETI -->
                        <input type="text" name="postanski_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['postanski_putnika'] : ''; ?>" class="dc-form-control validate" id="postanski_putnika" placeholder="" style="letter-spacing: 2px"> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="mjesto_putnika" class="dc-label">Grad / mjesto putnika *</label> <!-- IZLETI -->
                        <input type="text" name="mjesto_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['mjesto_putnika'] : ''; ?>" class="dc-form-control validate" id="mjesto_putnika" placeholder=""> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="oib_putnika" class="dc-label">OIB putnika *</label> <!-- IZLETI -->
                        <input type="text" name="oib_putnika" value="<?php echo isset($dc_first_step_data) ? $dc_first_step_data['oib_putnika'] : ''; ?>" class="dc-form-control validate" id="oib_putnika" placeholder="" style="letter-spacing: 2px"> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <div class="dc-mb-3"> <!-- IZLETI -->
                        <label for="informacije" class="dc-label">Dodatne informacije / poruka</label> <!-- IZLETI -->
                        <textarea name="informacije" id="informacije" value="<?php echo isset($dc_first_step_data) ? nl2br($dc_first_step_data['informacije']) : ''; ?>" cols="30" rows="6"></textarea> <!-- IZLETI -->
                    </div> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row mandatory" style="display: none;"> <!-- IZLETI -->
                <div class="dc-col-12"> <!-- IZLETI -->
                    <p class="dc-alert dc-alert-danger">Polja označena crvenom bojom su obavezna. Molimo da ih popunite.</p> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
            <div class="dc-row dc-mt-4"> <!-- IZLETI -->
                <div class="dc-col-6 dc-mb-3"> <!-- IZLETI -->
                    <a href="<?php echo get_permalink(); ?>?act=odustani" class="dc-button dc-button-warning" id="odustani_btn">Odustani</a> <!-- IZLETI -->
                </div> <!-- IZLETI -->
                <div class="dc-col-6"> <!-- IZLETI -->
                    <a class="dc-button dc-button-info" id="reservation_button" style="width: 100%;"> <!-- IZLETI -->
                        Sljedeći korak <!-- IZLETI -->
                    </a> <!-- IZLETI -->
                </div> <!-- IZLETI -->
            </div> <!-- IZLETI -->
        </div> <!-- IZLETI -->
    </div> <!-- IZLETI -->
</form> <!-- IZLETI -->
