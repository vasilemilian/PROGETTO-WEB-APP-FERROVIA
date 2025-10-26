<?php
header("X-Accel-Buffering: no");
header("Cache-Control: no-cache");

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
    <title>Orari - SFT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 1rem 0; }
        .nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #e74c3c; }
        .logo span { color: white; }
        .nav-links { display: flex; gap: 2rem; }
        .nav-links a { color: white; text-decoration: none; font-weight: 500; padding: 0.5rem 1rem; border-radius: 4px; }
        .nav-links a:hover { color: #e74c3c; background: rgba(255,255,255,0.1); }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .card { background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 2rem; }
        .card h2 { color: #2c3e50; margin-bottom: 1.5rem; border-bottom: 2px solid #e74c3c; padding-bottom: 0.5rem; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .table tr:hover { background: #f8f9fa; }
        .orario-section { margin-bottom: 3rem; }
        .orario-title { background: #34495e; color: white; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; }
        .btn { background: #e74c3c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #c0392b; }
        .tabs { display: flex; margin-bottom: 2rem; border-bottom: 2px solid #34495e; }
        .tab { padding: 1rem 2rem; background: #ecf0f1; border: none; cursor: pointer; font-size: 1rem; }
        .tab.active { background: #34495e; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .stazione-orario { font-weight: bold; color: #e74c3c; }
        .note { background: #fff3cd; padding: 1rem; border-radius: 5px; margin: 1rem 0; border-left: 4px solid #ffc107; }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">SFT<span>.it</span></div>
            <div class="nav-links">
                <a href="index.php">Home</a>
                <a href="orari.php">Orari</a>
                <a href="convogli.php">Convogli Storici</a>
                <a href="linea.php">La Linea</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <h1>Orari dei Treni</h1>
        <p>Consulta gli orari della linea Torre Spaventa - Villa San Felice</p>

        <!-- Tabs -->
        <div class="tabs">
            <button class="tab active" onclick="openTab('festivi')">Festivi</button>
            <button class="tab" onclick="openTab('feriali')">Feriali Estate</button>
            <button class="tab" onclick="openTab('domani')">Treni Demo</button>
        </div>

        <!-- Tab Festivi -->
        <div id="festivi" class="tab-content active">
            <div class="card">
                <h2>🎉 Orari Festivi (Sabato e Domenica)</h2>
                
                <div class="orario-section">
                    <div class="orario-title">📍 Direzione SUD (Torre Spaventa → Villa San Felice)</div>
                    <?php if ($db_connected): 
                        $sql = "SELECT op.nome_corsa, fp.ordine, s.nome as stazione, 
                                       fp.arrivo_previsto, fp.partenza_prevista
                                FROM proj1_FermataProgrammata fp
                                JOIN proj1_OrarioProgrammato op ON fp.id_orario = op.id_orario
                                JOIN proj1_Stazione s ON fp.id_stazione = s.id_stazione
                                WHERE op.nome_corsa LIKE '%Festivo%' AND op.direzione = 'SUD'
                                ORDER BY fp.id_orario, fp.ordine";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0): ?>
                            <table class="table">
                                <tr><th>Corsa</th><th>Stazione</th><th>Ordine</th><th>Arrivo</th><th>Partenza</th></tr>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['nome_corsa']; ?></td>
                                        <td class="stazione-orario"><?php echo $row['stazione']; ?></td>
                                        <td><?php echo $row['ordine']; ?></td>
                                        <td><?php echo $row['arrivo_previsto']; ?></td>
                                        <td><?php echo $row['partenza_prevista']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        <?php else: ?>
                            <p>Nessun orario festivo disponibile.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <table class="table">
                            <tr><th>Corsa</th><th>Stazione</th><th>Arrivo</th><th>Partenza</th></tr>
                            <tr><td>Festivo Mattina SUD</td><td>Torre Spaventa</td><td>08:00</td><td>08:05</td></tr>
                            <tr><td>Festivo Mattina SUD</td><td>Prato Terra</td><td>08:09</td><td>08:10</td></tr>
                            <tr><td>Festivo Mattina SUD</td><td>Rocca Pietrosa</td><td>08:16</td><td>08:17</td></tr>
                            <tr><td>Festivo Mattina SUD</td><td>Villa San Felice</td><td>09:25</td><td>09:25</td></tr>
                        </table>
                    <?php endif; ?>
                </div>

                <div class="orario-section">
                    <div class="orario-title">📍 Direzione NORD (Villa San Felice → Torre Spaventa)</div>
                    <?php if ($db_connected): 
                        $sql = "SELECT op.nome_corsa, fp.ordine, s.nome as stazione, 
                                       fp.arrivo_previsto, fp.partenza_prevista
                                FROM proj1_FermataProgrammata fp
                                JOIN proj1_OrarioProgrammato op ON fp.id_orario = op.id_orario
                                JOIN proj1_Stazione s ON fp.id_stazione = s.id_stazione
                                WHERE op.nome_corsa LIKE '%Festivo%' AND op.direzione = 'NORD'
                                ORDER BY fp.id_orario, fp.ordine";
                        $result = $conn->query($sql);
                        if ($result && $result->num_rows > 0): ?>
                            <table class="table">
                                <tr><th>Corsa</th><th>Stazione</th><th>Ordine</th><th>Arrivo</th><th>Partenza</th></tr>
                                <?php while($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['nome_corsa']; ?></td>
                                        <td class="stazione-orario"><?php echo $row['stazione']; ?></td>
                                        <td><?php echo $row['ordine']; ?></td>
                                        <td><?php echo $row['arrivo_previsto']; ?></td>
                                        <td><?php echo $row['partenza_prevista']; ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </table>
                        <?php else: ?>
                            <p>Nessun orario festivo disponibile.</p>
                        <?php endif; ?>
                    <?php else: ?>
                        <table class="table">
                            <tr><th>Corsa</th><th>Stazione</th><th>Arrivo</th><th>Partenza</th></tr>
                            <tr><td>Festivo Mattina NORD</td><td>Villa San Felice</td><td>08:30</td><td>08:35</td></tr>
                            <tr><td>Festivo Mattina NORD</td><td>Porto San Felice</td><td>08:43</td><td>08:44</td></tr>
                            <tr><td>Festivo Mattina NORD</td><td>Torre Spaventa</td><td>09:50</td><td>09:50</td></tr>
                        </table>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Tab Feriali -->
        <div id="feriali" class="tab-content">
            <div class="card">
                <h2>📅 Orari Feriali Estate (Lunedì-Venerdì, 1 Giugno - 30 Settembre)</h2>
                
                <?php if ($db_connected): 
                    $sql = "SELECT op.nome_corsa, op.direzione, op.giorni_settimana, op.tipo_periodo
                            FROM proj1_OrarioProgrammato op
                            WHERE op.nome_corsa LIKE '%Estate%'";
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Corsa</th><th>Direzione</th><th>Giorni</th><th>Periodo</th></tr>
                            <?php while($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['nome_corsa']; ?></td>
                                    <td><?php echo $row['direzione']; ?></td>
                                    <td><?php echo $row['giorni_settimana']; ?></td>
                                    <td><?php echo $row['tipo_periodo']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                        
                        <div class="note">
                            <strong>⚠️ Nota:</strong> Gli orari dettagliati per le corse feriali estive sono in fase di definizione. 
                            I treni partono ogni 2 ore dalle 08:00 alle 18:00 in entrambe le direzioni.
                        </div>
                    <?php else: ?>
                        <p>Nessun orario feriale disponibile.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <table class="table">
                        <tr><th>Corsa</th><th>Direzione</th><th>Giorni</th><th>Partenza</th></tr>
                        <tr><td>FerialEstate SUD</td><td>SUD</td><td>LUN-VEN</td><td>08:00, 10:00, 12:00, 14:00, 16:00, 18:00</td></tr>
                        <tr><td>FerialEstate NORD</td><td>NORD</td><td>LUN-VEN</td><td>08:30, 10:30, 12:30, 14:30, 16:30, 18:30</td></tr>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tab Treni Demo -->
        <div id="domani" class="tab-content">
            <div class="card">
                <h2>🚆 Treni Demo Disponibili (20-21 Gennaio 2024)</h2>
                
                <?php if ($db_connected): 
                    $sql = "SELECT t.id_treno, t.data_corsa, t.orario_partenza, t.direzione, 
                                   c.nome as convoglio,
                                   s1.nome as partenza, s2.nome as arrivo
                            FROM proj1_Treno t
                            JOIN proj1_Fermata f1 ON t.id_treno = f1.id_treno AND f1.ordine = 1
                            JOIN proj1_Fermata f2 ON t.id_treno = f2.id_treno AND f2.ordine = (
                                SELECT MAX(ordine) FROM proj1_Fermata WHERE id_treno = t.id_treno
                            )
                            JOIN proj1_Stazione s1 ON f1.id_stazione = s1.id_stazione
                            JOIN proj1_Stazione s2 ON f2.id_stazione = s2.id_stazione
                            JOIN proj1_Convoglio c ON t.id_convoglio = c.id_convoglio
                            WHERE t.data_corsa IN ('2024-01-20', '2024-01-21')
                            AND t.stato = 'PROGRAMMATO'
                            ORDER BY t.data_corsa, t.orario_partenza";
                    
                    $result = $conn->query($sql);
                    if ($result && $result->num_rows > 0): ?>
                        <table class="table">
                            <tr><th>Data</th><th>Treno</th><th>Ora</th><th>Partenza</th><th>Arrivo</th><th>Convoglio</th></tr>
                            <?php while($row = $result->fetch_assoc()): 
                                $data_italiana = date('d/m/Y', strtotime($row['data_corsa']));
                            ?>
                                <tr>
                                    <td><strong><?php echo $data_italiana; ?></strong></td>
                                    <td>TR<?php echo str_pad($row['id_treno'], 3, '0', STR_PAD_LEFT); ?></td>
                                    <td><?php echo $row['orario_partenza']; ?></td>
                                    <td><?php echo $row['partenza']; ?></td>
                                    <td><?php echo $row['arrivo']; ?></td>
                                    <td><?php echo $row['convoglio']; ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </table>
                    <?php else: ?>
                        <p>Nessun treno disponibile per la demo.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <table class="table">
                        <tr><th>Data</th><th>Treno</th><th>Ora</th><th>Partenza</th><th>Arrivo</th><th>Convoglio</th></tr>
                        <tr><td>20/01/2024</td><td>TR001</td><td>08:00</td><td>Torre Spaventa</td><td>Villa San Felice</td><td>Storico A</td></tr>
                        <tr><td>20/01/2024</td><td>TR002</td><td>08:00</td><td>Villa San Felice</td><td>Torre Spaventa</td><td>Storico B</td></tr>
                        <tr><td>21/01/2024</td><td>TR003</td><td>10:00</td><td>Torre Spaventa</td><td>Villa San Felice</td><td>Rapido C</td></tr>
                    </table>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="login.php" class="btn">Accedi per Prenotare</a>
                </div>
            </div>
        </div>

        <div class="note">
            <strong>💡 Informazioni:</strong> 
            • La durata del viaggio completo è di circa 1 ora e 25 minuti<br>
            • I convogli storici possono subire variazioni per manutenzione<br>
            • Si consiglia di presentarsi in stazione 15 minuti prima della partenza
        </div>
    </div>

    <script>
        function openTab(tabName) {
            // Nascondi tutti i tab content
            var tabcontents = document.getElementsByClassName("tab-content");
            for (var i = 0; i < tabcontents.length; i++) {
                tabcontents[i].classList.remove("active");
            }

            // Rimuovi active da tutti i tab
            var tabs = document.getElementsByClassName("tab");
            for (var i = 0; i < tabs.length; i++) {
                tabs[i].classList.remove("active");
            }

            // Mostra il tab specifico e attivalo
            document.getElementById(tabName).classList.add("active");
            event.currentTarget.classList.add("active");
        }
    </script>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>