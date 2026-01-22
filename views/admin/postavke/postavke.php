<?php if ( current_user_can('administrator') ) : ?>
    <div class="wrap">
        <h2>DC rezervacije - postavke</h2>

        <h2 class="nav-tab-wrapper cn-nav-tab-wrapper">
            <a class="nav-tab nav-tab-active" href="<?php echo admin_url('admin.php?page=dcr-postavke'); ?>">Općenite postavke</a>

            <a class="nav-tab" href="<?php echo admin_url('admin.php?page=dcr-postavke&action=dozvole'); ?>">Dozvole</a>
        </h2>

        <?php include_once (DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>
        <form action="" method="post" id="form-postavke" class="dc-form-postavke">
            <div id="postbox-container-2" class="postbox-container">

                <table class="form-table" role="presentation">
                    <?php if($settings->dc_postavke->active != 1) : ?>
                        <div class="status status-danger" style="margin: 20px;">DC rezervacije su neaktivne. Trenutno se prikazuje poruka neaktivnosti na stranici.</div>
                    <?php endif; ?>

                    <div class="status status-<?php echo $licence->status == 'error' ? 'danger' : 'success'; ?>" style="margin: 20px;"><?php echo $licence->message ?? ''; ?></div>

                    <h2><u>Detalji o agenciji</u></h2>
                    <tbody>
                    <tr>
                        <th scope="row"><label for="active">AKTIVNO</label></th>
                        <td>
                            <input type="checkbox" name="active" id="active" value="1" <?php echo $settings->dc_postavke->active == 1 ? 'checked="checked"' : ''; ?>>
                            <label for="active">Označite za aktiviranje DC rezervacije</label>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="active_message">Poruka neaktivnosti</label></th>
                        <td>
                            <textarea name="active_message" rows="3" id="active_message"><?php echo $settings->dc_postavke->active_message ?? ''; ?></textarea>
                        </td>
                        <td>Ova se poruka prikazuje kada DC rezervacije nisu aktivne.</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="licenca">Licenca</label></th>
                        <td><input name="licenca" type="text" id="licenca" class="licenca validate" value="<?php echo $settings->dc_postavke->licenca ?>"></td>
                        <td><span class="status status-<?php echo $licence->status == 'error' ? 'danger' : 'success'; ?>"><?php echo $licence->valid_to ?? 'NEISPRAVNA LICENCA'?></span></td>
                    </tr>
                    <tr>
                        <td colspan="3"><hr></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="naziv_tvrtke">Naziv agencije</label></th>
                        <td><input name="naziv_tvrtke" type="text" id="naziv_tvrtke" class="naziv_tvrtke validate" value="<?php echo $settings->dc_postavke->naziv_tvrtke ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="adresa_tvrtke">Adresa agencije</label></th>
                        <td><input name="adresa_tvrtke" type="text" id="adresa_tvrtke" class="adresa_tvrtke validate" value="<?php echo $settings->dc_postavke->adresa_tvrtke ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="postanski_broj_tvrtke">Poštanski broj agencije</label></th>
                        <td><input name="postanski_broj_tvrtke" type="text" id="postanski_broj_tvrtke" class="postanski_broj_tvrtke validate" value="<?php echo $settings->dc_postavke->postanski_broj_tvrtke ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="mjesto_tvrtke">Mjesto agencije</label></th>
                        <td><input name="mjesto_tvrtke" type="text" id="mjesto_tvrtke" class="mjesto_tvrtke validate" value="<?php echo $settings->dc_postavke->mjesto_tvrtke ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="banka_tvrtke">Banka agencije</label></th>
                        <td><input name="banka_tvrtke" type="text" id="banka_tvrtke" class="banka_tvrtke validate" value="<?php echo $settings->dc_postavke->banka_tvrtke ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="iban_tvrtke">IBAN agencije</label></th>
                        <td><input name="iban_tvrtke" type="text" id="iban_tvrtke" class="iban_tvrtke validate" value="<?php echo $settings->dc_postavke->iban_tvrtke ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="radno_vrijeme_od">Radno vrijeme od</label></th>
                        <td><input name="radno_vrijeme_od" type="text" id="radno_vrijeme_od" class="radno_vrijeme_od validate" value="<?php echo $settings->dc_postavke->radno_vrijeme_od ?? ''; ?>"></td>
                        <td class="small">Prikazuje se korisniku kao info na stranici zahvale. Svaki radni dan od... do...</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="radno_vrijeme_do">Radno vrijeme do</label></th>
                        <td><input name="radno_vrijeme_do" type="text" id="radno_vrijeme_do" class="radno_vrijeme_do validate" value="<?php echo $settings->dc_postavke->radno_vrijeme_do ?? ''; ?>"></td>
                        <td class="small">Prikazuje se korisniku kao info na stranici zahvale. Svaki radni dan od... do...</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="rezervirano_sati">Zadržavanje rezervacije (sati)</label></th>
                        <td><input name="rezervirano_sati" type="number" min="0" max="240" id="rezervirano_sati" class="rezervirano_sati validate" value="<?php echo $settings->dc_postavke->rezervirano_sati ?? ''; ?>"></td>
                        <td class="small">U ovom vremenskom roku će se čuvati mjesto rezervacije u slučaju da nije odmah izvšena uplata. Isključite ovu opciju postavljanjem vrijednost na 0 (nulu).</td>
                    </tr>

                    <?php
                    $image_url = '';
                    if($settings->dc_postavke->slika_potpisa_id > 0) {
                        $image_url = wp_get_attachment_image_src($settings->dc_postavke->slika_potpisa_id, 'full');
                    }
                    ?>
                    <tr class="form-field">
                        <th scope="row"><label for="rezervirano_sati">Slika potpisa za ugovor</label></th>
                        <td>
                            <input type="button" id="upload_image_button" class="button" value="Odaberi sliku potpisa">
                            <p>
                                <img src="<?php echo esc_url($image_url[0]); ?>" id="uploaded_image" style="max-width: 200px;">
                            </p>
                            <input type="hidden" id="slika_potpisa_id" name="slika_potpisa_id" value="<?php echo $settings->dc_postavke->slika_potpisa_id; ?>">
                        </td>
                        <td class="small">Ova slika će biti prikazana u ugovoru kao potpis. Preporučene max. dimenzije 250 x 180px</td>
                    </tr>
                    </tbody>
                </table>

                <table class="form-table" role="presentation">
                    <h2><u>Administrator</u></h2>
                    <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="admin_name">Ime administratora</label></th>
                        <td><input name="admin_name" type="text" id="admin_name" class="admin_name validate" value="<?php echo $settings->dc_postavke->admin_name ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="admin_mail">Email administratora</label></th>
                        <td><input name="admin_mail" type="text" id="admin_mail" class="admin_mail validate" value="<?php echo $settings->dc_postavke->admin_mail ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="copy_mail">Email za kopiju mailova</label></th>
                        <td><input name="copy_mail" type="text" id="copy_mail" class="copy_mail validate" value="<?php echo $settings->dc_postavke->copy_mail ?? ''; ?>"></td>
                        <td class="small">Ako ovo polje nije prazno, svi mailovi koji se šalju korisnicima biti će poslani kao kopija na zadani mail sa naslovom "Kopija:..."</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>

                <table class="form-table" role="presentation">
                    <h2><u>Stranice</u></h2>
                    <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="stranica_rezervacije">Stranica rezervacija</label></th>
                        <td style="width: 400px;">
                            <select name="stranica_rezervacije" id="stranica_rezervacije">
                                <option value="0">Stranica rezervacije putovanja</option>
                                <?php
                                foreach ($settings->pages as $page) {
                                    $selected = '';
                                    if($settings->dc_postavke->stranica_rezervacije == $page->ID) {
                                        $selected = 'selected="selected"';
                                    }
                                    echo '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td class="small">Odaberite stranicu na kojoj se nalazi kratki kod <span class="dc_shortcode">[dc_rezervacija]</span></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="stranica_naplate">Stranica naplate</label></th>
                        <td>
                            <select name="stranica_naplate" id="stranica_naplate" style="width: 100%;">
                                <option value="0">Stranica naplate putovanja</option>
                                <?php
                                foreach ($settings->pages as $page) {
                                    $selected_2 = '';
                                    if($settings->dc_postavke->stranica_naplate == $page->ID) {
                                        $selected_2 = 'selected="selected"';
                                    }
                                    echo '<option value="' . $page->ID . '" ' . $selected_2 .  '>' . $page->post_title . '</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td class="small">Odaberite stranicu na kojoj se nalazi kratki kod <span class="dc_shortcode">[dc_naplata]</span></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="stranica_naplate">Tip ostalih putovanja</label></th>
                        <td>
                            <select name="stranica_putovanja" id="stranica_putovanja" style="width: 100%;">
                                <option value="page">Stranica putovanja u administraciji</option>
                                <?php
                                $post_types = get_post_types([], 'objects');

                                foreach ($post_types as $post_type) {
                                    $selected_3 = '';
                                    if($settings->dc_postavke->post_type == $post_type->name) {
                                        $selected_3 = 'selected="selected"';
                                    }
                                    echo '<option value="' . esc_html($post_type->name) . '" ' . $selected_3 .  '>' . $post_type->name . ' (' . $post_type->label . ')</option>';
                                }
                                ?>
                            </select>
                        </td>
                        <td class="small">Odaberite tip stranice na kojoj želite prikazati DC rezervacije za ostala putovanja.</td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>

                <table class="form-table" role="presentation">
                    <h2><u>WsPay postavke</u></h2>
                    <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="ws_pay_id">WsPay ID</label></th>
                        <td><input type="text" id="ws_pay_id" value="<?php echo $licence->ws_pay_id ?? ''; ?>" readonly=""></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="ws_pay_secret">WsPay secret</label></th>
                        <td style="width: 400px;"><input name="ws_pay_secret" type="text" id="ws_pay_secret" class="ws_pay_secret validate" value="****" autocomplete="off"></td>
                        <td class="small">Ako želite promijeniti tajni ključ, samo ga upišite i spremite.<br>U suprotnom ostavite polje kako jest.</td>
                    </tr>
                    <tr>
                        <th scope="row"><label for="ws_pay_id">WsPay test</label></th>
                        <td>
                            <input type="checkbox" name="ws_pay_test" id="ws_pay_test" value="1" <?php echo $settings->dc_postavke->ws_pay_test == 1 ? 'checked="checked"' : ''; ?>>
                            <label for="ws_pay_test">Označite za aktiviranje testnog okruženja</label>
                        </td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>

                <table class="form-table" role="presentation">
                    <h2><u>E-računi postavke</u></h2>
                    <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="eracuni_username">API Username</label></th>
                        <td><input type="text" id="eracuni_username" value="<?php echo $licence->eracuni_username ?? ''; ?>" readonly=""></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="eracuni_token">API Token</label></th>
                        <td style="width: 400px;"><input name="eracuni_token" type="text" id="eracuni_token" class="eracuni_token validate" value="****" autocomplete="off"></td>
                        <td class="small">Ako želite promijeniti API token, upišite i spremite.<br>U suprotnom ostavite polje kako jest.</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="eracuni_password">API Password</label></th>
                        <td style="width: 400px;"><input name="eracuni_password" type="text" id="eracuni_password" class="eracuni_password validate" value="****" autocomplete="off"></td>
                        <td class="small">Ako želite promijeniti API lozinku, upišite i spremite.<br>U suprotnom ostavite polje kako jest.</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="eracuni_broj_artikla">Broj artikla putovanja</label></th>
                        <td style="width: 400px;"><input name="eracuni_broj_artikla" type="text" id="eracuni_broj_artikla" class="eracuni_broj_artikla validate" value="<?php echo $settings->dc_postavke->eracuni_broj_artikla ?? ''; ?>" autocomplete="off"></td>
                        <td class="small">Upišite broj artikla za organizaciju putovanja postavljen na eRačun.
                            <span class="dc-open-popup dc_shortcode" style="cursor: pointer;">Prikaži primjer artikla</span>
                            <div class="dc-popup-bubble-container">
                                <div class="dc-popup-bubble">
                                    <img src="<?php echo DC_REZERVACIJE_URL; ?>assets/images/articleExample.png" alt="Popup Image">
                                    <p>Obavezno mora biti označena promjena cijene, opisa i artikal mora biti aktivan.</p>
                                    <div class="dc-close-popup" style="font-weight: 700; color: #4a94d8; cursor: pointer;">Zatvori prikaz</div>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="eracuni_posl_prostor">Broj poslovnog prostora (školska)</label></th>
                        <td style="width: 400px;"><input name="eracuni_posl_prostor" type="text" id="eracuni_posl_prostor" class="eracuni_posl_prostor validate" value="<?php echo $settings->dc_postavke->eracuni_posl_prostor ?? ''; ?>" autocomplete="off"></td>
                        <td class="small">Upišite broj poslovnog prostora koji je prijavljen na FINU za školska putovanja. (Trenutno se koristi za obje vrste putovanja)</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="eracuni_posl_prostor_2">Broj poslovnog prostora (ostala)</label></th>
                        <td style="width: 400px;"><input name="eracuni_posl_prostor_2" type="text" id="eracuni_posl_prostor_2" class="eracuni_posl_prostor_2" value="<?php echo $settings->dc_postavke->eracuni_posl_prostor_2 ?? ''; ?>" autocomplete="off"></td>
                        <td class="small">Upišite broj poslovnog prostora koji je prijavljen na FINU za ostala putovanja. <span style="color: red;">(NIJE AKTIVNO)</span></td>
                    </tr>
                    <tr>
                        <td>&nbsp;</td>
                    </tr>
                    </tbody>
                </table>

                <table class="form-table" role="presentation">
                    <h2><u>Postavke putovanja</u></h2>
                    <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="prefix_broja">Prefix školskih putovanja</label></th>
                        <td style="width: 400px;"><input name="prefix_broja" type="text" id="prefix_broja" class="prefix_broja validate" value="<?php echo $settings->dc_postavke->prefix_broja ?? ''; ?>" autocomplete="off"></td>
                        <td class="small">Npr. DC324. U ovom slučaju "DC" je prefix. Preporučeno do max. 6 brojeva i slova.</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="prefix_broja">Prefix ostalih putovanja</label></th>
                        <td style="width: 400px;"><input name="prefix_broja_ostala" type="text" id="prefix_broja_ostala" class="prefix_broja_ostala validate" value="<?php echo $settings->dc_postavke->prefix_broja_ostala ?? ''; ?>" autocomplete="off"></td>
                        <td class="small">Npr. DC-OST324. U ovom slučaju "DC-OST" je prefix. Preporučeno do max. 6 brojeva i slova.</td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="varijabilni_text_ugovora_draft">Varijabilni tekst prijedloga ugovora</label></th>
                        <td>
                            <textarea name="varijabilni_text_ugovora_draft" rows="12" id="varijabilni_text_ugovora_draft"><?php echo $settings->dc_postavke->varijabilni_text_ugovora_draft ?? ''; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <table class="form-table" role="presentation">
                    <h2><u>Opći uvjeti putovanja</u></h2>
                    <tbody>
                    <tr class="form-field">
                        <th scope="row"><label for="opci_uvjeti">Opći uvjeti putovanja</label></th>
                        <td>
                            <textarea name="opci_uvjeti" rows="12" id="opci_uvjeti"><?php echo $settings->dc_postavke->opci_uvjeti ?? ''; ?></textarea>
                        </td>
                    </tr>
                    </tbody>
                </table>

                <input type="submit" name="spremi_postavke" id="spremi_postavke" class="button button-primary button-large" value="Spremi promjene">
            </div>
        </form>
    </div>

    <script>
        jQuery(document).ready(function($){
            $('#upload_image_button').click(function(){
                var mediaUploader;
                if (mediaUploader) {
                    mediaUploader.open();
                    return;
                }
                mediaUploader = wp.media.frames.file_frame = wp.media({
                    title: 'Choose Image',
                    button: {
                        text: 'Choose Image'
                    },
                    multiple: false
                });
                mediaUploader.on('select', function(){
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    $('#uploaded_image').attr('src', attachment.url);
                    $('#slika_potpisa_id').val(attachment.id);
                });
                mediaUploader.open();
            });
        });

    </script>

<?php else: ?>

    <div class="notice notice-error is-dismissible">
        <p>Nemate pravo pristupa ovom dijelu stranice</p>
    </div>

<?php endif; ?>