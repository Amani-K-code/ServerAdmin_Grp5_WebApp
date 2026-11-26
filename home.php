<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$members = $conn->query(
    'SELECT username, student_id, created_at, last_login FROM users ORDER BY created_at'
);

include __DIR__ . '/header.php';
?>
<div class="card success">
  <h2>Login successful</h2>
  <p>Welcome, <strong><?= e($_SESSION['username']) ?></strong>
     (ID <?= e($_SESSION['student_id']) ?>).
     You are authenticated on <strong>Server Admin Group 5</strong>.</p>
</div>

<div class="card">
  <h2>Group members</h2>
  <table>
    <tr><th>Username</th><th>Student ID</th><th>Registered</th><th>Last login</th></tr>
    <?php while ($m = $members->fetch_assoc()): ?>
      <tr>
        <td><?= e($m['username']) ?></td>
        <td><?= e($m['student_id']) ?></td>
        <td><?= e($m['created_at']) ?></td>
        <td><?= e($m['last_login'] ?? 'never') ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include __DIR__ . '/footer.php'; ?>