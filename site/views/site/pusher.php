<script src="https://js.pusher.com/8.0.1/pusher.min.js"></script>
<script>
    // Включаем логирование для отладки (удалите в продакшене)
    Pusher.logToConsole = true;

    const pusher = new Pusher('ВАШ_KEY', {
        cluster: 'eu'
    });

    const channel = pusher.subscribe('chat-channel');

    // Слушаем событие, которое отправил Yii2
    channel.bind('new-message', function(data) {
        // Здесь логика добавления сообщения в DOM
        const chatBox = document.getElementById('chat-messages');
        const newMessage = document.createElement('div');
        newMessage.innerHTML = `<b>${data.from}:</b> ${data.message} <small>${data.time}</small>`;
        chatBox.appendChild(newMessage);

        // Скролл вниз
        chatBox.scrollTop = chatBox.scrollHeight;
    });

    // Функция отправки сообщения
    function sendMessage() {
        const input = document.getElementById('message-input');
        const message = input.value;

        if (!message) return;

        // Отправляем на бэкенд Yii2
        fetch('/chat/send-message', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    // Добавляем CSRF токен для защиты Yii2
                    'X-CSRF-Token': yii.getCsrfToken()
                },
                body: JSON.stringify({
                    text: message,
                    chat_id: '123456', // ID чата из вашей БД
                    user_id: 1 // ID текущего оператора
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    input.value = ''; // Очищаем поле
                    console.log('Сообщение отправлено в n8n');
                }
            })
            .catch(error => console.error('Ошибка:', error));
    }
</script>

<div id="chat-messages" style="height: 400px; overflow-y: auto; border: 1px solid #ccc;">
</div>