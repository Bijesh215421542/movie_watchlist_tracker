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
        
        <form id="loginForm">
          <div class="input-group">
            <label for="username">Username</label>
            <input 
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
            <input type="checkbox" /> Remember me
          </label>
          <a href="#">Forgot password?</a>
        </div>

        <div class="switch">
          Don't have an account? <a href="signup.html">Create one</a>
        </div>
      </div>
    </div>
<script>
  document
    .getElementById("loginForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();

      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value.trim();

      // Check for Admin credentials
      if (username === "admin1" && password === "admin@123") {
        // Set role in session storage for route protection
        sessionStorage.setItem("userRole", "admin");
        window.location.href = "admin-dashboard.html";
      } else {
        // Standard user login
        sessionStorage.setItem("userRole", "user");
        window.location.href = "dashboard.html";
      }
    });
    </script>
  </body>
</html>
