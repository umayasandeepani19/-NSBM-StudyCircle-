<?php
require_once __DIR__ . '/../includes/bootstrap.php';
require_login();
$qs = get_str('q'); $sub = get_int('subject'); $cat = get_int('category'); $sort = get_str('sort', 'new');
$where = ["r.status = 'approved'"]; $p = [];
if ($qs !== '') { $where[] = '(r.title LIKE ? OR r.description LIKE ?)'; $p[] = "%$qs%"; $p[] = "%$qs%"; }
if ($sub) { $where[] = 'r.subject_id = ?'; $p[] = $sub; }
if ($cat) { $where[] = 'r.category_id = ?'; $p[] = $cat; }
$order = ['new' => 'r.created_at DESC', 'downloads' => 'r.downloads DESC, r.created_at DESC', 'rating' => 'avg_rating DESC, rating_count DESC'][$sort] ?? 'r.created_at DESC';
$per = 12; $page = page_num();
$total = (int) val('SELECT COUNT(*) FROM resources r WHERE ' . implode(' AND ', $where), $p);
$list = rows(RESOURCE_SELECT . ' WHERE ' . implode(' AND ', $where) . " ORDER BY $order LIMIT $per OFFSET " . (($page - 1) * $per), $p);
$subjects = rows('SELECT id, code, name FROM subjects ORDER BY code');
$cats = rows('SELECT id, name FROM categories ORDER BY name');
$pageTitle = 'Study materials'; $area = 'student';
include __DIR__ . '/../includes/header.php';
?>
<div class="page-head">
  <div><h1>Study materials</h1><p><?= $total ?> approved file<?= $total === 1 ? '' : 's' ?> from students across all modules.</p></div>
  <a class="btn btn-primary" href="<?= h(url('student/upload.php')) ?>">Upload notes</a>
</div>
<form class="filters" method="get">
  <div class="grow"><label for="q">Search</label><input type="search" id="q" name="q" value="<?= h($qs) ?>" placeholder="Title or description"></div>
  <div><label for="subject">Subject</label><select id="subject" name="subject" data-autosubmit><option value="0">All subjects</option>
    <?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>" <?= $sub === (int) $s['id'] ? 'selected' : '' ?>><?= h($s['code'] . ' - ' . $s['name']) ?></option><?php endforeach; ?></select></div>
  <div><label for="category">Type</label><select id="category" name="category" data-autosubmit><option value="0">All types</option>
    <?php foreach ($cats as $c): ?><option value="<?= $c['id'] ?>" <?= $cat === (int) $c['id'] ? 'selected' : '' ?>><?= h($c['name']) ?></option><?php endforeach; ?></select></div>
  <div><label for="sort">Sort by</label><select id="sort" name="sort" data-autosubmit>
    <option value="new" <?= $sort === 'new' ? 'selected' : '' ?>>Newest</option><option value="downloads" <?= $sort === 'downloads' ? 'selected' : '' ?>>Most downloaded</option><option value="rating" <?= $sort === 'rating' ? 'selected' : '' ?>>Top rated</option></select></div>
  <div style="flex:0 0 auto"><button class="btn btn-blue" type="submit">Search</button> <a class="btn btn-ghost" href="resources.php">Reset</a></div>
</form>
<?php if (!$list): ?>
  <div class="empty"><b>No materials match your search</b>Try different words or clear the filters.</div>
<?php else: ?>
  <div class="res-grid"><?php foreach ($list as $r) echo resource_card($r); ?></div>
  <?= pager($total, $per, $page) ?>
<?php endif; ?>
<?php include __DIR__ . '/../includes/footer.php'; ?>
