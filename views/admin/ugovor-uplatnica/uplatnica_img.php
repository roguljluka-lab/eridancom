<?php
require_once(TCPDF_PATH . 'tcpdf.php');

ob_start();
// create new PDF document
$pdf = new TCPDF('L', PDF_UNIT, '', true, 'UTF-8', false);

// add a page
$pdf->AddPage();

$lf = "\x0A";

$converted_number = str_pad($iznos_za_uplatu, 15, '0', STR_PAD_LEFT);
$full_name = strtoupper($ime_ugovaratelja);
$address = strtoupper($adresa_ugovaratelja);
$post_number = strtoupper($postanski_broj);
$town = strtoupper($mjesto);

$barcodeData =
    'HRVHUB30' . $lf .
    'EUR' . $lf .
    $converted_number . $lf .
    $full_name . $lf .
    $address . $lf .
    $post_number . ' ' . $town . $lf .
    strtoupper($this->dc_settings->naziv_tvrtke) . $lf .
    strtoupper($this->dc_settings->adresa_tvrtke) . $lf .
    $this->dc_settings->postanski_broj_tvrtke . ' ' . strtoupper($this->dc_settings->mjesto_tvrtke) . $lf .
    $this->dc_settings->iban_tvrtke . $lf .
    'HR00' . $lf .
    substr($poziv_na_broj, 3) . $lf .
    'COST' . $lf .
    $putovanje_naziv . ' (' . $sifra_putovanja . ')' . $lf
;
$style = array(
    'border' => 0,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => array(0,0,0),
    'bgcolor' => false, //array(255,255,255)
    'module_width' => 1, // width of a single module in points
    'module_height' => 1 // height of a single module in points
);
//$pdf->write2DBarcode($barcodeData, 'PDF417', '', '', 580,260);
$pdf->write2DBarcode($barcodeData, 'PDF417', 0, 0, 580, 260, $style, 'N');

// ---------------------------------------------------------
ob_end_clean();
//Close and output PDF document
$pdfContent  = $pdf->Output('', 'S');

// Save PDF417 barcode as PNG in wp-content/uploads/barcode
$uploadsDir = wp_upload_dir();
$barcodeDir = $uploadsDir['basedir'] . '/barcode';
$pngFilePath = $barcodeDir . '/' . md5($nova_rezervacija_id) . '.png';

if (!file_exists($barcodeDir)) {
    mkdir($barcodeDir);
}

// Convert PDF to PNG using Imagick extension
/*$imagick = new \Imagick();
$imagick->readImageBlob($pdfContent);
$imagick->setImageFormat("png");
$imageContent = $imagick->getImageBlob();*/

try {
    $imagick = new \Imagick();
    $imagick->setResolution(300, 300);
    $imagick->readImageBlob($pdfContent);
    $imagick->setImageFormat("png");
    $imagick->setIteratorIndex(0);
    $imageContent = $imagick->getImageBlob();
} catch (Exception $e) {
    error_log('Imagick greška: ' . $e->getMessage());
}


// Save PNG file
file_put_contents($pngFilePath, $imageContent);
