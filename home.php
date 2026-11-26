<?php
session_start();
require __DIR__ . '/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$members = $conn->query(
    'SELECT student_id, created_at, last_login FROM users ORDER BY created_at'
);

include __DIR__ . '/header.php';
?>
<div class="card success">
  <h2>Login successful</h2>
  <p>Welcome, student <strong><?= e($_SESSION['student_id']) ?></strong>.
     You are authenticated on <strong>Server Admin Group 5</strong>.</p>
</div>

<div class="card">
  <h2>Group members</h2>
  <table>
    <tr><th>Student ID</th><th>Registered</th><th>Last login</th></tr>
    <?php while ($m = $members->fetch_assoc()): ?>
      <tr>
        <td><?= e($m['student_id']) ?></td>
        <td><?= e($m['created_at']) ?></td>
        <td><?= e($m['last_login'] ?? 'never') ?></td>
      </tr>
    <?php endwhile; ?>
  </table>
</div>
<?php include __DIR__ . '/footer.php'; ?>