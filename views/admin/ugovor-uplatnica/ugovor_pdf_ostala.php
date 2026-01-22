<?php

require_once(TCPDF_PATH . 'tcpdf.php');

// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// set document information
$pdf->SetCreator(PDF_CREATOR);
$pdf->setPrintHeader(false);

// set default monospaced font
$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

// set margins
//$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetMargins("12", "12", "12");
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

// set auto page breaks
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

// set image scale factor
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

// set some language-dependent strings (optional)
/*if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
    require_once(dirname(__FILE__).'/lang/eng.php');
    $pdf->setLanguageArray($l);
}*/

// ---------------------------------------------------------

// set font
$pdf->SetFont('dejavusans', '', 8);

// add a page
$pdf->AddPage();

// writeHTML($html, $ln=true, $fill=false, $reseth=false, $cell=false, $align='')
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// create some HTML content
$html = '
<style>
    table {
      border-collapse: collapse;
	  width: 100%;
    }
	.pt-30 {
		padding-top: 30px !important;
	}
    .font-8 {
    font-size: 8px !important;
    }
    .padding-4 {
        padding: 4px;
    }
    .padding-8 {
        padding: 8px;
    }
    .padding-10 {
        padding: 10px;
    }
    p.small {
    font-size: 9px;
    }
</style>
<h3 style="text-align: center">UGOVOR I PRIJAVNICA ZA PUTOVANJE</h3>
<table>
<tr>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	<td style="width: 10%;"></td>
	</tr>
    <tr>
		<td colspan="10" style="text-align: center; border: 1px solid #000;"><br><br>
				Putnička agencija "ERIDAN", Fuležina 12, Kaštel Stari, tel: 021/231 977, 231 655,   fax: 021/231 397<br>
				Žiro-račun, IBAN: HR8923600001101494102,   OIB: 63978810525,   ID kod: HR-AB-21-1114166<br>
				RADNO VRIJEME AGENCIJE: PONEDJELJAK – PETAK: 09:00 – 14:00,  UTORAK I ČETVRTAK: 09:00 – 17:00<br>
		</td>
	</tr>
	<tr>
		<td colspan="5" style="height: 32px;">
		</td>
	</tr>
	<tr>
		<td colspan="5" style="line-height: 15px;">
			<b>Naziv putovanja:</b><br>' . $parameters['naziv'] . '
		</td>
		<td colspan="5" style="line-height: 15px;">
		<b>Šifra aranžmana:</b> ' . $parameters['sifra'] . '
		</td>
	</tr>
	<tr>
		<td colspan="5" style="height: 12px;">
		</td>
	</tr>
	<tr>
		<td colspan="5" style="line-height: 15px;">
		<b>GLAVNI PUTNIK:</b><br>
		<b>Ime i prezime:</b> ' . $dc_first_step_data['ime_glavni'] . ' ' . $dc_first_step_data['prezime_glavni'] . '<br>
		<b>Datum rođenja:</b> ' . $dc_first_step_data['datum_glavni'] . '<br>
		<b>Kontakt broj:</b> ' . $dc_first_step_data['kontakt_glavni'] . '<br>
		<b>Adresa stanovanja:</b> ' . $dc_first_step_data['adresa_glavni'] . '<br>
		<b>Mjesto:</b> ' . $dc_first_step_data['postanski_glavni'] . ' ' . $dc_first_step_data['mjesto_glavni'] . '<br>
		</td>
		<td colspan="5">
		<b>DODATNI PUTNIK:</b><br>
		<b>Ime i prezime:</b> ' . $dc_first_step_data['ime_dodatni'] . ' ' . $dc_first_step_data['prezime_dodatni'] . '<br>
		<b>Datum rođenja:</b> ' . $dc_first_step_data['rodenje_dodatni'] . '<br><br>
		</td>
	</tr>
	<tr>
		<td colspan="6" style="height: 30px; font-size: 14px;">
			<b>CIJENA ARANŽMANA:</b> ' . $parameters['ukupni_iznos_putovanja'] . ' €
		</td>
		<td colspan="4">
			
		</td>
	</tr>
    <tr>
		<td colspan="10">
			<span style="color: red; text-decoration: underline">Akontacija je obavezna te garantira mjesto na putovanju.</span>
		</td>
	</tr>
	
	<tr>
		<td colspan="5" style="height: 15px;">
		</td>
	</tr>
	<tr>
	<td colspan="10">
        <h2 style="text-align: center">UGOVOR ZA PUTOVANJE ' . $parameters['sifra'] . ' – ' . $parameters['naziv'] . '</h2>
        <p style="height: 10px"><hr></p>
        ' . $parameters['text_ugovora'] . '
        <p style="height: 10px"><hr></p>
        
        <p>
            Sukladno Zakonu o provedbi Opće uredbe o zaštiti podataka ( N.N. 42/18 ) i čl.  30. Opće uredbe o zaštiti osobnih podataka suglasan sam da putnička agencija Eridan d.o.o.  , Fuležina 12, 21216 Kaštel Stari, OIB: 63978810525 , obrađuje osobne podate, ime i prezime, datum rođenja , po potrebi : broj osobne iskaznice / putovnice ,  OIB i adresu putnika i zakonskog zastupnika  u svrhu realizacije ugovorenog paket aranžmana prema prihvaćenom programu te ih prosljeđuje Trećim osobama.<br><br>
            Upoznat sam sa standardnim obrascem koji u skladu s europskom direktivom odabrano putovanje svrstava u turistički paket.<br><br>
            Upoznat sam s Općim uvjetima i s njima se slažem u ime svih putnika.<br><br>
        </p>
    </td>
</tr>
<tr>
		<td colspan="10" style="height: 50px;">
		</td>
	</tr>
<tr>
    <td colspan="7">
    </td>
    <td colspan="3">
    <img src="' . $potpis_image_url . '" alt="" width="100px">
    </td>
</tr>
</table>
';

// output the HTML content
$pdf->writeHTML($html, true, false, true, false, '');

// reset pointer to the last page
$pdf->lastPage();

// ---------------------------------------------------------
ob_end_clean();
$temp_filename = sys_get_temp_dir() . '/' . md5($parameters['reservation_id']) . '.pdf';
$pdf->Output($temp_filename, 'F');
/*
//Close and output PDF document
if(isset($save_temp_pdf) && $save_temp_pdf === true) {
    $temp_filename = sys_get_temp_dir() . '/Ugovor.pdf';
    $pdf->Output($temp_filename, 'F');
} else {
    $pdf->Output('Ugovor o putovanju ' . $reservation->tour_sku . '-' . sprintf("%02d", $reservation->contract_number) . '.pdf', $action);
}*/