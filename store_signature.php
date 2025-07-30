<?php
function get_pdo() {
    $host = '127.0.0.1';
    $db   = 'sign';
    $user = 'root'; // Change as needed
    $pass = '';
    $charset = 'utf8mb4';
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    return new PDO($dsn, $user, $pass, $options);
}

function store_signature($pdf_id, $page_number, $image_path, $x, $y, $width, $height) {
    $pdo = get_pdo();
    $sql = "INSERT INTO pdf_signatures (pdf_id, page_number, image_path, x, y, width, height) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$pdf_id, $page_number, $image_path, $x, $y, $width, $height]);
    if ($stmt->rowCount()) {
        return $pdo->lastInsertId();
    } else {
        return false;
    }
}

// // If called directly via POST, store one signature
// if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['pdf_id'])) {
//     $pdf_id      = isset($_POST['pdf_id']) ? intval($_POST['pdf_id']) : 0;
//     $page_number = isset($_POST['page_number']) ? intval($_POST['page_number']) : 0;
//     $image_path  = isset($_POST['image_path']) ? $_POST['image_path'] : '';
//     $x           = isset($_POST['x']) ? floatval($_POST['x']) : 0;
//     $y           = isset($_POST['y']) ? floatval($_POST['y']) : 0;
//     $width       = isset($_POST['width']) ? floatval($_POST['width']) : 0;
//     $height      = isset($_POST['height']) ? floatval($_POST['height']) : 0;
//     if (!$pdf_id || !$page_number || !$image_path) {
//         echo json_encode(['success' => false, 'error' => 'Missing required fields']);
//         exit;
//     }
//     $id = store_signature($pdf_id, $page_number, $image_path, $x, $y, $width, $height);
//     if ($id) {
//         echo json_encode(['success' => true, 'id' => $id]);
//     } else {
//         echo json_encode(['success' => false, 'error' => 'Insert failed']);
//     }
// }
