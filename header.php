<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
function e($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Server Admin Group 5</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<header class="topbar">
  <h1>Server Admin Group 5</h1>
  <nav>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="home.php">Home</a>
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a>
      <a href="signup.php">Sign up</a>
    <?php endif; ?>
  </nav>
</header>
<main class="container">