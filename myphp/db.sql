-- Создание базы данных (если не существует)
CREATE DATABASE IF NOT EXISTS sports_site CHARACTER SET utf8 COLLATE utf8_general_ci;
USE sports_site;

-- Таблица пользователей
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Таблица статей
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    author_id INT NOT NULL,
    image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Таблица комментариев
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    user_id INT NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Вставка тестовых данных

-- Пароль для admin: admin123
-- Пароль для user1: user123
INSERT INTO users (username, password, email, role) VALUES
('admin', '$2y$10$ldBMHDMQF/gpfnXnm7lfQ.tk4DK69PNr9qP4Yi9CjWQHU.H0wIa12', 'admin@example.com', 'admin'),
('user1', '$2y$10$UfTgJpLjg7n.7kQq3YqQ.OcZ1W8tQKvJvYVpLp5lLxGp5VcW8fLb2', 'user1@example.com', 'user'),
('user2', '$2y$10$UfTgJpLjg7n.7kQq3YqQ.OcZ1W8tQKvJvYVpLp5lLxGp5VcW8fLb2', 'user2@example.com', 'user');

-- Тестовые статьи
INSERT INTO articles (title, content, author_id, image) VALUES
('Добро пожаловать на спортивный сайт', 'Этот сайт создан для любителей спорта. Здесь вы найдете последние новости, статьи и обсуждения на спортивные темы. Присоединяйтесь к нашему сообществу!', 1, 'https://via.placeholder.com/600x400'),
('Как правильно тренироваться?', 'Тренировки должны быть регулярными и систематическими. Важно правильно сочетать кардио и силовые нагрузки, не забывать о растяжке и восстановлении. Рекомендуется заниматься 3-4 раза в неделю по 45-60 минут.', 2, 'https://via.placeholder.com/600x400'),
('Питание для спортсменов', 'Правильное питание - основа спортивных достижений. Важно соблюдать баланс белков, жиров и углеводов, пить достаточное количество воды и употреблять пищу за 1,5-2 часа до тренировки.', 3, 'https://via.placeholder.com/600x400');

-- Тестовые комментарии
INSERT INTO comments (article_id, user_id, comment) VALUES
(1, 2, 'Отличный сайт! Уже порекомендовал друзьям.'),
(1, 3, 'Интересная концепция, буду следить за обновлениями.'),
(2, 1, 'Хорошая статья, но хотелось бы больше конкретных упражнений.'),
(2, 3, 'Спасибо за советы! Уже начал применять их на практике.'),
(3, 2, 'А есть примеры конкретных меню на день?');

-- Создание индексов для ускорения запросов
CREATE INDEX idx_articles_author ON articles(author_id);
CREATE INDEX idx_comments_article ON comments(article_id);
CREATE INDEX idx_comments_user ON comments(user_id);
CREATE INDEX idx_users_username ON users(username);
CREATE INDEX idx_users_email ON users(email);