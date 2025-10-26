<?php
session_start();
header("X-Accel-Buffering: no");
header("Cache-Control: no-cache");

// Verifica permessi
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'BACKOFFICE_ADMIN') {
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
    <meta name="viewport" content="width=device-width, initial-scale= 1.0">
    <title>Backoffice Amministrativo - SFT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 1rem 0; }
        .nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #e74c3c; }
        .user-info { color: white; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card h3 { color: #2c3e50; margin-bottom: 1rem; border-bottom: 2px solid #e74c3c; padding-bottom: 0.5rem; }
        .stat { font-size: 2rem; font-weight: bold; color: #e74c3c; text-align: center; margin: 1rem 0; }
        .btn { background: #e74c3c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #c0392b; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .low-occupation { background: #fff3cd; }
        .high-occupation { background: #d4edda; }
        .demo-info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border-left: 4px solid #0dcaf0; }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">SFT<span>.it</span></div>
            <div class="user-info">
                Backoffice Amministrativo | <?php echo $_SESSION['user_name']; ?>
                <a href="logout.php" style="color: white; margin-left: 1rem;">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Backoffice Amministrativo</h1>
        <p>Verifica occupazione treni e redditività</p>

        <div class="demo-info">
            <strong>📅 Demo Attiva:</strong> Treni disponibili per il 20-21 Gennaio 2024
        </div>

        <div class="dashboard">
            <div class="card">
                <h3>📊 Occupazione Treni Demo</h3>
                <?php if ($db_connected): 
                    $sql = "SELECT t.id_treno, t.data_corsa, c.nome as convoglio, t.direzione,
                                   (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) as prenotati,
                                   (SELECT COUNT(*) FROM proj1_Posto p 
                                    JOIN proj1_Composizione comp ON p.id_mezzo = comp.id_mezzo 
                                    WHERE comp.id_convoglio = t.id_convoglio) as posti_totali,
                                   ROUND((SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) / 
                                   (SELECT COUNT(*) FROM proj1_Posto p 
                                    JOIN proj1_Composizione comp ON p.id_mezzo = comp.id_mezzo 
                                    WHERE comp.id_convoglio = t.id_convoglio) * 100, 1) as occupazione_perc
                            FROM proj1_Treno t
                            JOIN proj1_Convoglio c ON t.id_convoglio = c.id_convoglio
                            WHERE t.data_corsa IN ('2024-01-20', '2024-01-21')
                            ORDER BY t.data_corsa, t.orario_partenza";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Data</th><th>Treno</th><th>Convoglio</th><th>Direzione</th><th>Occupazione</th></tr>
                            <?php while($row = $result->fetch_assoc()): 
                                $occupation_class = $row['occupazione_perc'] < 30 ? 'low-occupation' : ($row['occupazione_perc'] > 70 ? 'high-occupation' : '');
                                $data_italiana = date('d/m/Y', strtotime($row['data_corsa']));
                            ?>
                                <tr class="<?php echo $occupation_class; ?>">
                                    <td><?php echo $data_italiana; ?></td>
                                    <td>TR<?php echo str_pad($row['id_treno'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo $row['convoglio']; ?></td>
                                    <td><?php echo $row['direzione']; ?></td>
                                    <td><strong><?php echo $row['occupazione_perc']; ?>%</strong> (<?php echo $row['prenotati']; ?>/<?php echo $row['posti_totali']; ?>)</td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>Nessun treno in programma per la demo</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Dati non disponibili</p>
                <?php endif; ?>
            </div>

            <div class="card">
                <h3>💰 Redditività Treno</h3>
                <?php if ($db_connected): 
                    $sql = "SELECT t.id_treno, t.data_corsa, c.nome as convoglio,
                                   (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) as biglietti_venduti,
                                   (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) * 42.50 as ricavi_stimati,
                                   (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) * 15 as costi_stimati,
                                   (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) * 27.50 as profitto_stimato
                            FROM proj1_Treno t
                            JOIN proj1_Convoglio c ON t.id_convoglio = c.id_convoglio
                            WHERE t.data_corsa IN ('2024-01-20', '2024-01-21')
                            ORDER BY t.data_corsa, t.orario_partenza";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Data</th><th>Treno</th><th>Biglietti</th><th>Ricavi</th><th>Costi</th><th>Profitto</th></tr>
                            <?php while($row = $result->fetch_assoc()): 
                                $data_italiana = date('d/m/Y', strtotime($row['data_corsa']));
                            ?>
                                <tr>
                                    <td><?php echo $data_italiana; ?></td>
                                    <td>TR<?php echo str_pad($row['id_treno'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo $row['biglietti_venduti']; ?></td>
                                    <td>€<?php echo number_format($row['ricavi_stimati'], 2); ?></td>
                                    <td>€<?php echo number_format($row['costi_stimati'], 2); ?></td>
                                    <td><strong>€<?php echo number_format($row['profitto_stimato'], 2); ?></strong></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>Nessun dato di redditività per la demo</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Dati non disponibili</p>
                <?php endif; ?>
            </div>
        </div>

        <div class="card">
            <h3>🚆 Richiesta Treni Straordinari</h3>
            <form method="POST" action="richiesta_straordinari.php">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1rem;">
                    <div>
                        <label>Data:</label>
                        <select name="data_straordinario" required style="width: 100%; padding: 0.5rem;">
                            <option value="2024-01-20">20/01/2024</option>
                            <option value="2024-01-21">21/01/2024</option>
                            <option value="2024-01-22">22/01/2024</option>
                        </select>
                    </div>
                    <div>
                        <label>Direzione:</label>
                        <select name="direzione_straordinario" required style="width: 100%; padding: 0.5rem;">
                            <option value="NORD">Nord</option>
                            <option value="SUD">Sud</option>
                        </select>
                    </div>
                    <div>
                        <label>Motivazione:</label>
                        <input type="text" name="motivazione" placeholder="Alta richiesta" required style="width: 100%; padding: 0.5rem;">
                    </div>
                </div>
                <button type="submit" class="btn">Richiedi Treno Straordinario</button>
            </form>
        </div>

        <div class="card">
            <h3>❌ Cessazione Treni</h3>
            <?php if ($db_connected): 
                $sql = "SELECT t.id_treno, t.data_corsa, t.orario_partenza, c.nome as convoglio,
                               (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) as prenotati
                        FROM proj1_Treno t
                        JOIN proj1_Convoglio c ON t.id_convoglio = c.id_convoglio
                        WHERE t.data_corsa IN ('2024-01-20', '2024-01-21')
                        AND (SELECT COUNT(*) FROM proj1_Prenotazione pr WHERE pr.id_treno = t.id_treno) = 0
                        ORDER BY t.data_corsa, t.orario_partenza";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0): ?>
                    <table class="table">
                        <tr><th>Treno</th><th>Data</th><th>Ora</th><th>Convoglio</th><th>Prenotati</th><th>Azioni</th></tr>
                        <?php while($row = $result->fetch_assoc()): 
                            $data_italiana = date('d/m/Y', strtotime($row['data_corsa']));
                        ?>
                            <tr>
                                <td>TR<?php echo str_pad($row['id_treno'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo $data_italiana; ?></td>
                                <td><?php echo $row['orario_partenza']; ?></td>
                                <td><?php echo $row['convoglio']; ?></td>
                                <td><?php echo $row['prenotati']; ?></td>
                                <td>
                                    <form method="POST" action="cessa_treno.php" style="display: inline;">
                                        <input type="hidden" name="id_treno" value="<?php echo $row['id_treno']; ?>">
                                        <button type="submit" class="btn" style="padding: 0.3rem 0.8rem; background: #dc3545;">Cessa</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Nessun treno senza prenotazioni nella demo</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Dati non disponibili</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>