<?php
require_once __DIR__ . '/includes/bootstrap.php';
if (current_user()) { redirect(home_for(current_user())); }
$stats = [
    'notes'    => (int) val("SELECT COUNT(*) FROM resources WHERE status='approved'"),
    'students' => (int) val("SELECT COUNT(*) FROM users WHERE role='student'"),
    'groups'   => (int) val('SELECT COUNT(*) FROM study_groups'),
];
$pageTitle = 'Learn together'; $area = 'public';
include __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <div class="wrap">
    <div>
      <h1>Study smarter with your NSBM classmates</h1>
      <p class="lead">Share lecture notes, get answers from students who just passed the module, and book a table in the library with a study group.</p>
      <div class="btn-row">
        <a class="btn btn-primary" href="<?= h(url('register.php')) ?>">Create your account</a>
        <a class="btn btn-ghost" href="<?= h(url('login.php')) ?>">I already have one</a>
      </div>
      <p class="muted small" style="margin-top:18px"><?= $stats['notes'] ?> study materials &middot; <?= $stats['students'] ?> students &middot; <?= $stats['groups'] ?> study groups</p>
    </div>
    <div class="hero-art" aria-hidden="true">
      <span class="c c1"></span><span class="c c2"></span>
      <span class="chip a">WMAD lecture notes</span>
      <span class="chip b">DBMS past paper</span>
      <span class="chip c3">Study group, Thu 3.30 pm</span>
    </div>
  </div>
</section>

<section class="section" id="features">
  <div class="wrap">
    <div class="grid g3">
      <div class="card feat"><h3>Notes and materials</h3><p class="muted">Upload your notes, download others by subject, and rate what helped. Every file is checked by an admin first.</p></div>
      <div class="card feat"><h3>Q&amp;A forum</h3><p class="muted">Ask an academic question, get answers from other students, and mark the one that solved it.</p></div>
      <div class="card feat"><h3>Study groups</h3><p class="muted">Start a group for any module, invite classmates and schedule study sessions with a time and place.</p></div>
    </div>
    <div class="cta" style="margin-top:40px">
      <h2>Ready for the next exam?</h2>
      <p>Joining takes less than a minute.</p>
      <a class="btn btn-blue" href="<?= h(url('register.php')) ?>">Join StudyCircle</a>
    </div>
  </div>
</section>
<?php include __DIR__ . '/includes/footer.php'; ?>
