<?php // IZLETI
?>
<style>
    #setting-error-tgmpa {
        display: none !important;
    }
    .select2-container {
        width: 100% !important;
    }
    .razrednik_tr,
    .dc_input_razrednici {
        display: none !important;
    }
</style>
<div class="wrap">
    <?php
    $handle_new_trip = $AdminClass->dc_handle_trip();
    $settings = $AdminClass->get_settings();

    include_once(DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php');

    $uredivanje = false;
    if(isset($putovanje->id)) {
        $uredivanje = true;
    }
    ?>

    <h1 class="wp-heading-inline" style="margin-bottom: 1rem;">
        <?php if(isset($_GET['action']) && isset($_GET['id'])): ?>
            <b>Uredi izlet</b>
        <?php else: ?>
            <b>Dodaj novi izlet</b>
        <?php endif; ?>
    </h1>

    <form name="post" action="#" method="post" id="<?php if($uredivanje) { echo 'uredi-putovanje'; } else { echo 'novo-putovanje'; } ?>">
        <input type="hidden" name="putovanje_type" value="izlet">
        <div id="postbox-container-2" class="postbox-container dc-admin-col">
            <table class="dc_input_table wp-list-table widefat striped dc_data_table" role="presentation">
                <tbody>
                <tr>
                    <th><label for="title">Naziv izleta</label></th>
                    <td colspan="2">
                        <input type="text" class="validate" name="naziv" size="30" id="title" autocomplete="off" style="width: 100% !important;" value="<?php echo $putovanje->naziv ?? ''; ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="akontacija">Akontacija</label></th>
                    <td>
                        <input name="akontacija" type="text" id="akontacija" class="akontacija" value="<?php echo $putovanje->ukupni_iznos ?? ''; ?>" readonly="readonly"><br>
                    </td>
                    <td>Automatski jednaka ukupnom iznosu</td>
                </tr>
                <tr>
                    <th><label for="ukupni_iznos">Iznos izleta (gotovina) </label></th>
                    <td><input name="ukupni_iznos" type="text" id="ukupni_iznos" class="ukupni_iznos validate" value="<?php echo $putovanje->ukupni_iznos ?? ''; ?>"></td>
                    <td>Upisati cijenu za 1 putnika</td>
                </tr>
                <tr>
                    <th><label for="broj_putnika">Max. broj putnika </label></th>
                    <td><input name="broj_putnika" type="text" id="broj_putnika" class="broj_putnika validate" value="<?php echo $putovanje->broj_putnika ?? ''; ?>"></td>
                    <td></td>
                </tr>
                <tr>
                    <th><label for="putovanje_od">Datum izleta </label></th>
                    <td><input name="putovanje_od" type="date" id="putovanje_od" class="putovanje_od validate" value="<?php echo $putovanje->putovanje_od ?? date('Y-m-01', strtotime('+1month')); ?>"></td>
                    <td>Zadano postavljeno 1. datum u sljedećem mjesecu</td>
                </tr>
                <tr class="form-field user-language-wrap">
                    <th>
                        <label for="locale">
                            Školska godina
                        </label>
                    </th>
                    <td>
                        <?php $razredi_data = json_decode($putovanje->razredi) ?? array(); ?>
                        <?php $razredi_sk_godina = $razredi_data[0]->sk_godina ?? ''; ?>
                        <select name="skolska_godina" id="skolska_godina" class="select-school-year">
                            <?php
                            $current_year = date('Y');
                            for ($ssy = $current_year; $ssy <= $current_year + 5; $ssy++) {
                                $selected = $razredi_sk_godina == ($ssy - 1) . $ssy ? 'selected' : '';
                                echo '<option value="' . ($ssy - 1) . $ssy . '" ' . $selected . '>
                                    ' . ($ssy - 1) . '. / ' . $ssy . '.
                                </option>';
                            }
                            ?>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <tr class="form-field user-language-wrap">
                    <th><label for="locale">Naziv škole</label></th>
                    <td>
                        <select name="skola_id" id="skola_id" class="select-school validate">
                            <option value=""></option>
                            <?php foreach($skole as $skola) : ?>
                                <option value="<?php echo $skola->id; ?>" data-naziv="<?php echo $skola->naziv; ?>" data-adresa="<?php echo $skola->adresa_skole; ?>" data-mjesto="<?php echo $skola->mjesto_skole; ?>" data-post="<?php echo $skola->post_skole; ?>" <?php echo isset($putovanje) && $skola->id == $putovanje->skola_id ? 'selected' : ''; ?>><?php echo $skola->naziv; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                    <td><input name="naziv_skole" type="hidden" id="naziv_skole" class="naziv_skole"></td>
                </tr>

                <tr class="form-field podaci_o_skoli">
                    <th><label for="adresa_skole">Adresa škole</label></th>
                    <td><input name="adresa_skole" type="text" id="adresa_skole" class="adresa_skole validate"></td>
                    <td></td>
                </tr>
                <tr class="form-field podaci_o_skoli">
                    <th><label for="mjesto_skole">Mjesto škole</label></th>
                    <td><input name="mjesto_skole" type="text" id="mjesto_skole" class="mjesto_skole validate"></td>
                    <td></td>
                </tr>
                <tr class="form-field podaci_o_skoli">
                    <th><label for="post_skole">Poštanski broj škole</label></th>
                    <td><input name="post_skole" type="text" id="post_skole" class="post_skole validate"></td>
                </tr>
                <tr class="form-field razred_tr <?php echo isset($putovanje->razredi) ? '' : 'style="display: none;'; ?>">
                    <th>
                        <label for="locale">
                            Razredi (npr. 3.A)
                        </label>
                    </th>
                    <td>
                        <select class="select-grade" multiple="multiple" style="width: 100%;">
                            <?php if($putovanje->razredi) : ?>
                                <?php foreach(json_decode($putovanje->razredi) as $razred) : ?>
                                    <option value="<?php echo $razred->id; ?>" selected><?php echo $razred->naziv; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </td>
                    <td></td>
                </tr>
                <tr class="form-field razrednik_tr" style="display: none;">
                    <td class="input_razrednici"></td>
                </tr>
                <tr>
                    <th>Status izleta</th>
                    <td>
                        <input type="checkbox" name="status" id="status_putovanja" value="1" checked="checked">
                        <label for="status_putovanja">AKTIVNO</label>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th><label for="rezervirano_sati">Plan izleta</label></th>
                    <td>
                        <input type="button" id="upload_plan_putovanja" class="button" value="Odaberite plan putovanja">
                        <input type="hidden" id="id_plana_putovanja" name="id_plana_putovanja" value="<?php echo $putovanje->id_plana_putovanja; ?>">
                        <div id="uploaded_file_link">
                            <?php if(!empty($putovanje->id_plana_putovanja)) : ?>
                                <a href="<?php echo esc_url(wp_get_attachment_url($putovanje->id_plana_putovanja)); ?>" target="_blank">Pogledaj PDF plan putovanja</a>
                                <span class="page-title-action remove_document" style="border: 1px solid #b12222; background: #fff4f4; color: #b12222">Ukloni dokument</span>
                            <?php endif; ?>
                        </div>
                    </td>
                    <td>Dozvoljeno samo dodavanje .pdf dokumenta veličine <?php echo size_format(wp_max_upload_size()); ?>. Ovaj plan i program se automatski šalje prilikom rezervacije.</td>
                </tr>
                <tr>
                    <th><label for="text_ugovora">Varijabilni tekst ugovora</label></th>
                    <td colspan="2">
                        <?php if(empty($putovanje->text_ugovora)) : ?>
                            <textarea name="text_ugovora" id="text_ugovora" style="width: 100%;" rows="10"><?php echo $settings->dc_postavke->varijabilni_text_ugovora_draft ?? ''; ?></textarea>
                        <?php else : ?>
                            <textarea name="text_ugovora" id="text_ugovora" style="width: 100%;" rows="10"><?php echo $putovanje->text_ugovora; ?></textarea>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <?php if(isset($putovanje->id)) : ?>
                            <input type="hidden" name="edit_tour" value="<?php echo $putovanje->id; ?>">
                        <?php endif; ?>

                        <?php if(isset($_GET['action']) && isset($_GET['id'])): ?>
                            <input type="submit" class="button button-primary button-large" value="Uredi izlet">
                        <?php else: ?>
                            <input type="submit" class="button button-primary button-large" value="Dodaj novi izlet">
                        <?php endif; ?>
                        <input type="hidden" name="submit_type" id="submit_type" value="create_new">
                    </td>
                    <td></td>
                </tr>
                </tbody>
            </table>
        </div>
    </form>
    <div id="postbox-container-2" class="postbox-container dc-admin-col">
        <?php if(isset($_GET['action']) && isset($_GET['id'])): include_once(DC_REZERVACIJE_PATH . 'views/admin/comon/logs.php'); endif; ?>
    </div>
</div>
<br class="clear">

<script>
    jQuery(document).ready(function($){
        $('#upload_plan_putovanja').click(function(){
            var mediaUploader;
            if (mediaUploader) {
                mediaUploader.open();
                return;
            }
            mediaUploader = wp.media.frames.file_frame = wp.media({
                title: 'Odaberite dokument',
                button: {
                    text: 'Odaberite dokument'
                },
                library: {
                    type: 'application/pdf' // Restrict media library to PDFs only
                },
                multiple: false
            });
            mediaUploader.on('select', function(){
                var attachment = mediaUploader.state().get('selection').first().toJSON();

                // Display a link to the PDF file
                $('#uploaded_file_link').html('<a href="' + attachment.url + '" target="_blank">Pogledaj PDF plan putovanja</a><span class="page-title-action remove_document" style="border: 1px solid #b12222; background: #fff4f4; color: #b12222">Ukloni dokument</span>');

                // Store the attachment ID in a hidden field, for further use
                $('#id_plana_putovanja').val(attachment.id);
            });
            mediaUploader.open();
        });

        $('#ukupni_iznos').on('input', function () {
            $('#akontacija').val($(this).val());
        });
    });
</script>
