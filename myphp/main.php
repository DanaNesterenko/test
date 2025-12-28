<?php
include 'db.php';
session_start();

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn && isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

try {
    $stmt = $pdo->query("SELECT id, title, content, created_at, image FROM articles ORDER BY created_at DESC");
    $articles = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Ошибка загрузки статей: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Спортивный сайт</title>
</head>
<body>
<div class="container">
    <!-- Шапка сайта -->
    <header class="site-header">
        <h1>Спортивный портал</h1>
        <h3>Платформа для спортивного контента</h3>
    </header>

    <!-- Навигация -->
    <nav class="site-nav">
        <div class="nav-wrapper">
            <div class="nav-left">
                <ul class="nav-list">
                    <li><a href="main.php">Главная</a></li>
                    <?php if ($isLoggedIn): ?>
                        <li><a href="create_article.php">Новая статья</a></li>
                        <li><a href="personal_articles.php">Мои статьи</a></li>
                        <li><a href="profile.php">Профиль</a></li>
                    <?php endif; ?>
                    <?php if ($isAdmin): ?>
                        <li><a href="admin.php">Админ-панель</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <div class="nav-right">
                <?php if ($isLoggedIn): ?>
                    <div class="user-display">
                        <?php echo htmlspecialchars($_SESSION['username'] ?? 'Пользователь'); ?>
                    </div>
                    <a href="logout.php" class="btn btn-danger">Выход</a>
                <?php else: ?>
                    <a href="login.php" class="btn">Вход</a>
                    <a href="register.php" class="btn btn-success">Регистрация</a>
                <?php endif; ?>
                
                <a href="https://vk.ru/dana_nesterenko" target="_blank" class="vk-link">
                    <img src="вк.png" alt="VK" class="vk-icon">
                </a>
            </div>
        </div>
    </nav>

    <!-- Основной контент -->
    <main>
        <?php if (empty($articles)): ?>
            <div class="content-box text-center">
                <h3>Статей пока нет</h3>
                <?php if (!$isLoggedIn): ?>
                    <p><a href="login.php" class="text-link">Войдите</a> или <a href="register.php" class="text-link">зарегистрируйтесь</a>, чтобы создать статью</p>
                <?php else: ?>
                    <a href="create_article.php" class="btn btn-success">Создать статью</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($articles as $article): ?>
                <article class="article-preview">
                    <h2 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h2>
                    <div class="article-meta">
                        <?php echo date('d.m.Y H:i', strtotime($article['created_at'])); ?>
                    </div>
                    
                    <?php if (!empty($article['image'])): ?>
                        <img src="<?php echo htmlspecialchars($article['image']); ?>" 
                             alt="<?php echo htmlspecialchars($article['title']); ?>" 
                             class="article-image">
                    <?php endif; ?>
                    
                    <div class="article-content">
                        <p><?php echo nl2br(htmlspecialchars(substr($article['content'], 0, 500))); ?>
                        <?php if (strlen($article['content']) > 500): ?>...<?php endif; ?>
                        </p>
                        
                        <a href="art.php?id=<?php echo $article['id']; ?>" class="btn">
                            Читать полностью
                        </a>
                    </div>
                    
                    <!-- Комментарии -->
                    <div class="comments-area">
                        <h4>Комментарии</h4>
                        <?php
                        try {
                            $commentStmt = $pdo->prepare("SELECT c.comment, c.created_at, u.username 
                                                         FROM comments c 
                                                         JOIN users u ON c.user_id = u.id 
                                                         WHERE c.article_id = :article_id 
                                                         ORDER BY c.created_at DESC 
                                                         LIMIT 3");
                            $commentStmt->execute(['article_id' => $article['id']]);
                            $comments = $commentStmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            $comments = [];
                        }
                        ?>
                        
                        <?php if (!empty($comments)): ?>
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment-item">
                                    <div class="comment-author">
                                        <?php echo htmlspecialchars($comment['username']); ?>
                                    </div>
                                    <p><?php echo nl2br(htmlspecialchars($comment['comment'])); ?></p>
                                    <div class="comment-date">
                                        <?php echo date('d.m.Y H:i', strtotime($comment['created_at'])); ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-comments">
                                Комментариев пока нет.
                            </p>
                        <?php endif; ?>
                        
                        <!-- Форма комментария -->
                        <?php if ($isLoggedIn): ?>
                            <form action="add_comment.php" method="POST" class="comment-form">
                                <input type="hidden" name="article_id" value="<?php echo $article['id']; ?>">
                                <textarea name="content" placeholder="Ваш комментарий..." rows="3" required></textarea>
                                <button type="submit" class="btn btn-success">Отправить комментарий</button>
                            </form>
                        <?php else: ?>
                            <p class="comment-login-prompt">
                                <a href="login.php" class="text-link">Войдите</a>, чтобы оставить комментарий
                            </p>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>

    <!-- Подвал -->
    <footer class="footer">
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Спортивный портал</p>
            <p class="footer-links">
                <a href="https://vk.ru/dana_nesterenko" target="_blank">Наша страница ВКонтакте</a> | 
                <a href="mailto:admin@sport-portal.ru">Контакты</a>
            </p>
        </div>
    </footer>
</div>
</body>
</html>