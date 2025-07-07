<?php
require_once 'vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);
$dompdf->loadHtml('<h1>Test PDF</h1><p>This PDF is working with full Dompdf setup.</p>');
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream("review.pdf", ["Attachment" => false]);
?>
