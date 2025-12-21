<?php
declare(strict_types=1);
session_start();

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }
function is_blank(?string $s): bool { return $s === null || trim($s) === ''; }
function valid_email(string $email): bool { return filter_var($email, FILTER_VALIDATE_EMAIL) !== false; }

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

$errors = [];
$email = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = (string)($_POST['csrf'] ?? '');
  if (!hash_equals((string)$_SESSION['csrf'], $token)) $errors[] = "Invalid request.";

  $email = trim((string)($_POST['email'] ?? ''));
  $password = (string)($_POST['password'] ?? '');

  if (is_blank($email) || !valid_email($email)) $errors[] = "Enter a valid email.";
  if (is_blank($password)) $errors[] = "Password is required.";

  if (!$errors) {
    $_SESSION['login_ok'] = true;
    $_SESSION['login_email'] = $email;
    header("Location: index.php");
    exit;
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MetroSheba - Login</title>
  <style>
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;background:#f2f2f2}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:18px}
    .card{width:min(1200px,100%);background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 14px 40px rgba(0,0,0,.12);display:grid;grid-template-columns:1.1fr .9fr}
    .left{background:#efe4cf;padding:22px;display:flex;flex-direction:column}
    .brand{font-size:64px;font-weight:900;margin:10px 0 0;color:#191919}
    .sub{font-size:48px;font-weight:900;font-style:italic;margin:-10px 0 8px;color:#191919}
    .hero{margin-top:auto}
    .hero img{width:100%;height:auto;display:block}
    .right{padding:30px 28px;display:flex;flex-direction:column;justify-content:center}
    .head{display:flex;flex-direction:column;align-items:center;gap:8px;margin-bottom:18px}
    .logo{width:56px;height:56px;border-radius:50%;display:flex;align-items:center;justify-content:center;background:#fff}
    .logo span{font-weight:900;color:#1d7a34;font-size:22px}
    h1{margin:0;color:#d11e1e;font-size:56px;font-weight:900}
    form{display:flex;flex-direction:column;gap:16px;margin-top:10px}
    .row{display:grid;grid-template-columns:140px 1fr;gap:12px;align-items:center}
    label{font-size:18px}
    input{width:100%;border:0;background:#f4eded;border-radius:22px;padding:14px 16px;font-size:16px;outline:none}
    .meta{display:flex;justify-content:space-between;align-items:center;font-size:12px;color:#333;margin-top:-6px}
    .btnWrap{display:flex;justify-content:center;margin-top:8px}
    .btn{border:0;cursor:pointer;background:#b56b6b;color:#fff;border-radius:34px;padding:16px 56px;font-size:22px;box-shadow:0 12px 22px rgba(181,107,107,.35)}
    .bottom{margin-top:14px;text-align:center;font-size:14px}
    .bottom a{color:#111;text-decoration:none}
    .topbar{display:flex;justify-content:flex-end;margin-bottom:6px}
    .pill{background:#b56b6b;color:#fff;border-radius:22px;padding:9px 14px;text-decoration:none;font-size:14px}
    .msg{margin:0 0 12px;padding:10px 12px;border-radius:10px;font-size:14px}
    .err{background:#ffe7e7;color:#8b1a1a}
    .fieldErr{color:#b42318;font-size:13px;margin:-10px 0 0 152px}
    @media (max-width:960px){
      .card{grid-template-columns:1fr}
      .brand{font-size:48px}
      .sub{font-size:36px}
      .row{grid-template-columns:1fr}
      .fieldErr{margin-left:0}
    }
  </style>
</head>
<body>
<div class="wrap">
  <div class="card">
    <div class="left">
      <div class="brand">MetroSheba</div>
      <div class="sub">BANGLADESH</div>
      <div class="hero"><img src="metro.png" alt="MetroSheba"></div>
    </div>

    <div class="right">
      <div class="topbar"><a class="pill" href="index.php">Home</a></div>

      <?php if ($errors): ?>
        <div class="msg err">
          <ul style="margin:0;padding-left:18px">
            <?php foreach($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="head">
        <div class="logo"><span>M</span></div>
        <h1>Login</h1>
      </div>

      <form id="loginForm" method="post" action="login.php" novalidate>
        <input type="hidden" name="csrf" value="<?= e((string)$_SESSION['csrf']) ?>">

        <div class="row">
          <label for="email">Email:</label>
          <input id="email" name="email" type="email" value="<?= e($email) ?>">
        </div>
        <div class="fieldErr" id="err_email"></div>

        <div class="row">
          <label for="password">Password:</label>
          <input id="password" name="password" type="password">
        </div>
        <div class="fieldErr" id="err_password"></div>

        <div class="meta">
          <label style="display:flex;gap:8px;align-items:center;">
            <input type="checkbox" name="remember" value="1"> Remember me
          </label>
          <span>Forget password?</span>
        </div>

        <div class="btnWrap">
          <button class="btn" type="submit">Log In</button>
        </div>

        <div class="bottom">
          Have not resister yet? <a href="register.php"><b>Resister Now</b></a>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  function simpleEmailOk(email){
    if(!email) return false;
    if(email.indexOf(' ') !== -1) return false;
    const at = email.indexOf('@');
    if(at <= 0) return false;
    if(email.lastIndexOf('@') !== at) return false;
    const domain = email.slice(at+1);
    if(domain.length < 3) return false;
    if(domain.indexOf('.') === -1) return false;
    if(domain.startsWith('.') || domain.endsWith('.')) return false;
    return true;
  }

  document.getElementById("loginForm").addEventListener("submit", function(e){
    document.getElementById("err_email").textContent = "";
    document.getElementById("err_password").textContent = "";
    const email = document.getElementById("email").value.trim();
    const pass = document.getElementById("password").value;
    let ok = true;
    if(!simpleEmailOk(email)){
      document.getElementById("err_email").textContent = "Enter a valid email.";
      ok = false;
    }
    if(pass.length < 1){
      document.getElementById("err_password").textContent = "Password is required.";
      ok = false;
    }
    if(!ok) e.preventDefault();
  });
</script>
</body>
</html>
