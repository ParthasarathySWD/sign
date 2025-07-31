
<?php
require 'vendor/autoload.php';
require_once __DIR__ . '/store_signature.php';
use setasign\Fpdi\Fpdi;

header('Content-Type: application/json');
define('FCPATH', __DIR__ );
define('SIGNATURES_DIR', FCPATH . '/signatures/');
define('TEMP_DIR', sys_get_temp_dir() . '/');
define('OUTPUT_DIR', FCPATH . '/output/');

// Handle file upload or URL
$pdfPath = null;
if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
    $pdfPath = $_FILES['pdf_file']['tmp_name'];
} elseif (!empty($_POST['pdf_url'])) {
    $pdfPath = $_POST['pdf_url'];
}
if (!$pdfPath || !file_exists($pdfPath)) {
    echo json_encode(['success' => false, 'error' => 'PDF not found']);
    exit;
}

$signatures = isset($_POST['signatures']) ? json_decode($_POST['signatures'], true) : [];
if (!is_array($signatures)) $signatures = [];

$pdf = new Fpdi('P', 'pt');
$pageCount = $pdf->setSourceFile($pdfPath);

for ($pageNo = 1; $pageNo <= $pageCount; $pageNo++) {
    $tplIdx = $pdf->importPage($pageNo);
    $size = $pdf->getTemplateSize($tplIdx);
    $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
    $pdf->useTemplate($tplIdx);
    foreach ($signatures as $sig) {
        if ((int)$sig['page'] === $pageNo) {
            // Handle base64 image
            $imgPath = null;
            if (strpos($sig['image'], 'base64,') !== false) {
                $imgData = explode(',', $sig['image'])[1];
                $imgPath = 'signatures/img-' . uniqid() . '.png';
                if(!file_put_contents($imgPath, base64_decode($imgData))) {
                    echo json_encode(['success' => false, 'error' => 'Failed to save signature image']);
                    exit;
                }
            } elseif (strpos($sig['image'], 'http') === 0 || file_exists($sig['image'])) {
                $imgPath = $sig['image'];
            }
            if ($imgPath) {
                $pdf->Image($imgPath, $sig['x'], $sig['y'], $sig['width'], $sig['height']);
                // Store signature details in DB
                // Use pdf_id = 0 unless you have a real PDF id
                store_signature(0, $pageNo, $imgPath, $sig['x'], $sig['y'], $sig['width'], $sig['height']);
                // if (isset($imgData)) unlink($imgPath);
            }
        }
    }
}

$outFile = 'signed_output_' . time() . '.pdf';
$pdf->Output('F', $outFile);
echo json_encode(['success' => true, 'file' => $outFile, 'img' => isset($imgPath) ? $imgPath : null]);
