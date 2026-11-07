<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) { redirect(home_for(current_user())); }
$v = ['full_name' => '', 'email' => '', 'student_id' => '', 'faculty' => ''];
$errors = [];
if (is_post()) {
    verify_csrf();
    foreach ($v as $k => $_) { $v[$k] = post($k); }
    $v['email'] = strtolower($v['email']);
    $pass = (string) ($_POST['password'] ?? '');
    $pass2 = (string) ($_POST['password2'] ?? '');
    if (mb_strlen($v['full_name']) < 3) $errors[] = 'Enter your full name (at least 3 characters).';
    if (!filter_var($v['email'], FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
    if ($v['student_id'] !== '' && !preg_match('/^[A-Za-z0-9.\-\/ ]{3,30}$/', $v['student_id'])) $errors[] = 'Student ID may only contain letters, numbers and . - /';
    if (strlen($pass) < 8 || !preg_match('/[A-Za-z]/', $pass) || !preg_match('/\d/', $pass)) $errors[] = 'Password needs at least 8 characters with a letter and a number.';
    if ($pass !== $pass2) $errors[] = 'The two passwords do not match.';
    if (!$errors && val('SELECT id FROM users WHERE email = ?', [$v['email']])) $errors[] = 'An account with this email already exists. Try logging in.';
    if (!$errors) {
        q('INSERT INTO users (full_name, email, student_id, faculty, password, role) VALUES (?,?,?,?,?,?)',
          [$v['full_name'], $v['email'], $v['student_id'] ?: null, $v['faculty'] ?: null, password_hash($pass, PASSWORD_DEFAULT), 'student']);
        login_user((int) db()->lastInsertId());
        flash('success', 'Welcome to StudyCircle, ' . explode(' ', $v['full_name'])[0] . '!');
        redirect('student/dashboard.php');
    }
}
$pageTitle = 'Create account'; $area = 'public';
include __DIR__ . '/includes/header.php';
?>
<div class="auth">
  <div class="auth-side"><h2>Join the NSBM study community</h2><p>Share what you know, ask what you do not, and find people studying the same modules.</p></div>
  <div class="auth-main">
    <div class="auth-box">
      <h1>Create your account</h1>
      <?php foreach ($errors as $e): ?><div class="alert alert-error"><?= h($e) ?></div><?php endforeach; ?>
      <form method="post" data-match>
        <?= csrf_field() ?>
        <div class="field"><label for="full_name">Full name</label><input type="text" id="full_name" name="full_name" value="<?= h($v['full_name']) ?>" required minlength="3" maxlength="100"></div>
        <div class="field"><label for="email">Email</label><input type="email" id="email" name="email" value="<?= h($v['email']) ?>" required maxlength="150"></div>
        <div class="grid g2">
          <div class="field"><label for="student_id">Student ID <span class="muted">(optional)</span></label><input type="text" id="student_id" name="student_id" value="<?= h($v['student_id']) ?>" maxlength="30"></div>
          <div class="field"><label for="faculty">Faculty <span class="muted">(optional)</span></label><input type="text" id="faculty" name="faculty" value="<?= h($v['faculty']) ?>" maxlength="100" placeholder="e.g. Computing"></div>
        </div>
        <div class="field"><label for="password">Password</label><input type="password" id="password" name="password" required minlength="8"><div class="hint">At least 8 characters, with a letter and a number.</div></div>
        <div class="field"><label for="password2">Confirm password</label><input type="password" id="password2" name="password2" required minlength="8"></div>
        <button class="btn btn-primary btn-block" type="submit">Create account</button>
      </form>
      <p style="margin-top:16px">Already registered? <a href="<?= h(url('login.php')) ?>">Log in</a></p>
    </div>
  </div>
</div>
<?php include __DIR__ . '/includes/footer.php'; ?>
