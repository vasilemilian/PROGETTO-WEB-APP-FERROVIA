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
    <title>La Linea - SFT</title>
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
        .hero-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 2rem 0; }
        .stat-card { background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%); color: white; padding: 1.5rem; border-radius: 10px; text-align: center; }
        .stat-number { font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; }
        .stations-timeline { position: relative; margin: 3rem 0; }
        .station-item { display: flex; align-items: center; margin-bottom: 2rem; position: relative; }
        .station-dot { width: 20px; height: 20px; background: #e74c3c; border-radius: 50%; position: absolute; left: 0; }
        .station-content { margin-left: 3rem; padding: 1.5rem; background: white; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); flex: 1; }
        .station-km { color: #e74c3c; font-weight: bold; margin-bottom: 0.5rem; }
        .line { position: absolute; left: 10px; top: 0; bottom: 0; width: 2px; background: #e74c3c; z-index: -1; }
        .map-container { background: #ecf0f1; padding: 2rem; border-radius: 10px; margin: 2rem 0; text-align: center; }
        .table { width: 100%; border-collapse: collapse; margin: 1rem 0; }
        .table th, .table td { padding: 1rem; text-align: left; border-bottom: 1px solid #ecf0f1; }
        .table th { background: #34495e; color: white; }
        .btn { background: #e74c3c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; display: inline-block; }
        .btn:hover { background: #c0392b; }
        .feature-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem; margin: 2rem 0; }
        .feature-item { text-align: center; padding: 1.5rem; }
        .feature-icon { font-size: 3rem; margin-bottom: 1rem; }
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
        <h1>🗺️ La Linea Torre Spaventa - Villa San Felice</h1>
        <p>Scopri i 54km di paesaggi mozzafiato della nostra linea ferroviaria storica</p>

        <!-- Statistiche -->
        <div class="hero-stats">
            <div class="stat-card">
                <div class="stat-number">54.68</div>
                <div>km di linea</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">10</div>
                <div>stazioni</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">85</div>
                <div>minuti di viaggio</div>
            </div>
            <div class="stat-card">
                <div class="stat-number">1925</div>
                <div>anno di apertura</div>
            </div>
        </div>

        <div class="card">
            <h2>🎯 Caratteristiche della Linea</h2>
            <div class="feature-grid">
                <div class="feature-item">
                    <div class="feature-icon">🏞️</div>
                    <h3>Panorami Unici</h3>
                    <p>Attraversa valli nascoste, coste rocciose e borghi medievali accessibili solo via treno.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🚂</div>
                    <h3>Storia Vivente</h3>
                    <p>Linea inaugurata nel 1925, perfettamente mantenuta con tecnologie originali.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🌉</div>
                    <h3>Opere d'Ingengeria</h3>
                    <p>8 ponti in pietra, 3 viadotti e 5 gallerie che sfidano la montagna.</p>
                </div>
                <div class="feature-item">
                    <div class="feature-icon">🦅</div>
                    <h3>Natura Protetta</h3>
                    <p>Attraversa 2 riserve naturali con avvistamenti di aquile e lupi.</p>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📍 Le Nostre Stazioni</h2>
            <div class="stations-timeline">
                <div class="line"></div>
                
                <!-- Stazione 1 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 0.000 - Stazione Capolinea</div>
                        <h3>🚉 Torre Spaventa</h3>
                        <p><strong>Città:</strong> Torre Spaventa</p>
                        <p>Stazione di origine della linea. Edificio storico del 1925 con biglietteria originale e museo ferroviario. Punto di partenza per tutti i viaggi verso sud.</p>
                    </div>
                </div>

                <!-- Stazione 2 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 2.700</div>
                        <h3>🚉 Prato Terra</h3>
                        <p><strong>Città:</strong> Prato Terra</p>
                        <p>Piccola stazione rurale che serve le comunità agricole della valle. Fermata su richiesta per escursionisti.</p>
                    </div>
                </div>

                <!-- Stazione 3 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 7.580</div>
                        <h3>🚉 Rocca Pietrosa</h3>
                        <p><strong>Città:</strong> Rocca Pietrosa</p>
                        <p>Stazione ai piedi dell'antico castello medievale. Punto di accesso per sentieri escursionistici verso la rocca.</p>
                    </div>
                </div>

                <!-- Stazione 4 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 12.680</div>
                        <h3>🚉 Villa Pietrosa</h3>
                        <p><strong>Città:</strong> Villa Pietrosa</p>
                        <p>Stazione che serve il borgo storico famoso per le sue ville in pietra del '700. Fermata obbligatoria per il servizio turistico.</p>
                    </div>
                </div>

                <!-- Stazione 5 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 16.900</div>
                        <h3>🚉 Villa Santa Maria</h3>
                        <p><strong>Città:</strong> Villa Santa Maria</p>
                        <p>Importante stazione di scambio. Qui si trova il deposito locomotive storico e l'officina di manutenzione.</p>
                    </div>
                </div>

                <!-- Stazione 6 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 23.950</div>
                        <h3>🚉 Pietra Santa Maria</h3>
                        <p><strong>Città:</strong> Pietra Santa Maria</p>
                        <p>Stazione nel cuore della riserva naturale. Punto di partenza per trekking e osservazione della fauna.</p>
                    </div>
                </div>

                <!-- Stazione 7 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 31.500</div>
                        <h3>🚉 Castro Marino</h3>
                        <p><strong>Città:</strong> Castro Marino</p>
                        <p>Stazione costiera con vista sull'Adriatico. Accesso alle spiagge nascoste e alle grotte marine.</p>
                    </div>
                </div>

                <!-- Stazione 8 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 39.500</div>
                        <h3>🚉 Porto Spigola</h3>
                        <p><strong>Città:</strong> Porto Spigola</p>
                        <p>Stazione portuale che collega il servizio ferroviario con i battelli per le isole. Mercato del pesce giornaliero.</p>
                    </div>
                </div>

                <!-- Stazione 9 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 46.000</div>
                        <h3>🚉 Porto San Felice</h3>
                        <p><strong>Città:</strong> Porto San Felice</p>
                        <p>Penultima stazione prima del capolinea. Servizi completi per i viaggiatori e area ristoro.</p>
                    </div>
                </div>

                <!-- Stazione 10 -->
                <div class="station-item">
                    <div class="station-dot"></div>
                    <div class="station-content">
                        <div class="station-km">KM 54.680 - Stazione Capolinea</div>
                        <h3>🚉 Villa San Felice</h3>
                        <p><strong>Città:</strong> Villa San Felice</p>
                        <p>Capolinea meridionale. Stazione principale con servizi completi, museo e centro visitatori. Punto di partenza per i viaggi verso nord.</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <h2>📊 Dettagli Tecnici</h2>
            <?php if ($db_connected): 
                $sql = "SELECT s.nome, s.citta, s.km_linea, 
                               (SELECT COUNT(*) FROM proj1_SubTratta st WHERE st.id_staz_da = s.id_stazione) as tratte_uscenti
                        FROM proj1_Stazione s
                        ORDER BY s.km_linea";
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0): ?>
                    <table class="table">
                        <tr><th>Stazione</th><th>Città</th><th>KM Linea</th><th>Tratte</th></tr>
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo $row['nome']; ?></strong></td>
                                <td><?php echo $row['citta']; ?></td>
                                <td><?php echo number_format($row['km_linea'], 3); ?> km</td>
                                <td><?php echo $row['tratte_uscenti']; ?> tratte</td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p>Dati non disponibili.</p>
                <?php endif; ?>
            <?php else: ?>
                <table class="table">
                    <tr><th>Stazione</th><th>Città</th><th>KM Linea</th></tr>
                    <tr><td>Torre Spaventa</td><td>Torre Spaventa</td><td>0.000 km</td></tr>
                    <tr><td>Prato Terra</td><td>Prato Terra</td><td>2.700 km</td></tr>
                    <tr><td>Rocca Pietrosa</td><td>Rocca Pietrosa</td><td>7.580 km</td></tr>
                    <tr><td>Villa San Felice</td><td>Villa San Felice</td><td>54.680 km</td></tr>
                </table>
            <?php endif; ?>
        </div>

        <div class="map-container">
            <h2>🗺️ Mappa della Linea</h2>
            <p>La linea si snoda attraverso paesaggi vari e mozzafiato:</p>
            <div style="background: #bdc3c7; height: 300px; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 1rem 0;">
                <div style="text-align: center; color: #2c3e50;">
                    <div style="font-size: 4rem;">🗺️</div>
                    <p><strong>Mappa Interattiva della Linea</strong></p>
                    <p>Torre Spaventa ←→ Villa San Felice</p>
                    <p>54.68km di paesaggi straordinari</p>
                </div>
            </div>
            <p><em>La mappa interattiva mostra il percorso completo con punti di interesse, aree naturalistiche e servizi turistici.</em></p>
        </div>

        <div style="text-align: center; margin-top: 2rem;">
            <a href="orari.php" class="btn">Consulta gli Orari</a>
            <a href="convogli.php" class="btn" style="background: #3498db; margin-left: 1rem;">Scopri i Convogli</a>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>