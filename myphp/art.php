<?php
include 'db.php'; 
session_start();

if (isset($_GET['id'])) {
    $articleId = $_GET['id'];

    $stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id");
    $stmt->execute(['id' => $articleId]);
    $article = $stmt->fetch();

    if (!$article) {
        header("Location: main.php");
        exit;
    }

    $stmt = $pdo->prepare("
        SELECT comments.*, users.username 
        FROM comments 
        LEFT JOIN users ON comments.user_id = users.id 
        WHERE article_id = :article_id 
        ORDER BY created_at DESC
    ");
    $stmt->execute(['article_id' => $articleId]);
    $comments = $stmt->fetchAll();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $comment = trim($_POST['comment']);
    $userId = $_SESSION['user_id'];

    if (!empty($comment)) {
        $stmt = $pdo->prepare("INSERT INTO comments (article_id, user_id, comment, created_at) VALUES (:article_id, :user_id, :comment, NOW())");
        $stmt->execute([
            'article_id' => $articleId,
            'user_id' => $userId,
            'comment' => $comment
        ]);
        header("Location: art.php?id=$articleId");
        exit;
    } else {
        $error = "Пожалуйста, напишите комментарий.";
    }
}

if (isset($_GET['delete_comment_id']) && isset($_SESSION['user_id'])) {
    $commentId = $_GET['delete_comment_id'];
    $userId = $_SESSION['user_id'];

    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = :id");
    $stmt->execute(['id' => $commentId]);
    $comment = $stmt->fetch();

    if ($comment && $comment['user_id'] == $userId) {
        $stmt = $pdo->prepare("DELETE FROM comments WHERE id = :id");
        $stmt->execute(['id' => $commentId]);
        header("Location: art.php?id=$articleId");
        exit;
    } else {
        $error = "Вы не можете удалить этот комментарий.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($article['title']) ?> - Спортивный портал</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   
    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Статья</div>
            <ul class="nav-menu">
                <li><a href="main.php">Главная</a></li>
                <li><a href="personal_articles.php">Мои статьи</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="logout.php">Выход</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="user-info-nav">
                        <?= htmlspecialchars($_SESSION['username']); ?>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </nav>

    <div class="container">
      
        <header class="page-header">
            <h1><?= htmlspecialchars($article['title']) ?></h1>
        </header>

      
        <div class="content-box">
            <?php if (!empty($article['image'])): ?>
                <div style="text-align: center; margin-bottom: 20px;">
                    <img src="<?= htmlspecialchars($article['image']) ?>" 
                         alt="Изображение статьи" 
                         style="max-width: 100%; max-height: 400px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                </div>
            <?php endif; ?>
            
            <div style="color: #333; line-height: 1.7; font-size: 16px; margin-bottom: 20px;">
                <?= nl2br(htmlspecialchars($article['content'])) ?>
            </div>
            
            <div style="color: #7f8c8d; font-size: 14px; padding-top: 15px; border-top: 1px solid #eee;">
                Опубликовано: <?= date('d.m.Y H:i', strtotime($article['created_at'])) ?>
            </div>
        </div>

      
        <?php if (isset($error)): ?>
            <div class="error-message">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

    
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="content-box" style="margin-top: 20px;">
                <h3 style="color: #2c3e50; margin-top: 0; margin-bottom: 15px;">Добавить комментарий</h3>
                <form method="POST" action="">
                    <textarea name="comment" 
                              style="width: 100%; padding: 12px; border: 2px solid #bbdefb; border-radius: 6px; margin-bottom: 15px; resize: vertical; min-height: 100px; font-family: Arial, sans-serif;"
                              placeholder="Напишите ваш комментарий..." 
                              required></textarea>
                    <button type="submit" class="btn btn-success">Отправить комментарий</button>
                </form>
            </div>
        <?php else: ?>
            <div style="background: #fff3cd; color: #856404; padding: 15px; border-radius: 4px; margin: 20px 0; border: 1px solid #ffeaa7; text-align: center;">
                Для добавления комментария нужно <a href="login.php" style="color: #2a5298; font-weight: bold;">войти</a> или <a href="register.php" style="color: #2a5298; font-weight: bold;">зарегистрироваться</a>.
            </div>
        <?php endif; ?>

     
        <div class="content-box" style="margin-top: 20px;">
            <h3 style="color: #2c3e50; margin-top: 0; margin-bottom: 20px;">Комментарии (<?= count($comments) ?>)</h3>
            
            <?php if ($comments): ?>
                <div style="list-style: none; padding: 0; margin: 0;">
                    <?php foreach ($comments as $comment): ?>
                        <div style="background: #f8fafc; padding: 15px; margin-bottom: 15px; border-radius: 6px; border-left: 4px solid #3498db;">
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                <span style="font-weight: bold; color: #2c3e50;"><?= htmlspecialchars($comment['username']) ?></span>
                                <span style="color: #95a5a6; font-size: 13px;">
                                    <?= date('d.m.Y H:i', strtotime($comment['created_at'])) ?>
                                </span>
                            </div>
                            
                            <div style="color: #444; line-height: 1.5; margin-bottom: 10px;">
                                <?= nl2br(htmlspecialchars($comment['comment'])) ?>
                            </div>
                            
                            <?php if (isset($_SESSION['user_id']) && $comment['user_id'] == $_SESSION['user_id']): ?>
                                <div style="text-align: right;">
                                    <a href="art.php?id=<?= $articleId ?>&delete_comment_id=<?= $comment['id'] ?>" 
                                       style="color: #e74c3c; text-decoration: none; font-size: 14px;"
                                       onclick="return confirm('Вы уверены, что хотите удалить комментарий?')">
                                        Удалить
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p style="color: #7f8c8d; font-style: italic; text-align: center; padding: 20px;">
                    Комментариев пока нет. Будьте первым!
                </p>
            <?php endif; ?>
        </div>

       
        <div class="action-buttons" style="margin-top: 30px;">
            <a href="main.php" class="btn btn-secondary">Назад к статьям</a>
            <a href="create_article.php" class="btn btn-success">Создать новую статью</a>
        </div>
    </div>


    <footer class="footer">
        <div class="container">
            <p>Спортивный портал &copy; <?= date('Y'); ?></p>
        </div>
    </footer>

    <script>
       
        document.querySelectorAll('a[style*="color: #e74c3c"]').forEach(link => {
            link.addEventListener('click', function(e) {
                if (!confirm('Вы уверены, что хотите удалить этот комментарий?')) {
                    e.preventDefault();
                }
            });
        });
    
        const textarea = document.querySelector('textarea');
        if (textarea) {
            textarea.addEventListener('input', function() {
                this.style.height = 'auto';
                this.style.height = (this.scrollHeight) + 'px';
            });
        }
    </script>
</body>
</html>