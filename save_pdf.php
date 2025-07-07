<?php
require_once 'dompdf/autoload.inc.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Setup Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Your content
$html = '<h1>Contoh PDF</h1><p>This file will be saved into server folder.</p>';

// Load and render
$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();

// Save to file
$output = $dompdf->output();
file_put_contents('saved_pdfs/my_review.pdf', $output);

echo "PDF has been saved!";
?>
