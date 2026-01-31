/**
 * Менеджер чата
 */
const ChatApp = {
    socket: null,
    chatContainerId: 'chat-display',

    // Инициализация чата
    init: function () {
        console.log('Запуск инициализации чата...');
        // Сначала история, потом сокет. И только ОДИН раз.
        this.loadHistory().then(() => {
            this.connectWebSocket();
        });
    },

    connectWebSocket: function () {
        // Закрываем старый сокет, если он вдруг был
        if (this.socket) {
            this.socket.close();
        }

        this.socket = new WebSocket(`wss://${window.location.host}/ws`);

        this.socket.onopen = () => {
            console.log('Успех: Соединение установлено и стабильно');
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.log('Пришли данные:', data);
            this.renderMessage(data, true);
        };

        this.socket.onclose = (e) => {
            console.log('Соединение закрыто. Код:', e.code);
        };
    },
    // Загрузка истории сообщений из БД через AJAX
    loadHistory: async function () {
        let id = $('#chat-id').val();
        if (id) {
            try {
                const response = await fetch('/chat/history/' + id);
                const data = await response.json();

                const container = document.getElementById(this.chatContainerId);
                if (!container) return;

                // Очищаем контейнер (если там было что-то лишнее)
                container.innerHTML = '';

                // Отрисовываем старые сообщения
                data.forEach(msg => {
                    this.renderMessage(msg, false); // false — не скроллим плавно на каждом сообщении
                });

                // Один раз скроллим в самый низ после загрузки всей пачки
                container.scrollTop = container.scrollHeight;
            } catch (e) {
                console.error('Ошибка загрузки истории:', e);
            }
        }
    },

    // Удаление чата (вызывается при переключении на другой раздел)
    destroy: function () {
        if (this.socket) {
            this.socket.close(); // Явно закрываем соединение
            this.socket = null;
        }
        console.log('Чат деактивирован');
    },

    // Отправка сообщения на бэкенд Yii2 (через AJAX)
    sendMessage: function (text) {
        if (!text.trim()) return;

        let chat_id = $('#chat-id').val();

        fetch('/chat/send', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                // 'X-CSRF-Token': yii.getCsrfToken() // Обязательно для Yii2
            },
            body: JSON.stringify({ message: text, chat_id: chat_id })
        })
            .then(response => response.json())
            .then(data => {
                if (!data.success) alert('Ошибка отправки');
            });
    },

    renderMessage: function (data) {
        const container = document.getElementById(this.chatContainerId);
        if (!container) return;

        // Проверяем, находится ли пользователь внизу чата
        const isAtBottom = container.scrollHeight - container.clientHeight <= container.scrollTop + 50;

        // Определяем класс (incoming или outgoing)
        // Допустим, вы передаете в JSON поле is_my: true/false
        const messageClass = data.is_my ? 'outgoing' : 'incoming';

        const msgHtml = `
        <div class="message ${messageClass}">
            ${data.is_my ? '' : `<strong>${data.username}</strong>`}
            <div class="text">${data.text}</div>
            <span class="meta">${data.date}</span>
        </div>
    `;

        container.insertAdjacentHTML('beforeend', msgHtml);

        if (container.children.length > 100) {
            container.removeChild(container.firstChild);
        }

        // Автопрокрутка, если пользователь был внизу
        if (isAtBottom) {
            container.scrollTop = container.scrollHeight;
        }
    }
};

function handleSendClick() {
    const input = document.getElementById('chat-input');
    const text = input.value;
    if (text) {
        ChatApp.sendMessage(text);
        input.value = ''; // Очищаем поле
    }
}

$(document).on('click', '#send-message-button', (e) => {
    handleSendClick();
});

// --- Пример использования в вашем коде ---

// При клике на "Открыть чат"
$(document).on('click', '#company-chat', (e) => {
    ChatApp.init();
});

// При клике на "Перейти в другой раздел"
$(document).on('click', 'button[data-chat-close="yes"]', (e) => {
    ChatApp.destroy();
});
