<?php
/**
 * index.php - Huvudsida / Flödet
 */
$pageTitle = 'Flödet';
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireLogin();

$stmt = $pdo->prepare('
    SELECT posts.id, posts.content, posts.created_at, posts.user_id, users.username
    FROM posts
    INNER JOIN users ON posts.user_id = users.id
    ORDER BY posts.created_at DESC
');
$stmt->execute();
$posts = $stmt->fetchAll();

require_once __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="clock-container">
            <canvas id="clockCanvas"></canvas>
        </div>

        <div class="card kvitter-form-card">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-pencil-square me-2"></i>Skriv ett kvitter
                </h5>
                <form method="POST" action="<?php echo htmlspecialchars(appUrl('create_post.php')); ?>">
                    <div class="mb-2">
                        <textarea
                            class="form-control"
                            name="content"
                            id="kvitterContent"
                            rows="3"
                            maxlength="280"
                            required
                            placeholder="Vad tänker du på? (max 280 tecken)"
                        ></textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="char-counter" id="charCounter">280 tecken kvar</span>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="bi bi-send-fill me-1"></i>Kvittra
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <h4 class="mb-3"><i class="bi bi-rss me-2"></i>Flödet</h4>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php echo htmlspecialchars($_GET['error']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($_GET['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($posts)): ?>
            <div class="card kvitter-card">
                <div class="card-body text-center text-muted py-5">
                    <i class="bi bi-chat-dots" style="font-size: 2.5rem;"></i>
                    <p class="mt-2 mb-0">Inga kvitter än. Bli den första att kvittra!</p>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($posts as $post): ?>
                <div class="card kvitter-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <span class="kvitter-username">
                                    <i class="bi bi-person-circle me-1"></i><?php echo htmlspecialchars($post['username']); ?>
                                </span>
                                <span class="kvitter-time ms-2">
                                    <i class="bi bi-clock me-1"></i><?php echo date('Y-m-d H:i', strtotime($post['created_at'])); ?>
                                </span>
                            </div>
                            <?php if ($post['user_id'] == getUserId() || isAdmin()): ?>
                                <form
                                    method="POST"
                                    action="<?php echo htmlspecialchars(appUrl('delete_post.php')); ?>"
                                    class="d-inline"
                                    onsubmit="return confirm('Är du säker på att du vill ta bort detta kvitter?');"
                                >
                                    <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                                    <button type="submit" class="btn btn-outline-danger btn-delete" title="Ta bort kvitter">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                        <p class="kvitter-content mt-2 mb-0">
                            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                        </p>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const textarea = document.getElementById('kvitterContent');
    const counter = document.getElementById('charCounter');

    if (textarea && counter) {
        textarea.addEventListener('input', function() {
            const remaining = 280 - this.value.length;
            counter.textContent = remaining + ' tecken kvar';

            if (remaining <= 20) {
                counter.classList.add('warning');
            } else {
                counter.classList.remove('warning');
            }
        });
    }
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
