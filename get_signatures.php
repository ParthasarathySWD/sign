<?php
// get_signatures.php
header('Content-Type: application/json');
require_once 'store_signature.php';

$pdo = get_pdo();
$pdf_id = isset($_GET['pdf_id']) ? $_GET['pdf_id'] : null;
if (!$pdf_id) {
    echo json_encode(['error' => 'Missing pdf_id']);
    exit;
}
$stmt = $pdo->prepare('SELECT * FROM pdf_signatures WHERE pdf_id = ?');
$stmt->execute([$pdf_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($rows);
