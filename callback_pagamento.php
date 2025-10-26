<?php
header("X-Accel-Buffering: no");
header("Cache-Control: no-cache");
header('Content-Type: application/json');

// ✅ ABILITA ERROR REPORTING PER DEBUG
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = 'localhost';
$user = 'em.vasile';
$pass = 'fXadr4AC';
$dbname = 'em_vasile';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) throw new Exception("Database non disponibile");
    $conn->set_charset("utf8");
} catch (Exception $e) {
    echo json_encode(['esito' => 'KO', 'errore' => 'Database non disponibile']);
    exit;
}

// Leggi i dati dalla callback
$input = file_get_contents('php://input');
error_log("Callback input: " . $input);

$input_data = json_decode($input, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    error_log("JSON decode error in callback: " . json_last_error_msg());
    echo json_encode(['esito' => 'KO', 'errore' => 'JSON non valido: ' . json_last_error_msg()]);
    exit;
}

$id_transazione = $input_data['id_transazione'] ?? '';
$stato_pagamento = $input_data['stato'] ?? '';

error_log("Callback ricevuta - Transazione: $id_transazione, Stato: $stato_pagamento");

if (!empty($id_transazione) && $stato_pagamento === 'Completata') {
    // Aggiorna lo stato del biglietto a "EMESSO"
    $sql = "UPDATE proj1_Biglietto SET stato = 'EMESSO' WHERE id_transazione_pagamento = ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        error_log("Prepare error in callback: " . $conn->error);
        echo json_encode(['esito' => 'KO', 'errore' => 'Errore preparazione query']);
        exit;
    }
    
    $stmt->bind_param("s", $id_transazione);
    
    if ($stmt->execute()) {
        error_log("Biglietto aggiornato per transazione: $id_transazione");
        $rows_affected = $stmt->affected_rows;
        error_log("Righe aggiornate: $rows_affected");
    } else {
        error_log("Errore aggiornamento biglietto: " . $stmt->error);
    }
    $stmt->close();
} else {
    error_log("Transazione non valida o stato non Completata");
}

// Risposta a PAY STEAM
echo json_encode(['esito' => 'OK', 'messaggio' => 'Callback ricevuta e elaborata']);
$conn->close();
?>