<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) { redirect(home_for(current_user())); }
$email = '';
if (is_post()) {
    verify_csrf();
    $email = strtolower(post('email'));
    $pass  = (string) ($_POST['password'] ?? '');
    $u = row('SELECT id, password, role, status FROM users WHERE email = ?', [$email]);
    if (!$u || !password_verify($pass, $u['password'])) {
        flash('error', 'Email or password is incorrect.');
    } elseif ($u['status'] !== 'active') {
        flash('error', 'This account is blocked. Contact a StudyCircle admin.');
    } else {
        login_user((int) $u['id']);
        redirect($u['role'] === 'admin' ? 'admin/dashboard.php' : 'student/dashboard.php');
    }
}
$pageTitle = 'Log in'; $area = 'public';
include __DIR__ . '/includes/header.php';
?>
<div class="auth">
  <div class="auth-side"><h2>Welcome back to your study circle</h2><p>Pick up where you left off: your notes, questions and groups are waiting.</p></div>
  <div class="auth-main">
    <div class="auth-box">
      <h1>Log in</h1>
      <form method="post" novalidate>
        <?= csrf_field() ?>
        <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= h($email) ?>" required autofocus></div>
        <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required></div>
        <button class="btn btn-primary btn-block" type="submit">Log in</button>
      </form>
      <p style="margin-top:16px">New here? <a href="<?= h(url('register.php')) ?>">Create an account</a></p>
      <div class="demo-box"><b>Demo accounts</b><br>Student: student@nsbm.ac.lk / Student@123<br>Admin: admin@nsbm.ac.lk / Admin@123</div>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
