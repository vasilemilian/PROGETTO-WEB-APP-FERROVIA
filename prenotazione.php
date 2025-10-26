<?php
session_start();
header("X-Accel-Buffering: no");
header("Cache-Control: no-cache");

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'UTENTE') {
    header("Location: login.php");
    exit;
}

$host = 'localhost';
$user = 'em.vasile';
$pass = 'fXadr4AC';
$dbname = 'em_vasile';

try {
    $conn = new mysqli($host, $user, $pass, $dbname);
    if ($conn->connect_error) throw new Exception("Database non disponibile");
    $conn->set_charset("utf8");
    $db_connected = true;
} catch (Exception $e) {
    $db_connected = false;
}

// Funzione per calcolare il prezzo
function calcolaPrezzoTrattaReale($conn, $partenza_id, $arrivo_id) {
    if (!$conn) return 42.50;
    
    $sql = "SELECT SUM(ts.prezzo_segmento) as prezzo_totale
            FROM proj1_TariffaSubTratta ts
            WHERE ts.id_subtratta IN (
                SELECT st.id_subtratta
                FROM proj1_SubTratta st
                JOIN proj1_Stazione s_da ON st.id_staz_da = s_da.id_stazione
                JOIN proj1_Stazione s_a ON st.id_staz_a = s_a.id_stazione
                WHERE s_da.km_linea >= (SELECT km_linea FROM proj1_Stazione WHERE id_stazione = ?)
                AND s_a.km_linea <= (SELECT km_linea FROM proj1_Stazione WHERE id_stazione = ?)
                AND s_da.km_linea < s_a.km_linea
            )
            AND ts.data = '2024-01-01'";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $partenza_id, $arrivo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc() && $row['prezzo_totale'] > 0) {
        return $row['prezzo_totale'];
    }
    
    return 42.50;
}

// **FUNZIONE PAGAMENTO CON cURL - CORRETTA**
function processaPagamentoPaySteam($importo, $descrizione, $user_id) {
    $url_api = "https://localhost/em.vasile/prova2/api_pagamento.php";
    
    $dati_pagamento = [
        'url_inviante' => 'https://localhost/em.vasile/prova1/prenotazione.php',
        'url_risposta' => 'https://localhost/em.vasile/prova1/callback_pagamento.php',
        'id_esercente' => 5, // ID esercente valido dal database Proj2_Utente
        'id_transazione' => 'SFT_' . time() . '_' . $user_id,
        'descrizione' => $descrizione,
        'prezzo' => floatval($importo)
    ];
    
    // DEBUG: Log dei dati inviati
    error_log("Dati pagamento inviati: " . json_encode($dati_pagamento));
    
    if (!function_exists('curl_init')) {
        return ['successo' => false, 'errore' => 'cURL non disponibile sul server'];
    }
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url_api,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($dati_pagamento),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Content-Type: application/json',
            'Content-Length: ' . strlen(json_encode($dati_pagamento))
        ],
        CURLOPT_TIMEOUT => 15,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'SFT-Ferrovia/1.0'
    ]);
    
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    
    // DEBUG: Log della risposta
    error_log("Risposta API: " . $response);
    error_log("HTTP Code: " . $http_code);
    error_log("cURL Error: " . $curl_error);
    
    curl_close($ch);
    
    if ($response === false) {
        return ['successo' => false, 'errore' => 'Errore di connessione: ' . $curl_error];
    }
    
    if ($http_code !== 200) {
        return ['successo' => false, 'errore' => 'Errore HTTP: ' . $http_code];
    }
    
    $risposta = json_decode($response, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['successo' => false, 'errore' => 'Risposta JSON non valida: ' . json_last_error_msg()];
    }
    
    if ($risposta && isset($risposta['esito']) && $risposta['esito'] === 'OK') {
        return [
            'successo' => true,
            'id_transazione' => $risposta['id_transazione'],
            'url_pagamento' => $risposta['url_pagamento']
        ];
    }
    
    $errore_api = $risposta['errore'] ?? 'Errore sconosciuto';
    return ['successo' => false, 'errore' => 'Errore API: ' . $errore_api];
}

// Funzione helper
function getNomeStazione($conn, $id_stazione) {
    $sql = "SELECT nome FROM proj1_Stazione WHERE id_stazione = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_stazione);
    $stmt->execute();
    $result = $stmt->get_result();
    $stazione = $result->fetch_assoc();
    $stmt->close();
    return $stazione['nome'] ?? 'Stazione';
}

// Variabili per la ricerca
$data = $_GET['data'] ?? '2024-01-20';
$partenza_id = $_GET['partenza'] ?? '';
$arrivo_id = $_GET['arrivo'] ?? '';
$treni_disponibili = [];
$prezzo_tratta = 0;

if ($partenza_id && $arrivo_id) {
    $prezzo_tratta = calcolaPrezzoTrattaReale($conn, $partenza_id, $arrivo_id);
}

// Cerca treni disponibili
if ($db_connected && $partenza_id && $arrivo_id) {
    $sql = "SELECT DISTINCT t.id_treno, t.data_corsa, t.orario_partenza, t.direzione, 
                   c.nome as convoglio,
                   s1.nome as partenza_nome, s2.nome as arrivo_nome
            FROM proj1_Treno t
            JOIN proj1_Convoglio c ON t.id_convoglio = c.id_convoglio
            JOIN proj1_Fermata f_partenza ON t.id_treno = f_partenza.id_treno 
            JOIN proj1_Fermata f_arrivo ON t.id_treno = f_arrivo.id_treno 
            JOIN proj1_Stazione s1 ON f_partenza.id_stazione = s1.id_stazione
            JOIN proj1_Stazione s2 ON f_arrivo.id_stazione = s2.id_stazione
            WHERE t.data_corsa = ?
            AND f_partenza.id_stazione = ?
            AND f_arrivo.id_stazione = ?
            AND f_partenza.ordine < f_arrivo.ordine
            AND t.stato = 'PROGRAMMATO'
            ORDER BY t.orario_partenza";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sii", $data, $partenza_id, $arrivo_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result) {
        $treni_disponibili = $result->fetch_all(MYSQLI_ASSOC);
    }
    $stmt->close();
}

// **GESTIONE PRENOTAZIONE CON PAGAMENTO**
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['treno_id'])) {
    $treno_id = $_POST['treno_id'];
    $user_id = $_SESSION['user_id'];
    $partenza_id = $_POST['partenza'];
    $arrivo_id = $_POST['arrivo'];
    
    $prezzo_totale = calcolaPrezzoTrattaReale($conn, $partenza_id, $arrivo_id);
    
    // Processa pagamento
    $descrizione_pagamento = "Biglietto treno SFT - " . 
        getNomeStazione($conn, $partenza_id) . " → " . 
        getNomeStazione($conn, $arrivo_id);
    
    $risultato_pagamento = processaPagamentoPaySteam($prezzo_totale, $descrizione_pagamento, $user_id);
    
    if ($risultato_pagamento['successo']) {
        // Crea biglietto in attesa di pagamento
        $sql_biglietto = "INSERT INTO proj1_Biglietto (id_utente, id_treno, staz_salita, staz_discesa, prezzo_totale, codice_prenotazione, stato, id_transazione_pagamento) 
                          VALUES (?, ?, ?, ?, ?, ?, 'PAGAMENTO_IN_CORSO', ?)";
        $codice_prenotazione = 'BGL' . str_pad(rand(1, 999), 3, '0', STR_PAD_LEFT);
        
        $stmt = $conn->prepare($sql_biglietto);
        $stmt->bind_param("iiiidss", $user_id, $treno_id, $partenza_id, $arrivo_id, $prezzo_totale, $codice_prenotazione, $risultato_pagamento['id_transazione']);
        
        if ($stmt->execute()) {
            // Reindirizza alla pagina di pagamento PAY STEAM
            header("Location: " . $risultato_pagamento['url_pagamento']);
            exit;
        } else {
            $error = "Errore creazione prenotazione: " . $conn->error;
        }
        $stmt->close();
    } else {
        $error = "Errore pagamento: " . $risultato_pagamento['errore'];
    }
}
?>

<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Prenotazione - SFT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 1rem 0; }
        .nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .logo { font-size: 1.8rem; font-weight: bold; color: white; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .btn { background: #27ae60; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219653; }
        .btn-pay { background: #e74c3c; }
        .btn-pay:hover { background: #c0392b; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .form-group { margin-bottom: 1rem; }
        .search-form { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .treno-card { background: #f8f9fa; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #27ae60; }
        .treno-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 1rem; margin-bottom: 1rem; }
        .prezzo-info { background: #e8f4fd; padding: 0.5rem 1rem; border-radius: 5px; margin-bottom: 1rem; text-align: center; font-weight: bold; color: #2c3e50; }
        .error { background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .payment-notice { background: #fff3cd; color: #856404; padding: 1rem; border-radius: 5px; margin: 1rem 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">SFT - Prenotazione</div>
            <div class="user-info">
                <?php echo $_SESSION['user_name']; ?>
                <a href="area_cliente.php" style="color: white; margin-left: 1rem;">← Torna all'Area Clienti</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Prenota un Viaggio</h1>

        <?php if (isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Form di ricerca -->
        <div class="card">
            <h3>🔍 Cerca Treni Disponibili</h3>
            <form method="GET" action="prenotazione.php">
                <div class="search-form">
                    <div class="form-group">
                        <label>Data viaggio:</label>
                        <select name="data" required>
                            <option value="2024-01-20" <?php echo ($data == '2024-01-20') ? 'selected' : ''; ?>>Sabato 20/01/2024</option>
                            <option value="2024-01-21" <?php echo ($data == '2024-01-21') ? 'selected' : ''; ?>>Domenica 21/01/2024</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Partenza:</label>
                        <select name="partenza" required>
                            <option value="">Seleziona...</option>
                            <?php if ($db_connected): 
                                $stazioni = $conn->query("SELECT id_stazione, nome FROM proj1_Stazione ORDER BY km_linea");
                                while($stazione = $stazioni->fetch_assoc()): ?>
                                    <option value="<?php echo $stazione['id_stazione']; ?>" 
                                        <?php echo ($stazione['id_stazione'] == $partenza_id) ? 'selected' : ''; ?>>
                                        <?php echo $stazione['nome']; ?>
                                    </option>
                                <?php endwhile;
                            endif; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Arrivo:</label>
                        <select name="arrivo" required>
                            <option value="">Seleziona...</option>
                            <?php if ($db_connected): 
                                $stazioni = $conn->query("SELECT id_stazione, nome FROM proj1_Stazione ORDER BY km_linea");
                                while($stazione = $stazioni->fetch_assoc()): ?>
                                    <option value="<?php echo $stazione['id_stazione']; ?>"
                                        <?php echo ($stazione['id_stazione'] == $arrivo_id) ? 'selected' : ''; ?>>
                                        <?php echo $stazione['nome']; ?>
                                    </option>
                                <?php endwhile;
                            endif; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn">Cerca Treni</button>
            </form>
        </div>

        <!-- Risultati ricerca -->
        <?php if ($partenza_id && $arrivo_id): ?>
            <div class="card">
                <h3>🚆 Treni Disponibili</h3>
                
                <!-- Info prezzo -->
                <div class="prezzo-info">
                    💰 Prezzo per la tratta: <strong>€<?php echo number_format($prezzo_tratta, 2); ?></strong>
                </div>

                <div class="payment-notice">
                    <strong>💳 Pagamento Sicuro:</strong> Verrai reindirizzato a PAY STEAM per completare il pagamento.
                </div>
                
                <?php if (empty($treni_disponibili)): ?>
                    <p>Nessun treno disponibile per la tratta selezionata.</p>
                <?php else: ?>
                    <?php foreach ($treni_disponibili as $treno): 
                        $data_italiana = date('d/m/Y', strtotime($treno['data_corsa']));
                    ?>
                        <div class="treno-card">
                            <div class="treno-info">
                                <div>
                                    <strong>Data:</strong><br>
                                    <?php echo $data_italiana; ?>
                                </div>
                                <div>
                                    <strong>Partenza:</strong><br>
                                    <?php echo $treno['orario_partenza']; ?><br>
                                    <small><?php echo $treno['partenza_nome']; ?></small>
                                </div>
                                <div>
                                    <strong>Arrivo:</strong><br>
                                    <?php echo $treno['direzione']; ?><br>
                                    <small><?php echo $treno['arrivo_nome']; ?></small>
                                </div>
                                <div>
                                    <strong>Convoglio:</strong><br>
                                    <?php echo $treno['convoglio']; ?>
                                </div>
                            </div>
                            
                            <form method="POST" action="prenotazione.php" style="margin-top: 1rem;">
                                <input type="hidden" name="treno_id" value="<?php echo $treno['id_treno']; ?>">
                                <input type="hidden" name="partenza" value="<?php echo $partenza_id; ?>">
                                <input type="hidden" name="arrivo" value="<?php echo $arrivo_id; ?>">
                                <input type="hidden" name="data" value="<?php echo $data; ?>">
                                <button type="submit" class="btn btn-pay">Paga e Prenota (€<?php echo number_format($prezzo_tratta, 2); ?>)</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="area_cliente.php" class="btn">← Torna all'Area Clienti</a>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>