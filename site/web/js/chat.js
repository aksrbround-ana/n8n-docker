
// Состояние приложения
let currentChatId = null;
// let centrifuge = null;
let activeSubscriptions = {};

// DOM элементы
let chatList;
let messagesContainer;
let chatHeader;
let inputContainer;
let messageInput;
let sendButton;
let connectionStatus;

// Инициализация Centrifugo
function initCentrifugo() {
    centrifuge = new Centrifuge(CONFIG.centrifugoUrl, {
        token: CONFIG.centrifugoToken
    });

    centrifuge.on('connected', function (ctx) {
        console.log('Connected to Centrifugo', ctx);
        connectionStatus.textContent = 'Подключено';
        connectionStatus.className = 'connection-status connected';
    });

    centrifuge.on('disconnected', function (ctx) {
        console.log('Disconnected from Centrifugo', ctx);
        connectionStatus.textContent = 'Отключено. Переподключение...';
        connectionStatus.className = 'connection-status';
    });

    centrifuge.connect();
}

// Подписка на канал чата
function subscribeToChat(chatId) {
    const channelName = `chat:${chatId}`;

    if (activeSubscriptions[channelName]) {
        return; // Уже подписаны
    }

    const subscription = centrifuge.newSubscription(channelName);

    subscription.on('publication', function (ctx) {
        console.log('New message from Centrifugo:', ctx.data);
        if (currentChatId == chatId) {
            addMessageToUI(ctx.data);
        }
    });

    subscription.subscribe();
    activeSubscriptions[channelName] = subscription;
}

// Отписка от канала
function unsubscribeFromChat(chatId) {
    const channelName = `chat:${chatId}`;
    if (activeSubscriptions[channelName]) {
        activeSubscriptions[channelName].unsubscribe();
        delete activeSubscriptions[channelName];
    }
}

// Загрузка списка чатов
async function loadChats() {
    try {
        const response = await fetch(`${CONFIG.yii2ApiUrl}/chat/get-active-chats`);
        const data = await response.json();

        if (data.success) {
            renderChatList(data.chats);
        }
    } catch (error) {
        console.error('Error loading chats:', error);
    }
}

// Отрисовка списка чатов
function renderChatList(chats) {
    chatList.innerHTML = '';
    chats.forEach(chat => {
        const chatItem = document.createElement('div');
        chatItem.className = 'chat-item';
        chatItem.dataset.chatId = chat.chat_id;
        chatItem.innerHTML = `
            <div class="chat-item-username">${chat.username || 'Пользователь ' + chat.chat_id}</div>
            <div class="chat-item-last-message">Сообщений: ${chat.message_count}</div>
        `;
        chatItem.onclick = () => selectChat(chat.chat_id, chat.username);
        chatList.appendChild(chatItem);
    });
}

// Выбор чата
async function selectChat(chatId, username) {
    // Отписываемся от предыдущего чата
    if (currentChatId) {
        unsubscribeFromChat(currentChatId);
    }

    currentChatId = chatId;

    // Обновляем UI
    document.querySelectorAll('.chat-item').forEach(item => {
        item.classList.toggle('active', item.dataset.chatId == chatId);
    });

    chatHeader.textContent = username || `Чат ${chatId}`;
    inputContainer.style.display = 'flex';

    // Загружаем историю сообщений
    await loadMessages(chatId);

    // Подписываемся на новые сообщения
    subscribeToChat(chatId);
}

// Загрузка истории сообщений
async function loadMessages(chatId) {
    try {
        const response = await fetch(`${CONFIG.yii2ApiUrl}/chat/get-messages?chatId=${chatId}&limit=50`);
        const data = await response.json();

        if (data.success) {
            messagesContainer.innerHTML = '';
            data.messages.reverse().forEach(message => {
                addMessageToUI(message);
            });
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error loading messages:', error);
    }
}

// Добавление сообщения в UI
function addMessageToUI(messageData) {
    const messageDiv = document.createElement('div');
    const isIncoming = messageData.from === 'telegram' || messageData.type === 'incoming';

    messageDiv.className = `message ${isIncoming ? 'incoming' : 'outgoing'}`;

    let senderName = '';
    if (isIncoming) {
        senderName = messageData.username || 'Пользователь';
    } else {
        senderName = messageData.operator_name || 'Оператор';
    }

    messageDiv.innerHTML = `
        <div>${messageData.text || messageData.response_text}</div>
        <div class="message-meta">${senderName} • ${formatTime(messageData.timestamp || messageData.created_at)}</div>
    `;

    messagesContainer.appendChild(messageDiv);
    scrollToBottom();
}

// Отправка сообщения
async function sendMessage() {
    const text = messageInput.value.trim();
    if (!text || !currentChatId) return;

    sendButton.disabled = true;
    messageInput.disabled = true;

    try {
        const response = await fetch(`${CONFIG.yii2ApiUrl}/chat/send-message`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                chat_id: currentChatId,
                operator_id: CONFIG.operatorId,
                operator_name: CONFIG.operatorName,
                response_text: text
            })
        });

        const data = await response.json();

        if (data.success) {
            messageInput.value = '';
        } else {
            alert('Ошибка отправки сообщения: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Error sending message:', error);
        alert('Ошибка отправки сообщения');
    } finally {
        sendButton.disabled = false;
        messageInput.disabled = false;
        messageInput.focus();
    }
}

// Вспомогательные функции
function scrollToBottom() {
    messagesContainer.scrollTop = messagesContainer.scrollHeight;
}

function formatTime(timestamp) {
    const date = new Date(timestamp);
    return date.toLocaleTimeString('ru-RU', { hour: '2-digit', minute: '2-digit' });
}


$(document).on('click', '#company-chat', function (e) {
    if ($(this).data('state') === 'active') {
        chatList = document.getElementById('chatList');
        messagesContainer = document.getElementById('messagesContainer');
        chatHeader = document.getElementById('chatHeader');
        inputContainer = document.getElementById('inputContainer');
        messageInput = document.getElementById('messageInput');
        sendButton = document.getElementById('sendButton');
        connectionStatus = document.getElementById('connectionStatus');
        // Обработчики событий
        sendButton.onclick = sendMessage;
        messageInput.onkeypress = (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
        };

        // Инициализация
        initCentrifugo();
        loadChats();

        // Обновление списка чатов каждые 30 секунд
        setInterval(loadChats, 30000);
    }
});