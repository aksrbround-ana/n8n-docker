/**
 * Менеджер чата
 */
const ChatApp = {
    socket: null,
    chatContainerId: 'chat-display',

    // Инициализация чата
    init: function () {
        console.log('Подключение к чату...');

        // 1. Сначала загружаем историю из БД
        this.loadHistory().then(() => {
            // 2. Только ПОСЛЕ загрузки истории подключаем сокет
            // Вся настройка теперь внутри этого метода
            this.connectWebSocket();
        });
    },

    connectWebSocket: function () {
        // Создаем соединение
        this.socket = new WebSocket(`wss://${window.location.host}/ws`);

        // Настраиваем обработчики ПРЯМО ЗДЕСЬ, когда socket точно не null
        this.socket.onopen = () => {
            console.log('Соединение с WebSocket установлено');
        };

        this.socket.onmessage = (event) => {
            const data = JSON.parse(event.data);
            console.dir(['От WebSocket получено сообщение:', data]);
            this.renderMessage(data, true);
        };

        this.socket.onclose = () => {
            console.log('Соединение с WebSocket закрыто');
        };

        this.socket.onerror = (error) => {
            console.error('Ошибка WebSocket:', error);
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

    // Отрисовка сообщения в DOM
    // renderMessage: function (data, smoothScroll = true) {
    //     const container = document.getElementById(this.chatContainerId);
    //     if (!container) return;

    //     const messageClass = data.is_my ? 'outgoing' : 'incoming';
    //     const msgHtml = `
    //         <div class="message ${messageClass}">
    //             ${data.is_my ? '' : `<strong>${data.username}</strong>`}
    //             <div class="text">${data.text}</div>
    //             <span class="meta">${data.date}</span>
    //         </div>
    //     `;

    //     container.insertAdjacentHTML('beforeend', msgHtml);

    //     if (smoothScroll) {
    //         container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
    //     }
    // },

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
    // document.getElementById('company-chat').addEventListener('click', () => {
    // 1. Подгружаете ваш HTML...
    // 2. Запускаете чат:
    ChatApp.init();
});

// При клике на "Перейти в другой раздел"
$(document).on('click', 'button[data-chat-close="yes"]', (e) => {
    ChatApp.destroy();
});
// document.getElementById('other-section-btn').addEventListener('click', () => {
//     ChatApp.destroy();
// });