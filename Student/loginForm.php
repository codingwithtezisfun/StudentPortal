<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login Form</title>
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css"
    />
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />
    <style>
      body {
        font-family: Arial, sans-serif;
        background-color: #778899;
        margin: 0;
        padding: 0;
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        flex-direction: column;
      }
      header {
        position: fixed;
        top: 0;
        width: 100%;
        height: 100px;
        background-color: #36454f;
        color: #fff000;
        display: flex;
        align-items: center;
        padding: 0 10px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        z-index: 1000;
      }
      header img {
        height: 20px;
        margin-right: 10px;
      }
      header .label {
        font-size: 14px;
      }
      .login-container {
        min-width: 300px;
        min-height: 300px;
        max-width: 600px;
        max-height: 600px;
        background: rgba(12, 12, 12, 0.6);
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
        margin-top: 50px; /* Adjusted for sticky header */
        background-image: url("../images/main\ 3.jpg"); /* Replace with your background image path */
        background-size: cover; /* Make the background image cover the entire container */
        background-position: center; /* Center the background image */
        background-repeat: no-repeat;
        box-shadow: 30px 30px 40px #4169e1;
      }
      .login-container h1 {
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
        color: wheat;
      }
      .form-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 15px;
      }
      .input-group {
        width: 100%;
      }
      .input-group .input-group-text {
        width: 40px;
        height: 40px;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: transparent;
        color: #fff000;
        border: none;
        font-size: 35px;
        margin-right: 5px;
      }
      .input-group .input-group-text i {
        font-size: 40px;
      }
      .form-control {
        width: 100%;
        padding: 8px;
        font-size: 16px;
        border: 1px solid #ccc;
        border-radius: 3px;
      }
      .login-button {
        width: 100%;
        padding: 10px;
        font-size: 16px;
        background-color: #007bff;
        color: #fff000;
        border: none;
        cursor: pointer;
        border-radius: 3px;
        margin-top: 15px;
      }
      .login-button:hover {
        background-color: #0056b3;
      }
      .forgot-password {
        text-align: center;
        margin-top: 10px;
        font-size: 14px;
        color: #007bff;
      }
      .logo {
        width: 70px;
        height: 70px;
        margin: 20px;
      }
    </style>
  </head>
  <body>
    <header>
      <img class="logo" src="../images/logo.gif" alt="Logo" />
      <!-- Replace 'logo.png' with your actual logo path -->
      <div class="label"><h2>Student Portal</h2></div>
    </header>

    <div class="login-container">
      <h1>Login</h1>
      <?php if (isset($_GET['error']) && $_GET['error'] == 'true') : ?>
      <div class="alert alert-danger" role="alert">
        Invalid username or password
      </div>
      <?php endif; ?>
      <form action="login.php" method="POST">
        <!-- Replace 'your-server-url' with your actual form submission endpoint -->
        <div class="form-row">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-user"></i></span>
            </div>
            <input
              type="text"
              id="username"
              name="username"
              class="form-control"
              placeholder="Enter username"
              required
            />
          </div>
        </div>
        <div class="form-row">
          <div class="input-group">
            <div class="input-group-prepend">
              <span class="input-group-text"><i class="fas fa-lock"></i></span>
            </div>
            <input
              type="password"
              id="password"
              name="password"
              class="form-control"
              placeholder="Enter password"
              required
            />
          </div>
        </div>
        <button type="submit" class="login-button">
          <i class="fas fa-sign-in-alt"></i> Login
        </button>
      </form>
      <div class="forgot-password">
        <a style="color: #00008b" href="ForgotPassword.html"
          >Forgot Password?</a
        >
      </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>
