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
    <title>SFT - Società Ferrovie Turistiche</title>
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
        .hero { background: linear-gradient(rgba(44, 62, 80, 0.8), rgba(52, 73, 94, 0.8)); color: white; text-align: center; padding: 6rem 1rem; }
        .hero h1 { font-size: 3rem; margin-bottom: 1rem; }
        .btn { display: inline-block; background: #e74c3c; color: white; padding: 1rem 2rem; text-decoration: none; border-radius: 50px; font-weight: bold; transition: all 0.3s; border: 2px solid #e74c3c; margin: 0.5rem; }
        .btn:hover { background: transparent; color: #e74c3c; transform: translateY(-2px); }
        .btn-secondary { background: transparent; border-color: white; }
        .btn-secondary:hover { background: white; color: #2c3e50; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem; margin: 4rem 0; }
        .feature { text-align: center; padding: 2rem; background: white; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s; }
        .feature:hover { transform: translateY(-5px); }
        .feature-icon { font-size: 3rem; margin-bottom: 1rem; }
        .simple-table { width: 100%; border-collapse: collapse; margin: 1rem 0; background: white; border-radius: 5px; overflow: hidden; box-shadow: 0 2px 5px rgba(0,0,0,0.1); }
        .simple-table th, .simple-table td { padding: 0.75rem; text-align: left; border-bottom: 1px solid #dee2e6; }
        .simple-table th { background: #34495e; color: white; }
        .stations-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin: 1rem 0; }
        .station-card { background: white; padding: 1rem; border-radius: 5px; box-shadow: 0 2px 5px rgba(0,0,0,0.1); text-align: center; }
        .station-km { color: #e74c3c; font-weight: bold; margin-top: 0.5rem; }
        footer { background: #2c3e50; color: white; text-align: center; padding: 2rem; margin-top: 4rem; }
        .access-buttons { text-align: center; margin: 2rem 0; }
        .access-btn { display: inline-block; margin: 0 1rem; padding: 1rem 2rem; background: #3498db; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; }
        .access-btn.admin { background: #e74c3c; }
        .access-btn.esercizio { background: #27ae60; }
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

    <section class="hero">
        <h1>Società Ferrovie Turistiche</h1>
        <p>Viaggia attraverso 54km di paesaggi mozzafiato sulla nostra linea storica</p>
        <div>
            <a href="orari.php" class="btn">Consulta gli orari</a>
            <a href="login.php" class="btn btn-secondary">Accedi per prenotare</a>
        </div>
    </section>

    <div class="container">
        <!-- Accesso alle aree riservate -->
        <div class="access-buttons">
            <a href="login.php?redirect=cliente" class="access-btn">Area Clienti</a>
            <a href="login.php?redirect=admin" class="access-btn admin">Backoffice Amministrativo</a>
            <a href="login.php?redirect=esercizio" class="access-btn esercizio">Backoffice Esercizio</a>
        </div>

        <div class="features">
            <div class="feature">
                <div class="feature-icon">🚆</div>
                <h3>Linea Panoramica</h3>
                <p>54km attraverso 10 stazioni caratteristiche, da Torre Spaventa a Villa San Felice. Scopri paesaggi unici accessibili solo con i nostri treni storici.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🎫</div>
                <h3>Prenotazione Online</h3>
                <p>Registrati e prenota il tuo posto a sedere online in pochi click. Sistema sicuro e garantito.</p>
            </div>
            <div class="feature">
                <div class="feature-icon">🏛️</div>
                <h3>Treno Storico</h3>
                <p>Viaggia su convogli d'epoca perfettamente mantenuti. Locomotive a vapore e carrozze originali degli anni '50.</p>
            </div>
        </div>

        <!-- Prossime Partenze -->
        <div style="background: white; border-radius: 10px; padding: 2rem; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin: 2rem 0;">
            <h2>Prossime Partenze Demo</h2>
            <?php if ($db_connected): ?>
                <?php
                $sql = "SELECT t.id_treno, t.data_corsa, t.orario_partenza, t.direzione, 
                               s1.nome as partenza, s2.nome as arrivo, c.nome as convoglio
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
                        ORDER BY t.data_corsa, t.orario_partenza 
                        LIMIT 3";
                
                $result = $conn->query($sql);
                if ($result && $result->num_rows > 0): ?>
                    <table class="simple-table">
                        <tr><th>Treno</th><th>Data</th><th>Ora</th><th>Partenza</th><th>Arrivo</th><th>Convoglio</th></tr>
                        <?php while($row = $result->fetch_assoc()): 
                            $data_italiana = date('d/m/Y', strtotime($row['data_corsa']));
                        ?>
                            <tr>
                                <td>TR<?php echo str_pad($row['id_treno'], 3, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo $data_italiana; ?></td>
                                <td><?php echo $row['orario_partenza']; ?></td>
                                <td><?php echo $row['partenza']; ?></td>
                                <td><?php echo $row['arrivo']; ?></td>
                                <td><?php echo $row['convoglio']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                <?php else: ?>
                    <p style="text-align: center; color: #666; padding: 2rem;">Nessun treno in programma per la demo.</p>
                <?php endif; ?>
            <?php else: ?>
                <table class="simple-table">
                    <tr><th>Treno</th><th>Data</th><th>Ora</th><th>Partenza</th><th>Arrivo</th><th>Convoglio</th></tr>
                    <tr><td>TR001</td><td>20/01/2024</td><td>08:00</td><td>Torre Spaventa</td><td>Villa San Felice</td><td>Storico A</td></tr>
                    <tr><td>TR002</td><td>20/01/2024</td><td>08:00</td><td>Villa San Felice</td><td>Torre Spaventa</td><td>Storico B</td></tr>
                    <tr><td>TR003</td><td>21/01/2024</td><td>10:00</td><td>Torre Spaventa</td><td>Villa San Felice</td><td>Rapido C</td></tr>
                </table>
            <?php endif; ?>
            <div style="text-align: center; margin-top: 1rem;">
                <a href="orari.php" class="btn" style="padding: 0.5rem 1rem; font-size: 0.9rem;">Vedi tutti gli orari</a>
            </div>
        </div>

        <!-- Le Nostre Stazioni -->
        <div style="background: #34495e; color: white; border-radius: 10px; padding: 2rem; margin: 2rem 0;">
            <h2 style="text-align: center; margin-bottom: 2rem;">Le Nostre 10 Stazioni</h2>
            <div class="stations-grid">
                <?php
                $stazioni = [
                    'Torre Spaventa' => '0.000 km',
                    'Prato Terra' => '2.700 km', 
                    'Rocca Pietrosa' => '7.580 km',
                    'Villa Pietrosa' => '12.680 km',
                    'Villa Santa Maria' => '16.900 km',
                    'Pietra Santa Maria' => '23.950 km',
                    'Castro Marino' => '31.500 km',
                    'Porto Spigola' => '39.500 km',
                    'Porto San Felice' => '46.000 km',
                    'Villa San Felice' => '54.680 km'
                ];
                
                foreach ($stazioni as $stazione => $km): ?>
                    <div class="station-card">
                        <strong><?php echo $stazione; ?></strong>
                        <div class="station-km"><?php echo $km; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <footer>
        <p>SFT - Società Ferrovie Turistiche</p>
        <p>Linea Storica Torre Spaventa - Villa San Felice | Progetto Basi di Dati</p>
    </footer>

    <?php if ($db_connected) $conn->close(); ?>
</body>
</html>