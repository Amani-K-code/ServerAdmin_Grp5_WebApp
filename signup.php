<?php
session_start();
require __DIR__ . '/config.php';

$errors = [];
$username = '';
$student_id = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username   = preg_replace('/\s+/', ' ', trim($_POST['username'] ?? ''));
    $student_id = trim($_POST['student_id'] ?? '');
    $password   = $_POST['password'] ?? '';
    $confirm    = $_POST['confirm'] ?? '';

        if (!preg_match('/^[A-Za-z0-9_. -]{3,50}$/', $username)) {
        $errors[] = 'Username must be 3-50 characters: letters, digits, spaces, dot, hyphen or underscore.';
    }
    if (!preg_match('/^[A-Za-z0-9]{4,20}$/', $student_id)) {
        $errors[] = 'Enter a valid student ID (4-20 letters or digits).';
    }
    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (!$errors) {
        $stmt = $conn->prepare('SELECT id FROM users WHERE username = ? OR student_id = ?');
        $stmt->bind_param('ss', $username, $student_id);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $errors[] = 'That username or student ID is already registered.';
        }
        $stmt->close();
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare('INSERT INTO users (username, student_id, password) VALUES (?, ?, ?)');
        $stmt->bind_param('sss', $username, $student_id, $hash);
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
    <label>Student Name
      <input type="text" name="username" value="<?= e($username) ?>" required maxlength="50">
    </label>
    <label>Student ID
      <input type="text" name="student_id" value="<?= e($student_id) ?>" required maxlength="20">
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