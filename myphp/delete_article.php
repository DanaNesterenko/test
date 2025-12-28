<?php
include 'db.php';
session_start();


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['article_id'])) {
    $articleId = (int)$_POST['article_id'];
    $userId = $_SESSION['user_id'];

  
    $stmt = $pdo->prepare("DELETE FROM articles WHERE id = :id AND author_id = :author_id");
    $stmt->execute([
        'id' => $articleId,
        'author_id' => $userId
    ]);

    header("Location: personal_articles.php?success=Статья удалена.");
    exit;
} else {
    header("Location: personal_articles.php?error=Некорректный запрос.");
    exit;
}
