<!DOCTYPE html>
<html>
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Reset Password</title>
  <style>
    body {
      font-family: Arial, Helvetica, sans-serif;
      background-color: #ffffff;
      margin: 0;
      padding: 0;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .login-box {
      text-align: center;
      width: 300px;
    }

    .login-box img {
      width: 120px;
      margin-bottom: 10px;
    }

    .login-box p {
      color: #666;
      margin-bottom: 20px;
    }

    form {
      display: flex;
      flex-direction: column;
    }

    input[type=password] {
      width: 100%;
      padding: 10px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      box-sizing: border-box;
      border-radius: 5px;
    }

    button {
      padding: 10px;
      background-color: #ffffff;
      color: #000000;
      border: 1px solid black;
      border-radius: 2px;
      cursor: pointer;
    }

    button:hover {
      background-color: #f2f2f2;
    }
  </style>
</head>
<body>

<div class="login-box">
  <img src="https://i.pinimg.com/564x/31/50/bf/3150bf915dad0ce70b152fae9f13cd0f.jpg" alt="University Logo">
  <p>Set a new password for your account</p>

  <form action="reset_password.php" method="POST">
    <input type="hidden" name="email" value="<?php echo htmlspecialchars($_GET['email']); ?>">
    <input type="password" placeholder="New Password" name="Npsw" required>
    <input type="password" placeholder="Confirm Password" name="Cpsw" required>
    <button type="submit">Reset Password</button>
  </form>
</div>

</body>
</html>
