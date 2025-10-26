<?php
session_start();
header("X-Accel-Buffering: no");
header("Cache-Control: no-cache");

// Verifica che l'utente sia loggato come cliente
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
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Area Clienti - SFT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #27ae60 0%, #2ecc71 100%); color: white; padding: 1rem 0; }
        .nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .logo { font-size: 1.8rem; font-weight: bold; color: white; }
        .user-info { color: white; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card h3 { color: #2c3e50; margin-bottom: 1rem; border-bottom: 2px solid #27ae60; padding-bottom: 0.5rem; }
        .btn { background: #27ae60; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #219653; }
        .btn-secondary { background: #3498db; }
        .btn-secondary:hover { background: #2980b9; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .status { padding: 0.3rem 0.8rem; border-radius: 15px; font-size: 0.8rem; font-weight: bold; }
        .status.emesso { background: #fff3cd; color: #856404; }
        .status.confermato { background: #d4edda; color: #155724; }
        .status.usato { background: #d1ecf1; color: #0c5460; }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">SFT - Area Clienti</div>
            <div class="user-info">
                Benvenuto, <?php echo $_SESSION['user_name']; ?>
                <a href="logout.php" style="color: white; margin-left: 1rem;">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Area Personale</h1>
        <p>Gestisci le tue prenotazioni e biglietti</p>

        <div class="dashboard">
            <div class="card">
                <h3>🎫 Le tue prenotazioni</h3>
                <?php if ($db_connected): 
                    $user_id = $_SESSION['user_id'];
                    $sql = "SELECT b.id_biglietto, b.codice_prenotazione, b.data_emissione, b.prezzo_totale, b.stato,
                                   t.data_corsa, t.orario_partenza, t.direzione,
                                   s1.nome as partenza, s2.nome as arrivo
                            FROM proj1_Biglietto b
                            JOIN proj1_Treno t ON b.id_treno = t.id_treno
                            JOIN proj1_Stazione s1 ON b.staz_salita = s1.id_stazione
                            JOIN proj1_Stazione s2 ON b.staz_discesa = s2.id_stazione
                            WHERE b.id_utente = ?
                            ORDER BY b.data_emissione DESC
                            LIMIT 5";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    
                    if ($result && $result->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Codice</th><th>Data Viaggio</th><th>Tratta</th><th>Prezzo</th><th>Stato</th></tr>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['codice_prenotazione']; ?></td>
                                    <td><?php echo $row['data_corsa'] . ' ' . $row['orario_partenza']; ?></td>
                                    <td><?php echo $row['partenza'] . ' → ' . $row['arrivo']; ?></td>
                                    <td>€<?php echo number_format($row['prezzo_totale'], 2); ?></td>
                                    <td>
    <span class="status <?php echo strtolower($row['stato']); ?>">
        <?php 
        // Mostra stato più user-friendly
        if ($row['stato'] == 'PAGAMENTO_IN_CORSO') {
            echo '⏳ In Attesa Pagamento';
        } else {
            echo $row['stato'];
        }
        ?>
    </span>
</td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>Non hai ancora effettuato prenotazioni.</p>
                    <?php endif;
                    $stmt->close();
                else: ?>
                    <p>Servizio temporaneamente non disponibile.</p>
                <?php endif; ?>
                <a href="prenotazione.php" class="btn" style="margin-top: 1rem;">Nuova Prenotazione</a>
            </div>

            <div class="card">
                <h3>👤 I tuoi dati</h3>
                <p><strong>Nome:</strong> <?php echo $_SESSION['user_name']; ?></p>
                <p><strong>Email:</strong> <?php echo $_SESSION['user_email']; ?></p>
                <p><strong>Tipo account:</strong> Cliente Registrato</p>
                
                <h4 style="margin-top: 1.5rem;">Modifica password</h4>
                <form method="POST" action="cambia_password.php">
                    <input type="password" name="nuova_password" placeholder="Nuova password" required style="width: 100%; padding: 0.5rem; margin-bottom: 0.5rem;">
                    <button type="submit" class="btn">Cambia Password</button>
                </form>
            </div>
        </div>

        <div class="card">
            <h3>🚆 Prenota un nuovo viaggio</h3>
            <form method="GET" action="prenotazione.php">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label>Data viaggio:</label>
                        <select name="data" required style="width: 100%; padding: 0.5rem;">
                            <option value="2024-01-20">Sabato 20/01/2024</option>
                            <option value="2024-01-21">Domenica 21/01/2024</option>
                        </select>
                    </div>
                    <div>
                        <label>Partenza:</label>
                        <select name="partenza" required style="width: 100%; padding: 0.5rem;">
                            <option value="">Seleziona...</option>
                            <?php if ($db_connected): 
                                $stazioni = $conn->query("SELECT id_stazione, nome FROM proj1_Stazione ORDER BY km_linea");
                                while($stazione = $stazioni->fetch_assoc()): ?>
                                    <option value="<?php echo $stazione['id_stazione']; ?>"><?php echo $stazione['nome']; ?></option>
                                <?php endwhile;
                            endif; ?>
                        </select>
                    </div>
                    <div>
                        <label>Arrivo:</label>
                        <select name="arrivo" required style="width: 100%; padding: 0.5rem;">
                            <option value="">Seleziona...</option>
                            <?php if ($db_connected): 
                                $stazioni = $conn->query("SELECT id_stazione, nome FROM proj1_Stazione ORDER BY km_linea");
                                while($stazione = $stazioni->fetch_assoc()): ?>
                                    <option value="<?php echo $stazione['id_stazione']; ?>"><?php echo $stazione['nome']; ?></option>
                                <?php endwhile;
                            endif; ?>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn">Cerca Treni Disponibili</button>
            </form>
            <p style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
                <strong>Demo disponibile:</strong> 20-21 Gennaio 2024
            </p>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>