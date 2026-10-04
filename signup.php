<?php
session_start();
require_once 'db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nickname = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if (!empty($nickname) && !empty($email) && !empty($password)) {
        if (strlen($password) < 8) {
            $error = "Password must be at least 8 characters long.";
        } else {
            // Check if email or nickname exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $nickname]);
            
            if ($stmt->fetch()) {
                $error = "User with this email or username already exists.";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                if ($stmt->execute([$nickname, $email, $hashedPassword])) {
                    header("Location: login.php?registered=success");
                    exit;
                } else {
                    $error = "Failed to register account. Please try again.";
                }
            }
        }
    } else {
        $error = "All fields are required.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Create Account | FilmTrack</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="signup1.css" />
</head>
<body>
  <div class="form-container">
    <div class="form-panel">
      <div class="brand-header">
        <h1>Create an account</h1>
        <p class="sub">Start keeping track of your favorite Movies and TV Series</p>
      </div>

      <?php if (!empty($error)): ?>
        <p style="color: #ff4d4d; font-size: 0.9rem; margin-bottom: 1rem;"><?php echo htmlspecialchars($error); ?></p>
      <?php endif; ?>

      <form id="signupForm" action="signup.php" method="POST">
        <div class="field">
          <label for="name">Nickname</label>
          <input type="text" id="name" name="name" placeholder="Choose a nickname" required value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>" />
        </div>

        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" placeholder="Enter a valid email address" required value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" />
        </div>

        <div class="field">
          <label for="password">Password</label>
          <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="At least 8 characters" required minlength="8" />
            <button type="button" class="toggle-password" id="togglePassword" aria-label="Toggle password visibility">Show</button>
          </div>
        </div>

        <button type="submit" class="submit">Sign up</button>
      </form>

      <div class="divider"><span>or</span></div>

      <p class="already">
        Already have an account? <a href="login.php">Log in</a>
      </p>
    </div>
  </div>

  <script>
    const toggleBtn = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');

    toggleBtn.addEventListener('click', () => {
      const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
      passwordInput.setAttribute('type', type);
      toggleBtn.textContent = type === 'password' ? 'Show' : 'Hide';
    });
  </script>
</body>
</html>
