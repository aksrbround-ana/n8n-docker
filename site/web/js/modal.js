// import { dictionaryLookup } from './dictionary.js';

// ================================================================
// КЛАСС МОДАЛЬНОГО ОКНА
// ================================================================
class Modal {
    constructor(modalId = 'modal-overlay', headerText = '', type = 'calendar') {
        this.modal = document.getElementById(modalId);
        this.currentRow = null; // Здесь будем хранить нажатую строку <tr>

        $(this.modal).find('.modal-header h3').text(headerText);
        if (type === 'calendar') {
            $(this.modal).find('.modal-body').empty().append('<table><thead><tr><th>Company</th><th>Reminder</th></tr></thead><tbody></tbody></table>');
        }

        this.close = this.close.bind(this);
        this.onOverlayClick = this.onOverlayClick.bind(this);
        this.onEscPress = this.onEscPress.bind(this);
        this._initEvents();
    }
    // Приватный метод для назначения обработчиков (эмулируем приватность)
    _initEvents() {
        // 1. Находим все элементы внутри модалки, которые должны её закрывать
        // (крестик и кнопка "Отмена")
        const closeButtons = this.modal.querySelectorAll('.modal-close-btn, .btn-secondary');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', this.close);
        });

        // 2. Закрытие по клику на оверлей
        this.modal.addEventListener('click', this.onOverlayClick);
    }

    open(clickedElement) {
        this.currentRow = clickedElement; // Сохраняем строку в память класса
        this.modal.classList.add('open');
        document.body.classList.add('no-scroll');
        document.addEventListener('keydown', this.onEscPress);
    }

    close() {
        this.modal.classList.remove('open');
        document.body.classList.remove('no-scroll');
        document.removeEventListener('keydown', this.onEscPress);
    }

    // Обработчик клика по фону
    onOverlayClick(event) {
        if (event.target === this.modal) {
            this.close();
        }
    }

    // Обработчик клавиши ESC
    onEscPress(event) {
        if (event.key === 'Escape') {
            this.close();
        }
    }
}

// ================================================================
// ГЛАВНОЕ: Инициализация после загрузки DOM
// ================================================================

let companyListModal;
let companyList = [];
let editReminderModal;

function loadCompanyListModal(id) {
    let user = getUser();
    let token = user ? user?.token : '';
    $.ajax({
        url: '/company/list-to-calendar',
        type: 'POST',
        data: {
            token: token,
            id: id
        },
        success: function (response) {
            if (response.status === 'success') {
                // companyListModal = new Modal('modal-company-list', 'Список компаний');
                $(companyListModal.modal).find('.modal-body ul').empty();
                let ul = $(companyListModal.modal).find('.modal-body tbody');
                for (let i = 0; i < response.data.list.length; i++) {
                    let tr = '<tr><td><label for="company_ps_reminder_' + response.data.list[i].id + '">' + response.data.list[i].name + '</label></td>' +
                        '<td><input id="company_ps_reminder_' + response.data.list[i].id + '" type="checkbox" value="' + response.data.list[i].id + '"' + (response.data.list[i].count > 0 ? ' checked' : '') + ' /></td></tr>';
                    ul.append(tr);
                }
            } else if (response.status === 'logout') {
                clearUser();
                loadContent();
            } else {
                showError('Load error', response.message);
            }
        },
        error: function (e) {
            showError('Load error', e);
        },
        type: 'json'
    })

}

function removeReminder(id) {
    let user = getUser();
    let token = user ? user?.token : '';
    if (confirm('Are you sure you want to delete this calendar row?')) {
        $.ajax({
            url: '/company/delete-calendar-reminder',
            type: 'POST',
            data: {
                token: token,
                id: id
            },
            success: function (response) {
                if (response.status === 'success') {
                    $('#tax-calendar-table').find('tr[data-item-id="' + id + '"]').remove();
                } else if (response.status === 'logout') {
                    clearUser();
                    loadContent();
                } else {
                    showError('Delete error', response.message);
                }
            },
            error: function (e) {
                showError('Delete error', e);
            },
            type: 'json'
        })
    }
}

document.addEventListener('DOMContentLoaded', () => {

    // Делегирование клика по таблице
    document.addEventListener('click', (event) => {
        // Ищем строку таблицы, по которой кликнули
        const row = $(event.target).closest('.open-modal-btn');
        if (row.length === 0) {
            return; // Клик был не по строке с классом .open-modal-btn
        }

        if ($(event.target).hasClass('edit-calendar-btn')) {
            let id = $(row).data('item-id');
            const user = getUser();
            let title = dictionaryLookup('editReminder', user?.lang || 'ru');
            editReminderModal = new Modal('modal-edit-reminder', title, 'edit-reminder');
            // editReminderModal.currentRow = id;
            let text = $('#tax-calendar-table').find('tr[data-item-id="' + id + '"] td.calendar-text').text();
            $(editReminderModal.modal).find('.modal-body').empty().append('<textarea class="edit-reminder-text" style="height: 100%;width: 100%;">' + text + '</textarea>');
            editReminderModal.open(id);
            return;
        } else if ($(event.target).hasClass('delete-calendar-btn')) {
            removeReminder($(row).data('item-id'));
            return;
        } else {
            const title = $(row).find('.calendar-text').text();

            companyListModal = new Modal('modal-overlay', title);
            const id = $(row).data('item-id');
            loadCompanyListModal(id);

            if (row) {
                // Передаем саму строку в метод open
                companyListModal.open(row);
            }
        }

    });

    // Логика кнопки действия
    const actionBtn = document.querySelector('#do-action-btn');
    if (actionBtn) {
        actionBtn.addEventListener('click', () => {
            // Кнопка обращается к свойству currentRow нашего экземпляра класса
            const row = companyListModal.currentRow;

            if (row) {
                // Или получить данные из атрибутов строки
                const reminder_id = $(row).data('item-id');
                const checkedCompanies = [];
                $(companyListModal.modal).find('tbody input[type="checkbox"]:checked').each(function () {
                    checkedCompanies.push($(this).val());
                });
                const unchekedCompanies = [];
                $(companyListModal.modal).find('tbody input[type="checkbox"]').not(':checked').each(function () {
                    unchekedCompanies.push($(this).val());
                });

                let user = getUser();
                let token = user ? user?.token : '';

                $.ajax({
                    url: '/company/update-calendar-reminders',
                    type: 'POST',
                    data: {
                        token: token,
                        reminder_id: reminder_id,
                        checked_companies: checkedCompanies,
                        uncheked_companies: unchekedCompanies
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            // Обновление прошло успешно
                        } else if (response.status === 'logout') {
                            clearUser();
                            loadContent();
                        } else {
                            showError('Update error', response.message);
                        }
                    },
                    error: function (e) {
                        showError('Update error', e);
                    },
                    type: 'json'
                });
            }

            companyListModal.close();
        });
    }

    const doEditReminderBtn = document.querySelector('#do-edit-reminder');
    if (doEditReminderBtn) {
        doEditReminderBtn.addEventListener('click', () => {
            // Логика сохранения изменений напоминания
            const reminder_id = editReminderModal.currentRow;

            if (reminder_id) {

                let user = getUser();
                let token = user ? user?.token : '';
                let text = $(editReminderModal.modal).find('.edit-reminder-text').val();

                $.ajax({
                    url: '/company/update-reminder-details',
                    type: 'POST',
                    data: {
                        token: token,
                        id: reminder_id,
                        text: text
                    },
                    success: function (response) {
                        if (response.status === 'success') {
                            $('#tax-calendar-table').find('tr[data-item-id="' + reminder_id + '"] td.calendar-text').text(text);
                        } else if (response.status === 'logout') {
                            clearUser();
                            loadContent();
                        } else {
                            showError('Update error', response.message);
                        }
                    },
                    error: function (e) {
                        showError('Update error', e);
                    },
                    type: 'json'
                });
            }

            editReminderModal.close();
        });
    }
});