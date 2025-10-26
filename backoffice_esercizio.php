<?php
session_start();
header("X-Accel-Buffering: no");
header("Cache-Control: no-cache");

// Verifica permessi
if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'BACKOFFICE_ESERCIZIO') {
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
    <title>Backoffice Esercizio - SFT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #3498db 0%, #2980b9 100%); color: white; padding: 1rem 0; }
        .nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .logo { font-size: 1.8rem; font-weight: bold; color: white; }
        .user-info { color: white; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .dashboard { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .card h3 { color: #2c3e50; margin-bottom: 1rem; border-bottom: 2px solid #3498db; padding-bottom: 0.5rem; }
        .btn { background: #3498db; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #2980b9; }
        .btn-danger { background: #e74c3c; }
        .btn-danger:hover { background: #c0392b; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .form-group { margin-bottom: 1rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; }
        .form-group input, .form-group select { width: 100%; padding: 0.5rem; border: 1px solid #ddd; border-radius: 5px; }
        .demo-info { background: #d1ecf1; color: #0c5460; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border-left: 4px solid #0dcaf0; }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">SFT - Esercizio</div>
            <div class="user-info">
                Backoffice Esercizio | <?php echo $_SESSION['user_name']; ?>
                <a href="logout.php" style="color: white; margin-left: 1rem;">Logout</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Backoffice Esercizio</h1>
        <p>Gestione convogli, orari e materiale rotabile</p>

        <div class="demo-info">
            <strong>📅 Demo Attiva:</strong> Treni programmati per il 20-21 Gennaio 2024
        </div>

        <div class="dashboard">
            <div class="card">
                <h3>🚂 Gestione Convogli</h3>
                <?php if ($db_connected): 
                    $convogli = $conn->query("SELECT * FROM proj1_Convoglio WHERE attivo = 1");
                    if ($convogli && $convogli->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Convoglio</th><th>Composizione</th><th>Posti Totali</th></tr>
                            <?php while($convoglio = $convogli->fetch_assoc()): 
                                $composizione = $conn->query("
                                    SELECT GROUP_CONCAT(CONCAT(mr.tipo, ' ', mr.modello) SEPARATOR ' + ') as mezzi,
                                           SUM(mr.posti) as posti_totali
                                    FROM proj1_Composizione c
                                    JOIN proj1_MaterialeRotabile mr ON c.id_mezzo = mr.id_mezzo
                                    WHERE c.id_convoglio = {$convoglio['id_convoglio']}
                                ");
                                $comp_data = $composizione->fetch_assoc();
                            ?>
                                <tr>
                                    <td><strong><?php echo $convoglio['nome']; ?></strong></td>
                                    <td><?php echo $comp_data['mezzi'] ?? 'Nessun mezzo'; ?></td>
                                    <td><?php echo $comp_data['posti_totali'] ?? 0; ?> posti</td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>Nessun convoglio attivo</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Dati non disponibili</p>
                <?php endif; ?>
                
                <h4 style="margin-top: 1.5rem;">Crea Nuovo Convoglio</h4>
                <form method="POST" action="crea_convoglio.php">
                    <div class="form-group">
                        <input type="text" name="nome_convoglio" placeholder="Nome convoglio" required>
                    </div>
                    <button type="submit" class="btn">Crea Convoglio</button>
                </form>
            </div>

            <div class="card">
                <h3>🕒 Gestione Orari</h3>
                <?php if ($db_connected): 
                    $orari = $conn->query("SELECT * FROM proj1_OrarioProgrammato WHERE attivo_a IS NULL OR attivo_a >= CURDATE()");
                    if ($orari && $orari->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Nome Corsa</th><th>Direzione</th><th>Tipo</th><th>Giorni</th><th>Azioni</th></tr>
                            <?php while($orario = $orari->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $orario['nome_corsa']; ?></td>
                                    <td><?php echo $orario['direzione']; ?></td>
                                    <td><?php echo $orario['tipo_periodo']; ?></td>
                                    <td><?php echo $orario['giorni_settimana']; ?></td>
                                    <td>
                                        <a href="modifica_orario.php?id=<?php echo $orario['id_orario']; ?>" class="btn" style="padding: 0.3rem 0.8rem;">Modifica</a>
                                        <form method="POST" action="cancella_orario.php" style="display: inline;">
                                            <input type="hidden" name="id_orario" value="<?php echo $orario['id_orario']; ?>">
                                            <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.8rem;">Cancella</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>Nessun orario programmato</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p>Dati non disponibili</p>
                <?php endif; ?>
                
                <a href="crea_orario.php" class="btn" style="margin-top: 1rem;">Crea Nuovo Orario</a>
            </div>
        </div>

        <div class="card">
            <h3>🔧 Materiale Rotabile Disponibile</h3>
            <?php if ($db_connected): 
                $materiale = $conn->query("SELECT * FROM proj1_MaterialeRotabile WHERE attivo = 1 ORDER BY tipo, modello");
                if ($materiale && $materiale->num_rows > 0): ?>
                    <table class="table">
                        <tr><th>Tipo</th><th>Modello</th><th>Posti</th><th>Stato</th></tr>
                        <?php while($mezzo = $materiale->fetch_assoc()): 
                            $in_uso = $conn->query("SELECT 1 FROM proj1_Composizione WHERE id_mezzo = {$mezzo['id_mezzo']}")->num_rows > 0;
                        ?>
                            <tr>
                                <td><?php echo $mezzo['tipo']; ?></td>
                                <td><?php echo $mezzo['modello']; ?></td>
                                <td><?php echo $mezzo['posti']; ?></td>
                                <td>
                                    <?php if ($in_uso): ?>
                                        <span style="color: #27ae60;">● In uso</span>
                                    <?php else: ?>
                                        <span style="color: #e74c3c;">● Disponibile</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Nessun materiale rotabile</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Dati non disponibili</p>
            <?php endif; ?>
        </div>

        <div class="card">
            <h3>📅 Treni Demo Programmati</h3>
            <?php if ($db_connected): 
                $treni = $conn->query("
                    SELECT t.*, c.nome as convoglio, 
                           (SELECT COUNT(*) FROM proj1_Prenotazione WHERE id_treno = t.id_treno) as prenotazioni
                    FROM proj1_Treno t 
                    JOIN proj1_Convoglio c ON t.id_convoglio = c.id_convoglio 
                    WHERE t.data_corsa IN ('2024-01-20', '2024-01-21')
                    ORDER BY t.data_corsa, t.orario_partenza
                ");
                if ($treni && $treni->num_rows > 0): ?>
                    <table class="table">
                        <tr><th>Data</th><th>Ora</th><th>Convoglio</th><th>Direzione</th><th>Prenotazioni</th><th>Azioni</th></tr>
                        <?php while($treno = $treni->fetch_assoc()): 
                            $data_italiana = date('d/m/Y', strtotime($treno['data_corsa']));
                        ?>
                            <tr>
                                <td><?php echo $data_italiana; ?></td>
                                <td><?php echo $treno['orario_partenza']; ?></td>
                                <td><?php echo $treno['convoglio']; ?></td>
                                <td><?php echo $treno['direzione']; ?></td>
                                <td><?php echo $treno['prenotazioni']; ?></td>
                                <td>
                                    <a href="modifica_treno.php?id=<?php echo $treno['id_treno']; ?>" class="btn" style="padding: 0.3rem 0.8rem;">Modifica</a>
                                    <form method="POST" action="cancella_treno.php" style="display: inline;">
                                        <input type="hidden" name="id_treno" value="<?php echo $treno['id_treno']; ?>">
                                        <button type="submit" class="btn btn-danger" style="padding: 0.3rem 0.8rem;">Cancella</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Nessun treno programmato per la demo</p>
                <?php endif; ?>
            <?php else: ?>
                <p>Dati non disponibili</p>
            <?php endif; ?>
            
            <a href="crea_treno.php" class="btn" style="margin-top: 1rem;">Programma Nuovo Treno</a>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>