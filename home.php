<?php
session_start();
require __DIR__ . '/config.php';

if (isset($_SESSION['user_id'])) {
    header('Location: home.php');
    exit;
}

$error = '';
$notice = isset($_GET['registered']) ? 'Account created. Please log in.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ?');
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        $upd = $conn->prepare('UPDATE users SET last_login = NOW() WHERE id = ?');
        $upd->bind_param('i', $user['id']);
        $upd->execute();
        $upd->close();

        header('Location: home.php');
        exit;
    }
    $error = 'Invalid username or password.';
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h2>Log in</h2>
  <?php if ($notice): ?><p class="msg ok"><?= e($notice) ?></p><?php endif; ?>
  <?php if ($error): ?><p class="msg error"><?= e($error) ?></p><?php endif; ?>
  <form method="post" action="login.php">
    <label>Username
      <input type="text" name="username" required>
    </label>
    <label>Password
      <input type="password" name="password" required>
    </label>
    <button type="submit">Log in</button>
  </form>
  <p>No account? <a href="signup.php">Sign up</a></p>
</div>
<?php include __DIR__ . '/footer.php'; ?>