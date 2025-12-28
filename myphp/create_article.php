<?php
include 'db.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $author_id = $_SESSION['user_id'];
    
    if (!empty($_FILES['image']['name'])) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $imagePath = $uploadDir . basename($_FILES['image']['name']);
        
        if (move_uploaded_file($_FILES['image']['tmp_name'], $imagePath)) {
            $stmt = $pdo->prepare("INSERT INTO articles (title, content, author_id, image) 
                                   VALUES (:title, :content, :author_id, :image)");
            $stmt->execute([
                'title' => $title,
                'content' => $content,
                'author_id' => $author_id,
                'image' => $imagePath
            ]);
            header("Location: main.php?success=Статья добавлена.");
            exit;
        } else {
            $error = "Ошибка при загрузке изображения.";
        }
    } else {
        $error = "Пожалуйста, добавьте изображение.";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Добавить статью</title>
</head>
<body>
    <!-- Навигация -->
    <nav class="nav-main">
        <div class="container nav-container">
            <div class="nav-logo">Добавить статью</div>
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
            <header style="background: #34495e; color: white; padding: 15px; text-align: center; border-radius: 8px 8px 0 0; margin-bottom: 20px;">
                <h1 style="margin: 0; font-size: 22px;">Добавить статью</h1>
            </header>
            
            <?php if (!empty($error)): ?>
                <div class="error-message"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="create_article.php" enctype="multipart/form-data" class="edit-form">
                <div class="form-group">
                    <label class="form-label">Заголовок:</label>
                    <input type="text" name="title" 
                           class="form-input" 
                           placeholder="Заголовок статьи" 
                           required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Текст статьи:</label>
                    <textarea name="content" 
                              class="form-textarea" 
                              placeholder="Текст статьи" 
                              required
                              rows="10"></textarea>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Изображение:</label>
                    <input type="file" name="image" 
                           class="form-input"
                           accept="image/*" 
                           required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-success">Добавить статью</button>
                    <a href="main.php" class="btn btn-secondary">Отмена</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>