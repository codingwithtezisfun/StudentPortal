<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admins Login</title>
    <link
      rel="stylesheet"
      href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />
    <style>
      .thisBody {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 100vh;
        margin: 0;
        font-family: Arial, sans-serif;
        background-color: #36454f;
      }
      .login-form {
        width: 400px;
        padding: 20px;
        background-color: #f8f9fa;
        border: 1px solid #ced4da;
        border-radius: 5px;
        box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
      }
      .form-group {
        margin-bottom: 20px;
      }
      .form-group label {
        font-weight: bold;
      }
      .form-group input {
        width: 100%;
        padding: 8px;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 3px;
      }
      .form-group input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
      }
      .button {
        width: 100%;
        padding: 10px;
        font-size: 1rem;
      }
    </style>
  </head>
  <body class="thisBody">
    <div class="login-form">
      <h2 class="text-center mb-4">Admin Login</h2>
      <?php if (isset($_GET['error']) && $_GET['error'] == 'true') : ?>
      <div class="alert alert-danger" role="alert">
        Invalid username or password
      </div>
      <?php endif; ?>
      <form action="AdminLogin.php" method="POST">
        <div class="form-group">
          <label for="username">Username</label>
          <input type="text" id="username" name="username" required />
        </div>
        <div class="form-group">
          <label for="password">Password</label>
          <input type="password" id="password" name="password" required />
        </div>
        <button type="submit" class="btn btn-primary button">Login</button>
      </form>
    </div>
  </body>
</html>
