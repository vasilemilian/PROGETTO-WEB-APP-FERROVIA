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
    <title>Convogli Storici - SFT</title>
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
        .convoglio-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .convoglio-card { background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .convoglio-card:hover { transform: translateY(-5px); }
        .convoglio-header { background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); color: white; padding: 1.5rem; text-align: center; }
        .convoglio-body { padding: 1.5rem; }
        .convoglio-storico { border: 3px solid #e74c3c; }
        .convoglio-moderno { border: 3px solid #3498db; }
        .badge { display: inline-block; padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.8rem; font-weight: bold; margin-bottom: 1rem; }
        .badge-storico { background: #e74c3c; color: white; }
        .badge-moderno { background: #3498db; color: white; }
        .mezzo-list { list-style: none; padding: 0; }
        .mezzo-list li { padding: 0.5rem 0; border-bottom: 1px solid #ecf0f1; display: flex; justify-content: space-between; }
        .mezzo-list li:last-child { border-bottom: none; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .btn { background: #e74c3c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #c0392b; }
        .icon { font-size: 2rem; margin-bottom: 1rem; }
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
        <h1>🚂 I Nostri Convogli Storici</h1>
        <p>Viaggia su autentici treni d'epoca perfettamente mantenuti</p>

        <div class="convoglio-grid">
            <!-- Convoglio Storico A -->
            <div class="convoglio-card convoglio-storico">
                <div class="convoglio-header">
                    <div class="icon">🚂</div>
                    <h3>Convoglio Storico A</h3>
                    <span class="badge badge-storico">⚡ ANNI '50</span>
                </div>
                <div class="convoglio-body">
                    <p><strong>Composizione:</strong></p>
                    <ul class="mezzo-list">
                        <li>Locomotiva SFT.3 Cavour <span>🚇</span></li>
                        <li>Carrozza B1 (36 posti) <span>🚃</span></li>
                        <li>Carrozza B2 (36 posti) <span>🚃</span></li>
                    </ul>
                    <p><strong>Posti totali:</strong> 72 posti</p>
                    <p><strong>Periodo storico:</strong> Anni 1950-1960</p>
                    <p><strong>Caratteristiche:</strong> Locomotiva a vapore originale, interni in legno</p>
                </div>
            </div>

            <!-- Convoglio Storico B -->
            <div class="convoglio-card convoglio-storico">
                <div class="convoglio-header">
                    <div class="icon">🚞</div>
                    <h3>Convoglio Storico B</h3>
                    <span class="badge badge-storico">⚡ ANNI '60</span>
                </div>
                <div class="convoglio-body">
                    <p><strong>Composizione:</strong></p>
                    <ul class="mezzo-list">
                        <li>Locomotiva SFT.4 Vittorio Emanuele <span>🚇</span></li>
                        <li>Carrozza C6 (48 posti) <span>🚃</span></li>
                        <li>Carrozza C9 (48 posti) <span>🚃</span></li>
                        <li>Bagagliaio CD1 (12 posti) <span>🎒</span></li>
                    </ul>
                    <p><strong>Posti totali:</strong> 108 posti</p>
                    <p><strong>Periodo storico:</strong> Anni 1960-1970</p>
                    <p><strong>Caratteristiche:</strong> Design italiano anni '60, comfort migliorato</p>
                </div>
            </div>

            <!-- Convoglio Rapido C -->
            <div class="convoglio-card convoglio-moderno">
                <div class="convoglio-header">
                    <div class="icon">🚄</div>
                    <h3>Convoglio Rapido C</h3>
                    <span class="badge badge-moderno">⚡ MODERNO</span>
                </div>
                <div class="convoglio-body">
                    <p><strong>Composizione:</strong></p>
                    <ul class="mezzo-list">
                        <li>Automotrice AN56.2 (56 posti) <span>🚅</span></li>
                    </ul>
                    <p><strong>Posti totali:</strong> 56 posti</p>
                    <p><strong>Periodo:</strong> Moderno</p>
                    <p><strong>Caratteristiche:</strong> Servizio rapido, aria condizionata</p>
                </div>
            </div>

            <!-- Convoglio Turistico D -->
            <div class="convoglio-card convoglio-storico">
                <div class="convoglio-header">
                    <div class="icon">🎯</div>
                    <h3>Convoglio Turistico D</h3>
                    <span class="badge badge-storico">⚡ MISTO</span>
                </div>
                <div class="convoglio-body">
                    <p><strong>Composizione:</strong></p>
                    <ul class="mezzo-list">
                        <li>Locomotiva SFT.6 Garibaldi <span>🚇</span></li>
                        <li>Bagagliaio CD2 (12 posti) <span>🎒</span></li>
                        <li>Carrozza B3 (36 posti) <span>🚃</span></li>
                        <li>Carrozza C12 (52 posti) <span>🚃</span></li>
                    </ul>
                    <p><strong>Posti totali:</strong> 100 posti</p>
                    <p><strong>Periodo:</strong> Misto storico-moderno</p>
                    <p><strong>Caratteristiche:</strong> Ideale per gruppi turistici</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📊 Materiale Rotabile Disponibile</h2>
            <?php if ($db_connected): 
                $sql = "SELECT mr.tipo, mr.modello, mr.posti, 
                               COUNT(DISTINCT c.id_convoglio) as in_uso,
                               GROUP_CONCAT(DISTINCT co.nome SEPARATOR ', ') as convogli
                        FROM proj1_MaterialeRotabile mr
                        LEFT JOIN proj1_Composizione comp ON mr.id_mezzo = comp.id_mezzo
                        LEFT JOIN proj1_Convoglio co ON comp.id_convoglio = co.id_convoglio
                        WHERE mr.attivo = 1
                        GROUP BY mr.id_mezzo
                        ORDER BY mr.tipo, mr.modello";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0): ?>
                    <table class="table">
                        <tr><th>Tipo</th><th>Modello</th><th>Posti</th><th>In Uso</th><th>Convogli</th></tr>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $row['tipo']; ?></strong></td>
                                <td><?php echo $row['modello']; ?></td>
                                <td><?php echo $row['posti']; ?> posti</td>
                                <td><?php echo $row['in_uso'] ? '✅' : '❌'; ?></td>
                                <td><?php echo $row['convogli'] ?: 'Nessuno'; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Nessun materiale rotabile disponibile.</p>
                <?php endif; ?>
            <?php else: ?>
                <table class="table">
                    <tr><th>Tipo</th><th>Modello</th><th>Posti</th><th>Stato</th></tr>
                    <tr><td>Locomotiva</td><td>SFT.3 Cavour</td><td>0</td><td>✅ In uso</td></tr>
                    <tr><td>Carrozza</td><td>B1</td><td>36</td><td>✅ In uso</td></tr>
                    <tr><td>Carrozza</td><td>B2</td><td>36</td><td>✅ In uso</td></tr>
                    <tr><td>Automotrice</td><td>AN56.2</td><td>56</td><td>✅ In uso</td></tr>
                </table>
            <?php endif; ?>
        </div>

        <div class="card">
            <h2>🎭 Esperienza di Viaggio Storica</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                <div>
                    <h3>🚂 Viaggio nel Tempo</h3>
                    <p>Salire su questi convogli è come fare un viaggio nel tempo. I sedili in velluto, i finestrini con cornici in ottone e i dettagli in legno ti trasportano negli anni '50.</p>
                </div>
                <div>
                    <h3>📸 Fotografia</h3>
                    <p>I nostri treni storici sono perfetti per gli appassionati di fotografia. Scatta foto indimenticabili con panorami mozzafiato come sfondo.</p>
                </div>
                <div>
                    <h3>🎉 Eventi Speciali</h3>
                    <p>Organizziamo regolarmente eventi a tema: cene viaggianti, rievocazioni storiche e viaggi fotografici guidati.</p>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <a href="login.php" class="btn">Prenota il Tuo Viaggio Storico</a>
            </div>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>