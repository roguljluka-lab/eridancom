<?php

$shop_id = $this->dc_settings->ws_pay_id;
$secret_key = $this->dc_settings->ws_pay_secret;
$ws_pay_test = $this->dc_settings->ws_pay_test;
$br_narudzbe = 'Rezervacija br. ' . $nova_rezervacija_id;
$signature = md5($shop_id . $secret_key . $br_narudzbe . $secret_key . number_format($iznos_za_uplatu, 2, '', '') . $secret_key);
?>
    <!--form name="pay" action="https://form.WSPay.biz/Authorization.aspx" id="wspay_form" method="POST" style="display: none;"-->
    <form name="pay" action="<?php echo $ws_pay_test == 1 ? 'https://formtest.WSPay.biz/Authorization.aspx' : 'https://form.WSPay.biz/Authorization.aspx'; ?>" id="wspay_form" method="POST" style="display: none;">
        <input type="text" class="form-control" name="CustomerFirstName" id="CustomerFirstName" value="<?php echo $i_ugovaratelja; ?>">
        <input type="text" class="form-control" name="CustomerLastName" id="CustomerLastName" value="<?php echo $p_ugovaratelja; ?>">
        <input type="text" class="form-control" name="CustomerPhone" id="CustomerPhone" value="<?php echo $kontakt_ugovaratelja; ?>">
        <input type="text" class="form-control" name="CustomerEmail" id="CustomerEmail" value="<?php echo $email_ugovaratelja; ?>">
        <input type="text" class="form-control" name="CustomerAddress" id="CustomerAddress" value="<?php echo $adresa_ugovaratelja; ?>">
        <input type="text" class="form-control" name="CustomerZip" id="CustomerZip" value="<?php echo $postanski_broj; ?>">
        <input type="text" class="form-control" name="CustomerCity" id="CustomerCity" value="<?php echo $mjesto; ?>">
        <input type="text" class="form-control" name="CustomerCountry" value="Croatia">
        <input type="text" class="form-control" name="ShoppingCartID" value="<?php echo $br_narudzbe ?>">
        <input type="text" class="form-control" name="ShopID" value="<?php echo $shop_id ?>">
        <input type="text" class="form-control" name="TotalAmount" value="<?php echo number_format($iznos_za_uplatu, 2, ',', '') ?>">
        <?php if($broj_rata != 'Jednokratno' && $broj_rata != 'undefined') : ?>
            <input type="text" class="form-control" name="PaymentPlan" value="<?php echo sprintf("%02d", $broj_rata); ?>00">
            <input type="text" class="form-control" name="CreditCardName" value="<?php echo $naziv_kartice ?? ''; ?>">
        <?php endif; ?>
        <input type="text" class="form-control" name="Signature" value="<?php echo $signature ?>">
        <input type="text" class="form-control" name="ReturnURL" value="<?php echo get_permalink($this->dc_settings->stranica_naplate); ?>">
        <input type="text" class="form-control" name="CancelURL" value="<?php echo get_permalink($this->dc_settings->stranica_naplate); ?>">
        <input type="text" class="form-control" name="ReturnErrorURL" value="<?php echo get_permalink($this->dc_settings->stranica_naplate); ?>">
        <input type="submit" class="btn btn-info" id="wsPay_submit" style="display:none;" value="Plati">
    </form>

    <script>
        document.getElementById("wspay_form").submit();
    </script>

<?php
exit();