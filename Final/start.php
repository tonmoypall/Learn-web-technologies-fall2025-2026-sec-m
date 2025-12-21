<?php
declare(strict_types=1);
session_start();
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MetroSheba Bangladesh</title>
  <style>
    *{box-sizing:border-box}
    body{margin:0;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial;background:#f2f2f2}
    .wrap{min-height:100vh;display:flex;align-items:center;justify-content:center;padding:18px}
    .card{width:min(1100px,100%);background:#fff;border-radius:14px;overflow:hidden;box-shadow:0 14px 40px rgba(0,0,0,.12);display:grid;grid-template-columns:1.15fr .85fr}
    .left{background:#efe4cf;position:relative;padding:22px;display:flex;flex-direction:column}
    .brand{font-size:64px;font-weight:900;letter-spacing:.4px;margin:10px 0 0;color:#191919}
    .sub{font-size:48px;font-weight:900;font-style:italic;margin:-10px 0 8px;color:#191919}
    .hero{margin-top:auto}
    .hero img{width:100%;height:auto;display:block}
    .right{padding:34px 28px;display:flex;flex-direction:column;justify-content:center;gap:18px}
    .title{font-size:34px;font-weight:800;color:#111;margin:0}
    .title b{display:block;font-size:46px}
    .online{font-size:56px;line-height:1;color:#1d7a34;font-weight:900;margin:6px 0 0}
    .ticket{color:#d11e1e;font-style:italic;font-size:28px;margin-top:2px}
    .btnrow{display:flex;gap:18px;margin-top:18px;justify-content:center}
    .btn{border:0;cursor:pointer;text-decoration:none;background:#b56b6b;color:#fff;font-size:22px;padding:16px 34px;border-radius:32px;box-shadow:0 10px 18px rgba(181,107,107,.35);display:inline-flex;align-items:center;justify-content:center;min-width:160px}
    .btn:hover{filter:brightness(.97)}
    @media (max-width:900px){
      .card{grid-template-columns:1fr}
      .left{min-height:340px}
      .brand{font-size:48px}
      .sub{font-size:36px}
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
        <p class="title">Welcome To <b>MetroSheba</b></p>
        <div>
          <div class="online">Online</div>
          <div class="ticket">Ticketing<br>Platform</div>
        </div>
        <div class="btnrow">
          <a class="btn" href="login.php">Log in</a>
          <a class="btn" href="register.php">Register</a>
        </div>
      </div>
    </div>
  </div>
</body>
</html>
