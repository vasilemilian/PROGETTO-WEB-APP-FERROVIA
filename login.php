<?php
session_start();
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

$error = '';
// ✅ CORREZIONE: Usa una sola variabile per il redirect
$redirect_type = $_GET['redirect'] ?? 'cliente';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    // ✅ CORREZIONE: Prendi il redirect dal form, altrimenti mantieni quello del GET
    $redirect_type = $_POST['redirect'] ?? $redirect_type;
    
    if ($db_connected) {
        $sql = "SELECT id_utente, nome, cognome, email, password, ruolo 
                FROM proj1_Utente WHERE email = ? AND attivo = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            // ✅ CORRETTO: Confronto diretto con password in chiaro
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id_utente'];
                $_SESSION['user_name'] = $user['nome'] . ' ' . $user['cognome'];
                $_SESSION['user_role'] = $user['ruolo'];
                $_SESSION['user_email'] = $user['email'];
                
                // ✅ CORRETTO: Redirect con nomi file in minuscolo
                switch($user['ruolo']) {
                    case 'BACKOFFICE_ADMIN':
                        header("Location: backoffice_admin.php");
                        break;
                    case 'BACKOFFICE_ESERCIZIO':
                        header("Location: backoffice_esercizio.php");
                        break;
                    default:
                        header("Location: area_cliente.php");
                }
                exit;
            } else {
                $error = "Password errata";
            }
        } else {
            $error = "Utente non trovato";
        }
        $stmt->close();
    } else {
        $error = "Sistema non disponibile";
    }
}
?>
<!DOCTYPE html>
<html lang="it">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SFT</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; line-height: 1.6; color: #333; background-color: #f8f9fa; }
        .header { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); color: white; padding: 1rem 0; }
        .nav { max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 0 2rem; }
        .logo { font-size: 1.8rem; font-weight: bold; color: #e74c3c; }
        .container { max-width: 1200px; margin: 0 auto; padding: 2rem; }
        .login-box { max-width: 400px; margin: 2rem auto; background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: bold; color: #2c3e50; }
        .form-group input { width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px; font-size: 1rem; }
        .btn { background: #e74c3c; color: white; padding: 0.75rem 1.5rem; border: none; border-radius: 5px; cursor: pointer; font-size: 1rem; width: 100%; }
        .btn:hover { background: #c0392b; }
        .error { background: #f8d7da; color: #721c24; padding: 0.75rem; border-radius: 5px; margin-bottom: 1rem; text-align: center; }
        .access-type { text-align: center; margin-bottom: 1rem; padding: 1rem; background: #e8f4fd; border-radius: 5px; }
    </style>
</head>
<body>
    <header class="header">
        <nav class="nav">
            <div class="logo">SFT<span>.it</span></div>
            <div class="nav-links">
                <a href="index.php" style="color: white;">Torna alla Home</a>
            </div>
        </nav>
    </header>

    <div class="container">
        <div class="login-box">
            <div class="access-type">
                <h3>
                    <?php 
                    switch($redirect_type) {
                        case 'admin': echo 'Accesso Backoffice Amministrativo'; break;
                        case 'esercizio': echo 'Accesso Backoffice Esercizio'; break;
                        default: echo 'Accesso Area Clienti';
                    }
                    ?>
                </h3>
            </div>
            
            <?php if ($error): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <input type="hidden" name="redirect" value="<?php echo $redirect_type; ?>">
                
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo $_POST['email'] ?? ''; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" required>
                </div>
                
                <button type="submit" class="btn">Accedi</button>
            </form>
            
            <div style="text-align: center; margin-top: 1.5rem;">
                <p><a href="index.php" style="color: #e74c3c;">← Torna alla Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
<?php if ($db_connected) $conn->close(); ?>