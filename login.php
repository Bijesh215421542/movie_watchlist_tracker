<?php
session_start();
require_once 'db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($username) && !empty($password)) {
        // Hardcoded admin check or query database
        if ($username === 'admin1' && $password === 'admin@123') {
            $_SESSION['user_id'] = 0;
            $_SESSION['username'] = 'Admin';
            $_SESSION['role'] = 'admin';
            header("Location: admin-dashboard.php");
            exit;
        }

        // Fetch user from DB
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'] ?? 'user';
            
            if ($_SESSION['role'] === 'admin') {
                header("Location: admin-dashboard.php");
            } else {
                header("Location: dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid username or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CINETRACK - Login</title>
  <link rel="stylesheet" href="login.css" />
</head>
<body>
  <div class="container">
    <div class="form-box">
      <h1>🎬 CINETRACK</h1>
      
      <?php if (!empty($error)): ?>
        <p style="color: #ff4d4d; font-size: 0.9rem; text-align: center;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <form id="loginForm" action="login.php" method="POST">
        <div class="input-group">
          <label for="username">Username</label>
          <input 
            type="text"
            id="username" 
            name="username" 
            placeholder="Enter your username" 
            required 
          />
        </div>

        <div class="input-group">
          <label for="password">Password</label>
          <input 
            type="password" 
            id="password" 
            name="password" 
            placeholder="At least 8 characters" 
            required 
            minlength="8" 
          />
        </div>

        <button type="submit" class="submit-btn">Login</button>
      </form>

      <div class="row">
        <label class="checkbox">
          <input type="checkbox" name="remember" /> Remember me
        </label>
        <a href="#">Forgot password?</a>
      </div>

      <div class="switch">
        Don't have an account? <a href="signup.php">Create one</a>
      </div>
    </div>
  </div>
</body>
</html>
