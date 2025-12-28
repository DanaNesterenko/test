<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$stmt = $pdo->prepare("SELECT id, title, content, created_at, image FROM articles WHERE author_id = :author_id ORDER BY created_at DESC");
$stmt->execute(['author_id' => $_SESSION['user_id']]);
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Мои статьи</title>
</head>
<body>
    <div style="background: #2c3e50; min-height: 100vh;">
    
        <nav class="nav-main">
            <div class="container nav-container">
                <div class="nav-logo">Мои статьи</div>
                <ul class="nav-menu">
                    <li><a href="main.php">Главная</a></li>
                    <li><a href="personal_articles.php" class="active">Мои статьи</a></li>
                    <li><a href="profile.php">Профиль</a></li>
                    <li><a href="logout.php">Выход</a></li>
                    <li class="user-info-nav">
                        <?= htmlspecialchars($_SESSION['username']); ?>
                    </li>
                </ul>
            </div>
        </nav>

        <div class="container">
            <header class="page-header">
                <h1>Мои статьи</h1>
            </header>

            <div class="action-buttons" style="margin-bottom: 20px;">
                <a href="create_article.php" class="btn btn-success">Добавить статью</a>
                <a href="main.php" class="btn btn-secondary">На главную</a>
            </div>

            <?php if (count($articles) > 0): ?>
                <?php foreach ($articles as $article): ?>
                <div class="content-box" style="margin-bottom: 20px;">
                    <h2 style="color: #2c3e50; margin-top: 0; border-bottom: 2px solid #e3f2fd; padding-bottom: 10px;">
                        <?= htmlspecialchars($article['title']) ?>
                    </h2>
                    
                    <div style="color: #7f8c8d; font-size: 14px; margin-bottom: 15px;">
                        Опубликовано: <?= date('d.m.Y H:i', strtotime($article['created_at'])) ?>
                    </div>
                    
                    <?php if (!empty($article['image'])): ?>
                        <img src="<?= htmlspecialchars($article['image']) ?>" 
                             alt="Изображение статьи" 
                             style="max-width: 300px; height: 200px; object-fit: cover; border-radius: 8px; float: right; margin: 0 0 15px 15px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                    <?php endif; ?>
                    
                    <div style="color: #333; line-height: 1.7;">
                        <p><?= nl2br(htmlspecialchars($article['content'])) ?></p>
                    </div>
                    
                    <div class="admin-actions" style="margin-top: 20px;">
                        <a href="edit_article.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-secondary">
                            Редактировать
                        </a>
                        
                        <form method="POST" action="delete_article.php" style="display: inline;">
                            <input type="hidden" name="article_id" value="<?= $article['id'] ?>">
                            <button type="submit" class="btn btn-sm btn-danger" 
                                    onclick="return confirm('Вы уверены, что хотите удалить эту статью?');">
                                Удалить
                            </button>
                        </form>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="content-box text-center">
                    <div style="padding: 40px;">
                        <p style="font-size: 18px; color: #6c757d; margin-bottom: 20px;">У вас пока нет статей.</p>
                        <a href="create_article.php" class="btn btn-success">Создать первую статью</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

       
        <footer class="footer">
            <div class="container">
                <p>Спортивный портал &copy; <?= date('Y'); ?></p>
            </div>
        </footer>
    </div>

    <script>
      
        document.querySelectorAll('.btn-danger').forEach(btn => {
            btn.addEventListener('click', function(e) {
                if (!confirm('Вы уверены, что хотите удалить эту статью?')) {
                    e.preventDefault();
                }
            });
        });
    </script>
</body>
</html>