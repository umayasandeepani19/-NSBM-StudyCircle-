<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$subjects = rows('SELECT id, code, name FROM subjects ORDER BY code');
$cats = rows('SELECT id, name FROM categories ORDER BY name');
$v = ['title' => '', 'description' => '', 'subject_id' => 0, 'category_id' => 0];
if (is_post()) {
    verify_csrf();
    $v = ['title' => post('title'), 'description' => post('description'), 'subject_id' => (int) post('subject_id'), 'category_id' => (int) post('category_id')];
    try {
        if (mb_strlen($v['title']) < 3 || mb_strlen($v['title']) > 150) throw new RuntimeException('Title must be 3 to 150 characters.');
        if (!val('SELECT id FROM subjects WHERE id = ?', [$v['subject_id']])) throw new RuntimeException('Choose a subject.');
        if (!val('SELECT id FROM categories WHERE id = ?', [$v['category_id']])) throw new RuntimeException('Choose a material type.');
        $f = handle_upload($_FILES['file'] ?? []);
        q('INSERT INTO resources (user_id, subject_id, category_id, title, description, file_name, file_path, file_size) VALUES (?,?,?,?,?,?,?,?)',
          [current_user()['id'], $v['subject_id'], $v['category_id'], $v['title'], $v['description'] ?: null, $f['name'], $f['path'], $f['size']]);
        flash('success', 'Uploaded. An admin will review it before it appears in the library.');
        redirect('student/my_uploads.php');
    } catch (RuntimeException $e) {
        flash('error', $e->getMessage());
    }
}
$pageTitle = 'Upload notes'; $area = 'student';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-head"><div><h1>Upload study material</h1><p>Admins check every file before it is shared with others.</p></div></div>
<div class="card form-narrow">
  <form method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>
    <div class="field"><label for="title">Title</label><input type="text" id="title" name="title" value="<?= h($v['title']) ?>" required minlength="3" maxlength="150" placeholder="e.g. DBMS normalisation summary"></div>
    <div class="grid g2">
      <div class="field"><label for="subject_id">Subject</label><select id="subject_id" name="subject_id" required><option value="">Choose...</option>
        <?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>" <?= $v['subject_id'] === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['code'] . ' - ' . $s['name']) ?></option><?php endforeach; ?></select></div>
      <div class="field"><label for="category_id">Type</label><select id="category_id" name="category_id" required><option value="">Choose...</option>
        <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $v['category_id'] === (int) $c['id'] ? 'selected' : '' ?>><?= h($c['name']) ?></option><?php endforeach; ?></select></div>
    </div>
    <div class="field"><label for="description">Description <span class="muted">(optional)</span></label><textarea id="description" name="description" maxlength="2000" placeholder="What does this cover? Which lecture or semester?"><?= h($v['description']) ?></textarea></div>
    <div class="field"><label for="file">File</label><input type="file" id="file" name="file" required data-max="<?= MAX_UPLOAD_MB ?>" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip,.png,.jpg,.jpeg">
      <div class="hint">PDF, Word, PowerPoint, Excel, text, ZIP or image. Up to <?= MAX_UPLOAD_MB ?> MB.</div></div>
    <div class="btn-row"><button class="btn btn-primary" type="submit">Upload for review</button><a class="btn btn-ghost" href="<?= h(url('student/my_uploads.php')) ?>">Cancel</a></div>
  </form>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
