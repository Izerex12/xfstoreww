<?php
// feedback.php - страница с формой обратной связи (Ajax)
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>XF Store - Обратная связь</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="icon" href="favicon (1).ico" type="image/x-icon">
    <style>
        .feedback-section {
            padding: 40px 20px;
            max-width: 800px;
            margin: 0 auto;
        }
        .feedback-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input, .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
        }
        .form-group textarea {
            resize: vertical;
        }
        .submit-btn {
            background-color: #4CAF50;
            color: white;
            padding: 12px 30px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .submit-btn:hover {
            background-color: #45a049;
        }
        .messages-list {
            margin-top: 40px;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .messages-list h3 {
            margin-top: 0;
        }
        .message-item {
            border-bottom: 1px solid #eee;
            padding: 15px 0;
        }
        .message-item:last-child {
            border-bottom: none;
        }
        .message-name {
            font-weight: bold;
            color: #4CAF50;
        }
        .message-email {
            color: #666;
            font-size: 12px;
        }
        .message-text {
            margin: 10px 0;
        }
        .message-date {
            font-size: 11px;
            color: #999;
        }
        .refresh-btn {
            background-color: #2196F3;
            color: white;
            padding: 8px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 15px;
        }
        .refresh-btn:hover {
            background-color: #0b7dda;
        }
        /* Модальное окно */
        .ajax-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 10000;
            justify-content: center;
            align-items: center;
        }
        .ajax-modal-content {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            max-width: 400px;
            margin: 20px;
        }
        .ajax-modal-content button {
            margin-top: 15px;
            padding: 8px 25px;
            background: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .ajax-modal-content.error {
            border-top: 4px solid #f44336;
        }
        .ajax-modal-content.success {
            border-top: 4px solid #4CAF50;
        }
    </style>
</head>
<body>
<header>
    <div class="container">
        <h1>XF Store</h1>
        <nav>
            <ul>
                <li><a href="index.html">Главная</a></li>
                <li><a href="search.php">Поиск</a></li>
                <li><a href="feedback.php">Обратная связь</a></li>
                <li><a href="#">Профиль</a></li>
                <li><a href="#">Корзина (0)</a></li>
            </ul>
        </nav>
    </div>
</header>

<main>
    <div class="feedback-section">
        <div class="feedback-form">
            <h2>Обратная связь</h2>
            <p>Задайте вопрос или оставьте отзыв</p>
            
            <form id="ajaxFeedbackForm">
                <div class="form-group">
                    <label>Ваше имя *</label>
                    <input type="text" name="name" id="fbName" required>
                </div>
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" id="fbEmail" required>
                </div>
                <div class="form-group">
                    <label>Сообщение *</label>
                    <textarea name="message" id="fbMessage" rows="5" required></textarea>
                </div>
                <button type="submit" class="submit-btn">Отправить сообщение</button>
            </form>
        </div>

        <div class="messages-list">
            <h3>Сообщения пользователей</h3>
            <button id="refreshMessages" class="refresh-btn">🔄 Обновить список</button>
            <div id="messagesContainer">
                <p>Загрузка сообщений...</p>
            </div>
            <p style="font-size: 12px; color: #666; margin-top: 10px;">* Список обновляется автоматически каждые 30 секунд</p>
        </div>
    </div>
</main>

<footer>
    <div class="container">
        <p>&copy; 2026 XF Store. Все права защищены.</p>
    </div>
</footer>

<!-- Модальное окно -->
<div id="ajaxModal" class="ajax-modal">
    <div class="ajax-modal-content" id="modalContent">
        <p id="modalMessage"></p>
        <button onclick="closeModal()">OK</button>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    $(document).ready(function() {
        // Загружаем сообщения при загрузке страницы
        loadMessages();
        
        // Автообновление каждые 30 секунд (по требованию - по таймеру)
        setInterval(loadMessages, 30000);
        
        // Кнопка ручного обновления
        $('#refreshMessages').on('click', function() {
            loadMessages();
        });
        
        // Отправка формы через Ajax POST
        $('#ajaxFeedbackForm').on('submit', function(e) {
            e.preventDefault();
            
            // Валидация
            let name = $('#fbName').val().trim();
            let email = $('#fbEmail').val().trim();
            let message = $('#fbMessage').val().trim();
            
            if (name === '' || email === '' || message === '') {
                showModal('Ошибка!', 'Пожалуйста, заполните все поля', 'error');
                return;
            }
            
            if (!email.includes('@') || !email.includes('.')) {
                showModal('Ошибка!', 'Введите корректный email адрес', 'error');
                return;
            }
            
            // Отправка POST запроса (jQuery.post)
            $.post('ajax.php', $(this).serialize())
                .done(function(response) {
                    if (response.success) {
                        showModal('Успешно!', response.message, 'success');
                        $('#ajaxFeedbackForm')[0].reset();
                        loadMessages(); // обновляем список сообщений
                    } else {
                        showModal('Ошибка!', response.error, 'error');
                    }
                })
                .fail(function() {
                    showModal('Ошибка!', 'Не удалось отправить запрос. Проверьте соединение.', 'error');
                });
        });
    });
    
    // Функция загрузки сообщений через GET (jQuery.get)
    function loadMessages() {
        $('#messagesContainer').html('<p>Загрузка...</p>');
        
        $.get('ajax.php')
            .done(function(data) {
                if (Array.isArray(data)) {
                    renderMessages(data);
                } else if (data.error) {
                    $('#messagesContainer').html('<p style="color:red;">Ошибка: ' + data.error + '</p>');
                } else {
                    $('#messagesContainer').html('<p style="color:red;">Неизвестная ошибка</p>');
                }
            })
            .fail(function() {
                $('#messagesContainer').html('<p style="color:red;">Не удалось загрузить сообщения</p>');
            });
    }
    
    // Отображение списка сообщений
    function renderMessages(messages) {
        if (!messages.length) {
            $('#messagesContainer').html('<p>Нет сообщений. Будьте первым!</p>');
            return;
        }
        
        let html = '';
        messages.forEach(function(msg) {
            html += `
                <div class="message-item">
                    <div class="message-name">${escapeHtml(msg.name)}</div>
                    <div class="message-email">${escapeHtml(msg.email)}</div>
                    <div class="message-text">${escapeHtml(msg.message)}</div>
                    <div class="message-date">${msg.created_at}</div>
                </div>
            `;
        });
        $('#messagesContainer').html(html);
    }
    
    // Защита от XSS
    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>]/g, function(m) {
            if (m === '&') return '&amp;';
            if (m === '<') return '&lt;';
            if (m === '>') return '&gt;';
            return m;
        });
    }
    
    // Модальное окно
    function showModal(title, message, type) {
        let modal = $('#ajaxModal');
        let content = $('#modalContent');
        
        content.removeClass('success error');
        content.addClass(type);
        content.html('<h3>' + title + '</h3><p>' + message + '</p><button onclick="closeModal()">OK</button>');
        
        modal.css('display', 'flex');
    }
    
    function closeModal() {
        $('#ajaxModal').css('display', 'none');
    }
    
    // Закрытие модального окна по клику вне его
    $(window).on('click', function(e) {
        if ($(e.target).is('#ajaxModal')) {
            closeModal();
        }
    });
</script>
</body>
</html>