<style>
    #setting-error-tgmpa {
        display: none !important;
    }
    .select2-container {
        width: 100% !important;
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
            <b>Uredi putovanje</b>
        <?php else: ?>
            <b>Dodaj novo putovanje</b>
        <?php endif; ?>
    </h1>

    <form name="post" action="#" method="post" id="<?php if($uredivanje) { echo 'uredi-putovanje'; } else { echo 'novo-putovanje'; } ?>">
        <input type="hidden" name="putovanje_type" value="ostala">
        <div id="postbox-container-2" class="postbox-container dc-admin-col">
            <table class="dc_input_table wp-list-table widefat striped dc_data_table" role="presentation">
                <tbody>
                <tr>
                    <th><label for="title">Naziv putovanja</label></th>
                    <td colspan="2">
                        <input type="text" class="validate" name="naziv" size="30" id="title" autocomplete="off" style="width: 100% !important;" value="<?php echo $putovanje->naziv ?? ''; ?>">
                    </td>
                </tr>
                <tr>
                    <th><label for="akontacija">Akontacija</label></th>
                    <td>
                        <input name="akontacija" type="text" id="akontacija" class="akontacija validate" value="<?php echo $putovanje->akontacija ?? ''; ?>"><br>
                    </td>
                    <td>Upisati cijenu za 1 putnika</td>
                </tr>
                <tr>
                    <th><label for="ukupni_iznos">Iznos putovanja (gotovina) </label></th>
                    <td><input name="ukupni_iznos" type="text" id="ukupni_iznos" class="ukupni_iznos validate" value="<?php echo $putovanje->ukupni_iznos ?? ''; ?>"></td>
                    <td>Upisati cijenu za 1 putnika</td>
                </tr>
                <tr>
                    <th><label for="ukupni_iznos_kartica">Iznos putovanja (kartica) </label></th>
                    <td><input name="ukupni_iznos_kartica" type="text" id="ukupni_iznos_kartica" class="ukupni_iznos_kartica validate" value="<?php echo $putovanje->ukupni_iznos_kartica ?? ''; ?>"></td>
                    <td>Upisati cijenu za 1 putnika</td>
                </tr>
                <tr>
                    <th><label for="broj_putnika">Max. broj putnika </label></th>
                    <td><input name="broj_putnika" type="text" id="broj_putnika" class="broj_putnika validate" value="<?php echo $putovanje->broj_putnika ?? ''; ?>"></td>
                    <td></td>
                </tr>
                <tr>
                    <th><label for="putovanje_od">Trajanje putovanja od </label></th>
                    <td><input name="putovanje_od" type="date" id="putovanje_od" class="putovanje_od validate" value="<?php echo $putovanje->putovanje_od ?? date('Y-m-01', strtotime('+1month')); ?>"></td>
                    <td>Zadano postavljeno 1. datum u sljedećem mjesecu</td>
                </tr>
                <tr>
                    <th><label for="putovanje_do">Trajanje putovanja do </label></th>
                    <td><input name="putovanje_do" type="date" id="putovanje_do" class="putovanje_do validate" value="<?php echo $putovanje->putovanje_do ?? date('Y-m-d', strtotime(date('Y-m-01', strtotime('+1month')) . '+10 days')); ?>"></td>
                    <td>Zadano postavljeno trajanje od 10 dana</td>
                </tr>
                <tr>
                    <th>Status putovanja</th>
                    <td>
                        <input type="checkbox" name="status" id="status_putovanja" value="1" checked="checked">
                        <label for="status_putovanja">AKTIVNO</label>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <th><label for="rezervirano_sati">Plan putovanja</label></th>
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
                <?php if($uredivanje) : ?>
                    <tr>
                        <td colspan="3">
                            <?php if($post_connection > 0) : ?>
                                Povezano putovanje: <a href="<?php echo get_edit_post_link($post_connection); ?>"><?php echo get_the_title($post_connection); ?></a>
                            <?php else: ?>
                                <span style="color: red; font-weight: 700;">Ovo putovanje još nije povezano sa putovanjem na webu!</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr>
                    <td colspan="3"><b><center>Fakultativni dodaci</center></b></td>
                </tr>
                <tr class="form-field">
                    <td>Iznos</td>
                    <td>Naziv</td>
                    <td><span class="button dc_add_new_row" style="float: right;"> + Dodaj novi red</span></td>
                </tr>
                <tr class="form-field dc_clone_row">
                    <td>
                        <input class="dc_iznos" name="additionals[amount][]" type="text">
                    </td>
                    <td colspan="2">
                        <input name="additionals[name][]" type="text">
                    </td>
                </tr>
                <?php foreach($additionals as $additional) : ?>
                    <tr class="form-field">
                        <td>
                            <input class="dc_iznos" name="additionals[amount][]" type="text" value="<?php echo $additional->amount; ?>">
                        </td>
                        <td colspan="2">
                            <input name="additionals[name][]" type="text" value="<?php echo $additional->name; ?>">
                        </td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="2">
                        <?php if(isset($putovanje->id)) : ?>
                            <input type="hidden" name="edit_tour" value="<?php echo $putovanje->id; ?>">
                        <?php endif; ?>

                        <?php if(isset($_GET['action']) && isset($_GET['id'])): ?>
                            <input type="submit" class="button button-primary button-large" value="Uredi putovanje">
                        <?php else: ?>
                            <input type="submit" class="button button-primary button-large" value="Dodaj novo putovanje">
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
    });
</script>
