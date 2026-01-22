<style>
    #setting-error-tgmpa {
        display: none !important;
    }
</style>
<div class="wrap">
    <h1 class="wp-heading-inline">
        Uredi putnika / ugovaratelja
    </h1>
    <p></p>
    <?php include_once(DC_REZERVACIJE_PATH . 'views/errors/admin_errors.php'); ?>

    <div id="postbox-container-2" class="postbox-container dc-admin-col">
        <form name="post" action="#" method="post">
            <table class="wp-list-table widefat striped dc_data_table">

                <tbody>
                <tr class="form-field">
                    <th scope="row"><label for="ime">Ime</label></th>
                    <td><input name="ime" type="text" id="ime" value="<?php echo $putnik->ime ?? ''; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="prezime">Prezime</label></th>
                    <td><input name="prezime" type="text" id="prezime" value="<?php echo $putnik->prezime ?? ''; ?>"></td>
                </tr>
                <tr class="form-field">
                    <th scope="row"><label for="kontakt">Kontakt</label></th>
                    <td><input name="kontakt" type="text" id="kontakt" value="<?php echo $putnik->kontakt ?? ''; ?>"></td>
                </tr>

                <?php if($putnik->tip == 'ugovaratelj') : ?>
                    <tr class="form-field">
                        <th scope="row"><label for="email">Email</label></th>
                        <td><input name="email" type="text" id="email" value="<?php echo $putnik->email ?? ''; ?>"></td>
                    </tr>
                <?php endif; ?>

                <?php if($putnik->tip == 'putnik') : ?>
                    <tr class="form-field">
                        <th scope="row"><label for="adresa">Adresa</label></th>
                        <td><input name="adresa" type="text" id="adresa" value="<?php echo $putnik->adresa ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="pb">Poštanski broj</label></th>
                        <td><input name="pb" type="text" id="pb" value="<?php echo $putnik->pb ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="mjesto">Mjesto</label></th>
                        <td><input name="mjesto" type="text" id="mjesto" value="<?php echo $putnik->mjesto ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="oib_putnika">OIB</label></th>
                        <td><input name="oib_putnika" type="text" id="oib_putnika" value="<?php echo $putnik->oib_putnika ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="rodendan">Datum rođenja</label></th>
                        <td><input name="rodendan" type="text" id="rodendan" value="<?php echo $putnik->rodendan ?? ''; ?>"></td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="spol">Spol</label></th>
                        <td>
                            <select name="spol" id="spol">
                                <option value="musko" <?php echo $putnik->spol == 'musko' ? 'selected=""' : ''; ?>>Muško</option>
                                <option value="zensko" <?php echo $putnik->spol == 'zensko' ? 'selected=""' : ''; ?>>Žensko</option>
                            </select>
                        </td>
                    </tr>
                    <tr class="form-field">
                        <th scope="row"><label for="vrsta_isprave">Vrsta isprave</label></th>
                        <td>
                            <select name="vrsta_isprave" id="vrsta_isprave">
                                <option value="osobna" <?php echo $putnik->vrsta_isprave == 'osobna' ? 'selected=""' : ''; ?>>Osobna iskaznica</option>
                                <option value="putovnica" <?php echo $putnik->vrsta_isprave == 'putovnica' ? 'selected=""' : ''; ?>>Putovnica</option>
                            </select>
                        </td>
                    </tr>
                <?php endif; ?>

                <tr class="form-field">
                    <th scope="row"><label for="tip">Tip korisnika</label></th>
                    <td>
                        <select name="tip" id="tip">
                            <option value="putnik" <?php echo $putnik->tip == 'putnik' ? 'selected=""' : ''; ?>>Putnik / Dodatni putnik</option>
                            <option value="ugovaratelj" <?php echo $putnik->tip == 'ugovaratelj' ? 'selected=""' : ''; ?>>Ugovaratelj / Glavni putnik</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                        <input type="submit" name="update_putnik" id="update_putnik" class="button button-primary button-large" value="Spremi promjene">
                    </td>
                </tr>
                </tbody>

            </table>
        </form>

    </div>

</div>
<br class="clear">