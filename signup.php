<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sign Up </title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;600&family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="signup.css" />
</head>
<body>
  <div class="form-container">
    <div class="form-panel">
      <div class="brand-header">
        <h1>Create an account</h1>
        <p class="sub">Start keeping track of your favorite Movies and TV Series</p>
      </div>

      <form id="signupForm">
        <div class="field">
          <label for="name">Nickname</label>
          <input type="text" id="name" name="name" placeholder="Choose a nickname" required />
        </div>

        <div class="field">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" placeholder="Enter a valid email address" required />
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" placeholder="At least 8 unique characters" required minlength="8" />
        </div>

        <button type="submit" class="submit">Sign up</button>
      </form>
      <div class="divider">or</div>
      <p class="already">
        I have an account? <a href="login.html"><br>
          Log in</a>
      </p>
    </div>
  </div>

  <script>
    document.getElementById("signupForm").addEventListener("submit", function(e) {
      e.preventDefault();
      // Handle authentication or validation logic here
      window.location.href = "dashboard.html";
    });
  </script>
</body>
</html>
