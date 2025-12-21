<?php
declare(strict_types=1);
session_start();

function e(string $s): string { return htmlspecialchars($s, ENT_QUOTES, "UTF-8"); }
function is_blank(?string $s): bool { return $s === null || trim($s) === ''; }
function valid_email(string $email): bool { return filter_var($email, FILTER_VALIDATE_EMAIL) !== false; }

function digits_only(string $s): bool {
  if ($s === '') return false;
  return ctype_digit($s);
}

function normalize_mobile(string $m): string {
  $m = trim($m);
  $out = '';
  $len = strlen($m);
  for ($i=0; $i<$len; $i++) {
    $ch = $m[$i];
    if ($ch === ' ' || $ch === '-' || $ch === '+') continue;
    $out .= $ch;
  }
  return $out;
}

if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));

$errors = [];
$success = "";

$name = "";
$email = "";
$nid = "";
$mobile = "";
$gender = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $token = (string)($_POST['csrf'] ?? '');
  if (!hash_equals((string)$_SESSION['csrf'], $token)) $errors[] = "Invalid request.";

  $action = (string)($_POST['action'] ?? 'submit');

  $name   = trim((string)($_POST['name'] ?? ''));
  $email  = trim((string)($_POST['email'] ?? ''));
  $nid    = trim((string)($_POST['nid'] ?? ''));
  $mobile = normalize_mobile((string)($_POST['mobile'] ?? ''));
  $gender = (string)($_POST['gender'] ?? '');
  $password = (string)($_POST['password'] ?? '');
  $confirm  = (string)($_POST['confirm'] ?? '');
  $code_input = trim((string)($_POST['verification_code'] ?? ''));

  if (is_blank($name) || strlen($name) < 2) $errors[] = "Name must be at least 2 characters.";
  if (is_blank($email) || !valid_email($email)) $errors[] = "Please enter a valid email address.";

  if (is_blank($nid) || !digits_only($nid) || strlen($nid) < 8 || strlen($nid) > 20) $errors[] = "NID must be digits only and 8-20 characters.";
  if (is_blank($mobile) || !digits_only($mobile) || strlen($mobile) < 10 || strlen($mobile) > 15) $errors[] = "Mobile number must be digits only and 10-15 characters.";
  if ($gender !== "male" && $gender !== "female") $errors[] = "Please select gender.";

  if ($action === 'get_code') {
    if (!$errors) {
      $code = (string)random_int(100000, 999999);
      $_SESSION['reg_code'] = $code;
      $_SESSION['reg_code_mobile'] = $mobile;
      unset($_SESSION['reg_verified'], $_SESSION['reg_verified_mobile']);
      $success = "Verification code generated (demo): " . e($code);
    }
  } elseif ($action === 'verify') {
    if (!$errors) {
      $saved = (string)($_SESSION['reg_code'] ?? '');
      $saved_mobile = (string)($_SESSION['reg_code_mobile'] ?? '');
      if ($saved === '' || $saved_mobile !== $mobile) {
        $errors[] = "Please click 'Get Code' first.";
      } elseif ($code_input === '' || $code_input !== $saved) {
        $errors[] = "Verification code does not match.";
      } else {
        $_SESSION['reg_verified'] = true;
        $_SESSION['reg_verified_mobile'] = $mobile;
        $success = "Verification successful.";
      }
    }
  } else {
    if (strlen($password) < 6) $errors[] = "Password must be at least 6 characters.";
    if ($password !== $confirm) $errors[] = "Passwords do not match.";

    $is_verified = (bool)($_SESSION['reg_verified'] ?? false);
    $v_mobile = (string)($_SESSION['reg_verified_mobile'] ?? '');
    if (!$is_verified || $v_mobile !== $mobile) $errors[] = "Verify your mobile (Get Code -> Verify) before confirming.";

    if (!$errors) {
      $_SESSION['registered'] = true;
      $_SESSION['reg_user'] = [
        'name' => $name,
        'email' => $email,
        'nid' => $nid,
        'mobile' => $mobile,
        'gender' => $gender
      ];
      header("Location: login.php");
      exit;
    }
  }
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>MetroSheba - Register</title>
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
    .right{padding:28px;display:flex;flex-direction:column;justify-content:center}
    .topbar{display:flex;justify-content:flex-end;margin-bottom:10px}
    .pill{background:#b56b6b;color:#fff;border-radius:22px;padding:9px 14px;text-decoration:none;font-size:14px}
    h1{margin:8px 0 18px;color:#1d7a34;font-size:48px;letter-spacing:.5px}
    form{display:flex;flex-direction:column;gap:14px}
    .row{display:grid;grid-template-columns:140px 1fr auto;gap:12px;align-items:center}
    .row.two{grid-template-columns:140px 1fr}
    label{color:#222;font-size:16px}
    input[type="text"],input[type="email"],input[type="password"]{width:100%;border:0;background:#f4eded;border-radius:22px;padding:14px 16px;font-size:16px;outline:none}
    .miniBtn{border:0;cursor:pointer;background:#b56b6b;color:#fff;border-radius:22px;padding:10px 14px;font-size:14px;min-width:90px;box-shadow:0 10px 18px rgba(181,107,107,.25)}
    .genderBox{display:flex;gap:20px;align-items:center}
    .actions{display:flex;justify-content:center;margin-top:6px}
    .confirm{border:0;cursor:pointer;background:#b56b6b;color:#fff;border-radius:34px;padding:16px 46px;font-size:22px;box-shadow:0 12px 22px rgba(181,107,107,.35)}
    .msg{margin:0 0 12px;padding:10px 12px;border-radius:10px;font-size:14px}
    .err{background:#ffe7e7;color:#8b1a1a}
    .ok{background:#e8fff0;colo
