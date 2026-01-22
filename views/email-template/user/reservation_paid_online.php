<td style="padding:2.5em;background: #ffffff;">
    <div class="heading-section" style="text-align: center;">
        <h2>Uplata za putovanje: <?php echo $data['putovanje_naziv']; ?></h2>
        <p>
            Vaš račun je uspješno terećen u iznosu od <?php echo $data['iznos_uplate']; ?>€ u korist plaćanja putovanja <?php echo $data['putovanje_naziv']; ?>.<br>
            U privitku dostavljamo račun za uplaćeni iznos.
        </p>
        <p><b>Broj vaše rezervacije je <?php echo $data['broj_rezervacije']; ?></b>. Novu uplatu možete izvršiti <a href="<?php echo $data['uplatni_link']; ?>">ovdje.</a></p>
    </div>
</td>