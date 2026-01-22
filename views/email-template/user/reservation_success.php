<td style="padding:2.5em;background: #ffffff;">
    <div class="heading-section" style="text-align: center;">
        <h2>Potvrdite rezervaciju</h2>
        <p>
            Zaprimili smo vašu prijavu za putovanje <?php echo $data['putovanje_naziv'] ?>. Prijava je rezervirana narednih <?php echo $this->dc_settings->rezervirano_sati; ?> sata.<br>
            Za potvrdu rezervacije potrebno je uplatiti akontaciju u iznosu od <b><?php echo $data['za_uplatiti'] ?> €</b>.
        </p>
        <p>
            <b>Broj vaše rezervacije je <?php echo $data['broj_rezervacije']; ?>.</b><br>
            <!--Uplatu putovanja možete izvršiti online kreditnom karticom <a href="<?php echo $data['uplatni_link']; ?>">ovdje.</a>-->
        </p>
    </div>
    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
            <td valign="top">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                    <tr>
                        <td style="padding-top: 20px; padding-right: 10px;">
                            <b>Podaci za uplatu</b>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <p>
                                Naziv tvrtke<br>
                                <b>
                                    <?php echo $this->dc_settings->naziv_tvrtke; ?><br>
                                    <?php echo $this->dc_settings->adresa_tvrtke; ?><br>
                                    <?php echo $this->dc_settings->postanski_broj_tvrtke; ?> <?php echo $this->dc_settings->mjesto_tvrtke; ?>
                                </b>
                            </p>
                            <p>
                                Iznos akontacije za uplatu<br>
                                <b><?php echo $data['za_uplatiti'] ?> €</b>
                            </p>
                            <p>
                                IBAN<br>
                                <b><?php echo $this->dc_settings->iban_tvrtke; ?><br><?php echo $this->dc_settings->banka_tvrtke; ?></b>
                            </p>
                            <p>
                                Model i poziv na broj primatelja<br>
                                <b>HR<?php echo $data['poziv_na_broj']; ?></b>
                            </p>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-top: 20px; padding-right: 10px;">
                            <b>Na ponudi u privitku se nalazi kod za skeniranje putem kojeg možete brže i jednostavnije izvršiti uplatu.</b>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</td>


