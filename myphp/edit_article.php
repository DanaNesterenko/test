<?php
include 'db.php';
session_start();

if (!isset($_GET['id']) || !isset($_SESSION['user_id'])) {
    header("Location: main.php");
    exit;
}

$articleId = $_GET['id'];

$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = :id AND author_id = :author_id");
$stmt->execute(['id' => $articleId, 'author_id' => $_SESSION['user_id']]);
$article = $stmt->fetch();

if (!$article) {
    header("Location: main.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $newImage = $_FILES['image'];

    if (!empty($title) && !empty($content)) {
        $imagePath = $article['image'];

        if (!empty($newImage['name'])) {
            $uploadDir = 'uploads/';
            $imageName = time() . '_' . basename($newImage['name']);
            $uploadFile = $uploadDir . $imageName;

            if (move_uploaded_file($newImage['tmp_name'], $uploadFile)) {
                $imagePath = $uploadFile;
            }
        }

        $stmt = $pdo->prepare("UPDATE articles SET title = :title, content = :content, image = :image WHERE id = :id AND author_id = :author_id");
        $stmt->execute([
            'title' => $title,
            'content' => $content,
            'image' => $imagePath,
            'id' => $articleId,
            'author_id' => $_SESSION['user_id']
        ]);

        header("Location: personal_articles.php?success=Статья обновлена");
        exit;
    } else {
        $error = "Пожалуйста, заполните все поля.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактировать статью</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
 
    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Редактирование статьи</div>
            <ul class="nav-menu">
                <li><a href="main.php">Главная</a></li>
                <li><a href="personal_articles.php">Мои статьи</a></li>
                <li><a href="profile.php">Профиль</a></li>
                <li><a href="logout.php">Выход</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="content-box">
            <h1 style="color: #2c3e50; margin-bottom: 20px;">Редактировать статью</h1>
            
            <?php if (isset($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form action="edit_article.php?id=<?= $articleId ?>" method="POST" enctype="multipart/form-data" class="edit-form">
                <div class="form-group">
                    <label for="title" class="form-label">Заголовок:</label>
                    <input type="text" id="title" name="title" 
                           value="<?= htmlspecialchars($article['title']) ?>" 
                           class="form-input" required>
                </div>
                
                <div class="form-group">
                    <label for="content" class="form-label">Содержимое:</label>
                    <textarea id="content" name="content" 
                              class="form-textarea" required
                              rows="8"><?= htmlspecialchars($article['content']) ?></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Текущее изображение:</label>
                    <?php if (!empty($article['image'])): ?>
                        <div style="text-align: center; margin: 10px 0;">
                            <img src="<?= htmlspecialchars($article['image']) ?>" 
                                 alt="Изображение статьи" 
                                 style="max-width: 100%; max-height: 200px; border-radius: 4px;">
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="image" class="form-label">Загрузить новое изображение:</label>
                    <input type="file" id="image" name="image" 
                           class="form-input"
                           accept="image/*">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Сохранить изменения</button>
                    <a href="personal_articles.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>