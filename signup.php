<?php
session_start();
require __DIR__ . '/config.php';

$errors = [];
$username = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm  = $_POST['confirm'] ?? '';

    if ($username === '' || strlen($username) > 50) {
        $errors[] = 'Username is required (max 50 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 100) {
        $errors[] = 'Enter a valid email address.';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR email = ?');
        $stmt->bind_param('ss', $username, $email);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'That username or email is already registered.';
        }
        $stmt->close();
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, password, email) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $hash, $email);
        $stmt->execute();
        $stmt->close();
        header('Location: login.php?registered=1');
        exit;
    }
}

include __DIR__ . '/header.php';
?>
<div class="card">
  <h2>Create an account</h2>
  <?php foreach ($errors as $err): ?>
    <p class="msg error"><?= e($err) ?></p>
  <?php endforeach; ?>
  <form method="post" action="signup.php">
    <label>Username
      <input type="text" name="username" value="<?= e($username) ?>" required maxlength="50">
    </label>
    <label>Email
      <input type="email" name="email" value="<?= e($email) ?>" required maxlength="100">
    </label>
    <label>Password
      <input type="password" name="password" required minlength="8">
    </label>
    <label>Confirm password
      <input type="password" name="confirm" required minlength="8">
    </label>
    <button type="submit">Sign up</button>
  </form>
  <p>Already registered? <a href="login.php">Log in</a></p>
</div>
<?php include __DIR__ . '/footer.php'; ?>