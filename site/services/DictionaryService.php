<?php

namespace app\services;

class DictionaryService
{
    static array $prioritySign = [
        'low' => '↓',
        'normal' => '●',
        'high' => '↑',
    ];

    static array $months = [
        'january',
        'february',
        'march',
        'april',
        'may',
        'june',
        'july',
        'august',
        'september',
        'october',
        'november',
        'december',
    ];
    static array $dictionary = [
        'ru' =>   [
            // Months
            'january' => 'Январь',
            'february' => 'Февраль',
            'march' => 'Март',
            'april' => 'Апрель',
            'may' => 'Май',
            'june' => 'Июнь',
            'july' => 'Июль',
            'august' => 'Август',
            'september' => 'Сентябрь',
            'october' => 'Октябрь',
            'november' => 'Ноябрь',
            'december' => 'Декабрь',

            // Navigation
            'dashboard' =>  'Главная',
            'companies' =>  'Компании',
            'tasks' =>  'Задачи',
            'documents' =>  'Документы',
            'reports' =>  'Отчёты',
            'settings' =>  'Настройки',
            'reminders' => 'Напоминания',
            'users' =>  'Пользователи',

            // Roles
            'ceo' =>  'Руководитель',
            'admin' =>  'Администратор',
            'accountant' =>  'Бухгалтер',

            // Auth
            'login' =>  'Войти',
            'password' =>  'Пароль',
            'oldPassword' =>  'Старый пароль',
            'newPassword' =>  'Новый пароль',
            'confirmPassword' =>  'Подтвердите пароль',
            'rememberMe' =>  'Запомнить меня',
            'forgotPassword' =>  'Забыли пароль?',
            'resetPassword' =>  'Сбросить пароль',
            'resetPasswordInstructions' =>  'Введите ваш адрес электронной почты, чтобы сбросить пароль.',

            // Common
            'search' =>  'Поиск',
            'searchPlaceholder' =>  'Поиск по компании, ПИБ, задаче...',
            'logout' =>  'Выйти',
            'profile' =>  'Профиль',
            'save' =>  'Сохранить',
            'cancel' =>  'Отмена',
            'delete' =>  'Удалить',
            'edit' =>  'Редактировать',
            'add' =>  'Добавить',
            'create' =>  'Создать',
            'close' =>  'Закрыть',
            'finish' =>  'Завершить',
            'filter' =>  'Фильтр',
            'filters' =>  'Фильтры',
            'clearFilters' =>  'Сбросить',
            'apply' =>  'Применить',
            'actions' =>  'Действия',
            'view' =>  'Просмотр',
            'upload' =>  'Загрузить',
            'download' =>  'Скачать',
            'comment' =>  'Комментарий',
            'comments' =>  'Комментарии',
            'noData' =>  'Нет данных',
            'loading' =>  'Загрузка...',
            'all' =>  'Все',

            // Dashboard
            'welcomeBack' =>  'Добро пожаловать',
            'overview' =>  'Обзор',
            'totalClients' =>  'Всего клиентов',
            'activeTasks' =>  'Активные задачи',
            'overdueTasks' =>  'Просроченные задачи',
            'pendingDocuments' =>  'Документы на проверке',
            'upcomingDeadlines' =>  'Ближайшие сроки',
            'tasksByAccountant' =>  'Задачи по бухгалтерам',
            'recentActivity' =>  'Последняя активность',
            'workloadDistribution' =>  'Загрузка бухгалтеров',

            // Companies
            'companyName' =>  'Название компании',
            'pib' =>  'ПИБ',
            'city' =>  'Город',
            'sector' =>  'Сектор',
            'assignedAccountant' =>  'Ответственный бухгалтер',
            'status' =>  'Статус',
            'openTasks' =>  'Открытые задачи',
            'overdueCount' =>  'Просрочено',
            'openProfile' =>  'Открыть профиль',
            'companyProfile' =>  'Профиль компании',
            'contactPerson' =>  'Контактное лицо',
            'email' =>  'Email',
            'phone' =>  'Телефон',
            'notes' =>  'Заметки',
            'contacts' =>  'Контакты',
            'chat' => 'Чат',
            'addCompany' =>  'Добавить компанию',

            // Company statuses
            'statusActive' =>  'Активная',
            'statusOnboarding' =>  'Адаптация',
            'statusPaused' =>  'Приостановлена',
            'statusInactive' =>  'Неактивная',

            // Tasks
            'taskId' =>  'ID',
            'taskType' =>  'Тип задачи',
            'period' =>  'Период',
            'priority' =>  'Приоритет',
            'dueDate' =>  'Срок',
            'assignedTo' =>  'Исполнитель',
            'lastUpdate' =>  'Обновлено',
            'taskDetails' =>  'Детали задачи',
            'description' =>  'Описание',
            'checklist' =>  'Чек-лист',
            'linkedDocuments' =>  'Связанные документы',
            'activityLog' =>  'История изменений',
            'addTask' =>  'Создать задачу',
            'bulkActions' =>  'Массовые действия',
            'changeStatus' =>  'Изменить статус',
            'assignAccountant' =>  'Назначить исполнителя',
            'selectedTasks' =>  'Выбрано задач',

            // Task statuses
            'taskStatusNew' =>  'Новая',
            'taskStatusInProgress' =>  'В работе',
            'taskStatusWaiting' =>  'Ожидание документов',
            'taskStatusDone' =>  'Выполнена',
            'taskStatusOverdue' =>  'Просрочена',
            'taskStatusClosed' =>  'Закрыта',
            'taskStatusArchived' =>  'Архивирована',

            // Task activity steps
            'taskStepCreated' =>  'создана',
            'taskStepAssigned' =>  'назначена',
            'taskStepInProgress' =>  'в работе',
            'taskStepWaiting' =>  'ожидает документы',
            'taskStepOverdue' =>  'просрочена',
            'taskStepDone' =>  'выполнена',
            'taskStepClosed' =>  'закрыта',
            'taskStepArchived' =>  'архивирована',
            'taskStepPriorityChanged' =>  'приоритет изменён',
            'taskStepDueDateChanged' =>  'срок изменён',

            // Task types
            'taskTypeVat' =>  'НДС декларация',
            'taskTypePayroll' =>  'Зарплата',
            'taskTypeAnnualReport' =>  'Годовой отчёт',
            'taskTypeReconciliation' =>  'Сверка',
            'taskTypeOther' =>  'Другое',

            // Priorities
            'priorityLow' =>  'Низкий',
            'priorityNormal' =>  'Обычный',
            'priorityHigh' =>  'Высокий',

            // Documents
            'fileName' =>  'Имя файла',
            'documentType' =>  'Тип документа',
            'uploadedBy' =>  'Загрузил',
            'uploadDate' =>  'Дата загрузки',
            'documentStatus' =>  'Статус',
            'uploadDocuments' =>  'Загрузить документы',
            'dragDropHint' =>  'Перетащите файлы сюда или нажмите для выбора',
            'selectCompany' =>  'Выберите компанию',
            'selectPeriod' =>  'Выберите период',
            'selectDocType' =>  'Выберите тип документа',

            // Document types
            'docTypeUnknown' =>  'Неизвестно',
            'docTypeInvoice' =>  'Счёт-фактура',
            'docTypeBankStatement' =>  'Банковская выписка',
            'docTypePayroll' =>  'Зарплатная ведомость',
            'docTypeContract' =>  'Договор',
            'docTypeTaxReturn' =>  'Налоговая декларация',
            'docTypeOther' =>  'Другое',

            // Document statuses
            'docStatusUploaded' =>  'Загружен',
            'docStatusChecked' =>  'Проверен',
            'docStatusNeedsRevision' =>  'Требует правки',
            'docStatusProcessing' => 'В обработке',

            // Notes
            'addNote' => 'Добавить заметку',

            // User management
            'userManagement' =>  'Управление пользователями',
            'createUser' =>  'Создать пользователя',
            'userName' =>  'Имя',
            'userEmail' =>  'Email',
            'userRole' =>  'Роль',
            'userStatus' =>  'Статус',
            'userActive' =>  'Активен',
            'userBlocked' =>  'Заблокирован',
            'blockUser' =>  'Заблокировать',
            'unblockUser' =>  'Разблокировать',

            // Settings
            'systemSettings' =>  'Системные настройки',
            'systemSettingsManaging' =>  'Управление настройками системы',
            'taxPeriods' =>  'Налоговые периоды',
            'defaultDeadlines' =>  'Сроки по умолчанию',
            'defaultDeadlinesForVariousTasks' =>  'Сроки по умолчанию для различных типов задач',
            'notificationTemplates' =>  'Шаблоны уведомлений',
            'notificationTemplatesForTelegram' => 'Шаблоны уведомлений для Telegram',
            'set' => 'настроить',

            // Time
            'today' =>  'Сегодня',
            'yesterday' =>  'Вчера',
            'thisWeek' =>  'Эта неделя',
            'thisMonth' =>  'Этот месяц',
            'overdue' =>  'Просрочено',
            'daysLeft' =>  'дней осталось',
            'daysOverdue' =>  'дней просрочено',

            // Reminders
            'actionDate' => 'Дата действия',
            'reminderDate' => 'Дата напоминания',
            'escalationDate' => 'Дата эскалации',
            'activityType' => 'Тип активности',
            'reminderText' => 'Текст напоминания',
            'reminderType' => 'Тип напоминания',
            'taxCalendar' => 'Налоговый календарь',
            'regularReminders' => 'Регулярные напоминания',
            'loadTaxCalendarPage' => 'Загрузить страницу налогового календаря',
            'editReminder' => 'Редактировать напоминание',

            // OCR
            'ocrSummary' => 'Резюме',
            'ocrCategory' => 'Категория',
            'ocr' => 'OCR текст',
            'notRecognized'=>'Не распознано',

            // Other
            'addComment' => 'Добавить комментарий',
            'companyInformation' => 'Информация о компании',
            'activity' => 'Активность',
            'viewDocument' => 'Просмотр документа',
            'open' => 'Открыть',
            'documentInformation' => 'Информация о документе',
            'fileSize' => 'Размер файла',
            'company' => 'Компания',
            'currentStatus' => 'Текущий статус',
            'by_unknown' => 'Неизвестно',
            'unknown' => 'Неизвестно',
            'information' => 'Информация',
            'noItemsToShow' => 'Нет элементов для отображения',
            'yourRequestHasBeenSent' => 'Запрос отправлен. Проверьте через пару минут.',
            'loadMonth' => 'Загрузить месяц',

        ],
        'rs' =>   [
            // Months
            'january' => 'Januar',
            'february' => 'Februar',
            'march' => 'Mart',
            'april' => 'April',
            'may' => 'Maj',
            'june' => 'Jun',
            'july' => 'Jul',
            'august' => 'Avgust',
            'september' => 'Septembar',
            'october' => 'Oktobar',
            'november' => 'Novembar',
            'december' => 'Decembar',

            // Navigation
            'dashboard' =>  'Početna',
            'companies' =>  'Kompanije',
            'tasks' =>  'Zadaci',
            'documents' =>  'Dokumenti',
            'reports' =>  'Izveštaji',
            'settings' =>  'Podešavanja',
            'reminders' => 'Podsetnici',
            'users' =>  'Korisnici',

            // Roles
            'ceo' =>  'Direktor',
            'admin' =>  'Administrator',
            'accountant' =>  'Knjigovođa',

            // Auth
            'login' =>  'Prijava',
            'password' =>  'Lozinka',
            'oldPassword' =>  'Stara lozinka',
            'newPassword' =>  'Nova lozinka',
            'confirmPassword' =>  'Potvrdi lozinku',
            'rememberMe' =>  'Zapamti me',
            'forgotPassword' =>  'Zaboravili ste lozinku?',
            'resetPassword' =>  'Resetuj lozinku',
            'resetPasswordInstructions' =>  'Unesite vašu email adresu da biste resetovali lozinku.',

            // Common
            'search' =>  'Pretraga',
            'searchPlaceholder' =>  'Pretraga po kompaniji, PIB-u, zadatku...',
            'logout' =>  'Odjava',
            'profile' =>  'Profil',
            'save' =>  'Sačuvaj',
            'cancel' =>  'Otkaži',
            'delete' =>  'Obriši',
            'edit' =>  'Izmeni',
            'add' =>  'Dodaj',
            'create' =>  'Kreiraj',
            'close' =>  'Zatvori',
            'finish' =>  'Završi',
            'filter' =>  'Filter',
            'filters' =>  'Filteri',
            'clearFilters' =>  'Poništi',
            'apply' =>  'Primeni',
            'actions' =>  'Akcije',
            'view' =>  'Pregled',
            'upload' =>  'Otpremi',
            'download' =>  'Preuzmi',
            'comment' =>  'Komentar',
            'comments' =>  'Komentari',
            'noData' =>  'Nema podataka',
            'loading' =>  'Učitavanje...',
            'all' =>  'Sve',

            // Dashboard
            'welcomeBack' =>  'Dobrodošli',
            'overview' =>  'Pregled',
            'totalClients' =>  'Ukupno klijenata',
            'activeTasks' =>  'Aktivni zadaci',
            'overdueTasks' =>  'Zakasneli zadaci',
            'pendingDocuments' =>  'Dokumenti na proveri',
            'upcomingDeadlines' =>  'Nadolazeći rokovi',
            'tasksByAccountant' =>  'Zadaci po knjigovođama',
            'recentActivity' =>  'Poslednja aktivnost',
            'workloadDistribution' =>  'Opterećenje knjigovođa',

            // Companies
            'companyName' =>  'Naziv kompanije',
            'pib' =>  'PIB',
            'city' =>  'Grad',
            'sector' =>  'Delatnost',
            'assignedAccountant' =>  'Zaduženi knjigovođa',
            'status' =>  'Status',
            'openTasks' =>  'Otvoreni zadaci',
            'overdueCount' =>  'Zakasnelo',
            'openProfile' =>  'Otvori profil',
            'companyProfile' =>  'Profil kompanije',
            'contactPerson' =>  'Kontakt osoba',
            'email' =>  'Email',
            'phone' =>  'Telefon',
            'notes' =>  'Beleške',
            'contacts' =>  'Kontakti',
            'chat' => 'Chat',
            'addCompany' =>  'Dodaj kompaniju',

            // Company statuses
            'statusActive' =>  'Aktivna',
            'statusOnboarding' =>  'U postupku',
            'statusPaused' =>  'Pauzirana',
            'statusInactive' =>  'Neaktivna',

            // Tasks
            'taskId' =>  'ID',
            'taskType' =>  'Tip zadatka',
            'period' =>  'Period',
            'priority' =>  'Prioritet',
            'dueDate' =>  'Rok',
            'assignedTo' =>  'Izvršilac',
            'lastUpdate' =>  'Ažurirano',
            'taskDetails' =>  'Detalji zadatka',
            'description' =>  'Opis',
            'checklist' =>  'Kontrolna lista',
            'linkedDocuments' =>  'Povezani dokumenti',
            'activityLog' =>  'Istorija promena',
            'addTask' =>  'Kreiraj zadatak',
            'bulkActions' =>  'Grupne akcije',
            'changeStatus' =>  'Promeni status',
            'assignAccountant' =>  'Dodeli izvršioca',
            'selectedTasks' =>  'Izabrano zadataka',

            // Task statuses
            'taskStatusNew' =>  'Novi',
            'taskStatusInProgress' =>  'U toku',
            'taskStatusWaiting' =>  'Čeka dokumente',
            'taskStatusDone' =>  'Završen',
            'taskStatusOverdue' =>  'Zakasnelo',
            'taskStatusClosed' =>  'Zatvoren',
            'taskStatusArchived' =>  'Arhiviran',

            // Task activity steps
            'taskStepCreated' =>  'kreirana',
            'taskStepAssigned' =>  'dodeljena',
            'taskStepInProgress' =>  'u radu',
            'taskStepWaiting' =>  'čeka dokumente',
            'taskStepOverdue' =>  'zakasnela',
            'taskStepDone' =>  'završena',
            'taskStepClosed' =>  'zatvorena',
            'taskStepArchived' =>  'arhivirana',
            'taskStepPriorityChanged' =>  'prioritet izmenjen',
            'taskStepDueDateChanged' =>  'rok izmenjen',

            // Task types
            'taskTypeVat' =>  'PDV prijava',
            'taskTypePayroll' =>  'Obračun zarada',
            'taskTypeAnnualReport' =>  'Godišnji izveštaj',
            'taskTypeReconciliation' =>  'Usaglašavanje',
            'taskTypeOther' =>  'Ostalo',

            // Priorities
            'priorityLow' =>  'Nizak',
            'priorityNormal' =>  'Normalan',
            'priorityHigh' =>  'Visok',

            // Documents
            'fileName' =>  'Naziv fajla',
            'documentType' =>  'Tip dokumenta',
            'uploadedBy' =>  'Otpremio',
            'uploadDate' =>  'Datum otpreme',
            'documentStatus' =>  'Status',
            'uploadDocuments' =>  'Otpremi dokumente',
            'dragDropHint' =>  'Prevucite fajlove ovde ili kliknite za izbor',
            'selectCompany' =>  'Izaberite kompaniju',
            'selectPeriod' =>  'Izaberite period',
            'selectDocType' =>  'Izaberite tip dokumenta',

            // Document types
            'docTypeUnknown' =>  'Nepoznato',
            'docTypeInvoice' =>  'Faktura',
            'docTypeBankStatement' =>  'Izvod iz banke',
            'docTypePayroll' =>  'Platni spisak',
            'docTypeContract' =>  'Ugovor',
            'docTypeTaxReturn' =>  'Poreska prijava',
            'docTypeOther' =>  'Ostalo',

            // Document statuses
            'docStatusUploaded' =>  'Otpremljen',
            'docStatusChecked' =>  'Proveren',
            'docStatusNeedsRevision' =>  'Potrebna ispravka',
            'docStatusProcessing' => 'U obradi',

            // Notes
            'addNote' => 'Dodaj belešku',

            // User management
            'userManagement' =>  'Upravljanje korisnicima',
            'createUser' =>  'Kreiraj korisnika',
            'userName' =>  'Ime',
            'userEmail' =>  'Email',
            'userRole' =>  'Uloga',
            'userStatus' =>  'Status',
            'userActive' =>  'Aktivan',
            'userBlocked' =>  'Blokiran',
            'blockUser' =>  'Blokiraj',
            'unblockUser' =>  'Odblokiraj',

            // Settings
            'systemSettings' =>  'Sistemska podešavanja',
            'systemSettingsManaging' =>  'Upravljanje podešavanjima sistema',
            'taxPeriods' =>  'Poreski periodi',
            'defaultDeadlines' =>  'Podrazumevani rokovi',
            'defaultDeadlinesForVariousTasks' =>  'Podrazumevani rokovi za različite zadatke',
            'notificationTemplates' =>  'Šabloni obaveštenja',
            'notificationTemplatesForTelegram' => 'Šabloni obaveštenja za Telegram',
            'set' => 'Podesi',

            // Time
            'today' =>  'Danas',
            'yesterday' =>  'Juče',
            'thisWeek' =>  'Ova nedelja',
            'thisMonth' =>  'Ovaj mesec',
            'overdue' =>  'Zakasnelo',
            'daysLeft' =>  'dana preostalo',
            'daysOverdue' =>  'dana zakašnjenja',

            // Reminders
            'actionDate' => 'Datum akcije',
            'reminderDate' => 'Datum podsećanja',
            'escalationDate' => 'Datum eskalacije',
            'activityType' => 'Tip aktivnosti',
            'reminderText' => 'Tekst podsećanja',
            'reminderType' => 'Tip podsećanja',
            'taxCalendar' => 'Poreski kalendar',
            'regularReminders' => 'Obična podsećanja',
            'loadTaxCalendarPage' => 'Učitaj stranicu poreskog kalendara',
            'editReminder' => 'Izmeni podsećanje',

            // OCR
            'ocrSummary' => 'Резюме',
            'ocrCategory' => 'Категорија',
            'ocr' => 'OCR текст',
            'notRecognized'=>'Nije prepoznato',

            // Other
            'addComment' => 'Dodaj komentar',
            'companyInformation' => 'Informacije o kompaniji',
            'activity' => 'Aktivnost',
            'viewDocument' => 'Pregled dokumenta',
            'open' => 'Otvori',
            'documentInformation' => 'Informacije o dokumentu',
            'fileSize' => 'Veličina fajla',
            'company' => 'Kompanija',
            'currentStatus' => 'Trenutni status',
            'by_unknown' => 'Nepoznato',
            'unknown' => 'Nepoznato',
            'information' => 'Informacija',
            'noItemsToShow' => 'Nema stavki za prikaz',
            'yourRequestHasBeenSent' => 'Vaš zahtev je poslat. Molimo vas da proverite ponovo za nekoliko minuta.',
            'loadMonth' => 'Učitaj mesec',

        ],
    ];

    /**
     *
     * @return string
     */
    public static function getWord($key, $lang): string
    {
        return self::$dictionary[$lang][$key] ?? $key;
    }
}
