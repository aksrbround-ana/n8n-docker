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
            'find' =>  'Найти',
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
            'choose' => 'Выбрать',

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
            'companyNameTg' =>  'Название компании в Telegram',
            'companyType' => 'Тип компании',
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
            'responsibleAccountant' => 'Ответственный бухгалтер',
            'sorting' => 'Сортировка',
            'byName' => 'По названию',
            'byOverdue' => 'По просроченным задачам',
            'byOpenTasks' => 'По открытым задачам',
            'companyEditing' => 'Редактирование компании',

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
            'taskCategory' => 'Категория задачи',
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
            'taskSearch' => 'Поиск задачи…',
            'taskEditing' => 'Редактирование задачи',
            'taskCreation' => 'Создание задачи',

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
            'firstReminder' => 'Первое напоминание',
            'secondReminder' => 'Второе напоминание',
            'escalation' => 'Эскалация',
            'pending' => 'В ожидании',
            'stopForThisMonth' => 'Остановить в этом месяце',
            'stop' => 'Остановить',
            'notAssigned' => 'Не назначено',
            'stopped' => 'Остановлено',
            'activityType' => 'Тип активности',
            'reminderText' => 'Текст напоминания',
            'reminderType' => 'Тип напоминания',
            'taxCalendar' => 'Налоговый календарь',
            'regularReminders' => 'Ежемесячные напоминания',
            'thisMonthReminders' => 'Напоминания этого месяца',
            'loadTaxCalendarPage' => 'Загрузить страницу налогового календаря',
            'editReminder' => 'Редактировать напоминание',
            'deadline_day' => 'Крайний срок',
            'createReminder' => 'Создать регулярное напоминание',
            'topic' => 'Тема',
            'text' => 'Текст',
            'type_ru' => 'Тема (RU)',
            'type_rs' => 'Тема (RS)',
            'text_ru' => 'Текст (RU)',
            'text_rs' => 'Текст (RS)',

            // OCR
            'ocrSummary' => 'Резюме',
            'ocrCategory' => 'Категория',
            'ocr' => 'OCR текст',
            'notRecognized' => 'Не распознано',

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
            'back' => 'Назад',
            'error' => 'Ошибка',

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
            'find' => 'Пронађи',
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
            'choose' => 'Izabrati',

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
            'companyNameTg' => 'Naziv kompanije u Telegramu',
            'companyType' => 'Vrsta kompanije',
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
            'responsibleAccountant' => 'Odgovorni računovođa',
            'sorting' => 'Sortiranje',
            'byName' => 'Po naslovu',
            'byOverdue' => 'Po zakasnelim zadacima',
            'byOpenTasks' => 'Po otvorenim zadacima',
            'companyEditing' => 'Уређивање компаније',

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
            'taskCategory' => 'Категорија задатка',
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
            'taskSearch' => 'Потражите задатак…',
            'taskEditing' => 'Уређивање задатка',
            'taskCreation' => 'Креирање задатка',

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
            'firstReminder' => 'Prvo podsećanje',
            'secondReminder' => 'Drugo podsećanje',
            'escalation' => 'Eskalacija',
            'pending' => 'Na čekanju',
            'stopForThisMonth' => 'Zaustavi za ovaj mesec',
            'stop' => 'Zaustavi',
            'notAssigned' => 'Nije dodeljeno',
            'stopped' => 'Zaustavljeno',
            'activityType' => 'Tip aktivnosti',
            'reminderText' => 'Tekst podsećanja',
            'reminderType' => 'Tip podsećanja',
            'taxCalendar' => 'Poreski kalendar',
            'regularReminders' => 'Svakomesećna podsećanja',
            'thisMonthReminders' => 'Podsećanja za ovaj mesec',
            'loadTaxCalendarPage' => 'Učitaj stranicu poreskog kalendara',
            'editReminder' => 'Izmeni podsećanje',
            'deadline_day' => 'Крайњи рок',
            'createReminder' => 'Kreiraj stalno podsećanje',
            'topic' => 'Tema',
            'text' => 'Tekst',
            'type_ru' => 'Тема (RU)',
            'type_rs' => 'Тема (RS)',
            'text_ru' => 'Tekst (RU)',
            'text_rs' => 'Tekst (RS)',

            // OCR
            'ocrSummary' => 'Резюме',
            'ocrCategory' => 'Категорија',
            'ocr' => 'OCR текст',
            'notRecognized' => 'Nije prepoznato',

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
            'back' => 'Nazad',
            'error' => 'Greška',

        ],
        'en' =>   [
            // Months
            'january' => 'January',
            'february' => 'February',
            'march' => 'March',
            'april' => 'April',
            'may' => 'May',
            'june' => 'June',
            'july' => 'July',
            'august' => 'August',
            'september' => 'September',
            'october' => 'October',
            'november' => 'November',
            'december' => 'December',

            // Navigation
            'dashboard' =>  'Dashboard',
            'companies' =>  'Companies',
            'tasks' =>  'Tasks',
            'documents' =>  'Documents',
            'reports' =>  'Reports',
            'settings' =>  'Settings',
            'reminders' => 'Reminders',
            'users' =>  'Users',

            // Roles
            'ceo' =>  'CEO',
            'admin' =>  'Administrator',
            'accountant' =>  'Accountant',

            // Auth
            'login' =>  'Login',
            'password' =>  'Password',
            'oldPassword' =>  'Old Password',
            'newPassword' =>  'New Password',
            'confirmPassword' =>  'Confirm Password',
            'rememberMe' =>  'Remember Me',
            'forgotPassword' =>  'Forgot Password?',
            'resetPassword' =>  'Reset Password',
            'resetPasswordInstructions' =>  'Enter your email address to reset your password.',

            // Common
            'search' =>  'Search',
            'find' => 'Find',
            'searchPlaceholder' =>  'Search by company, PIB, task...',
            'logout' =>  'Logout',
            'profile' =>  'Profile',
            'save' =>  'Save',
            'cancel' =>  'Cancel',
            'delete' =>  'Delete',
            'edit' =>  'Edit',
            'add' =>  'Add',
            'create' =>  'Create',
            'close' =>  'Close',
            'finish' =>  'Finish',
            'filter' =>  'Filter',
            'filters' =>  'Filters',
            'clearFilters' =>  'Clear Filters',
            'apply' =>  'Apply',
            'actions' =>  'Actions',
            'view' =>  'View',
            'upload' =>  'Upload',
            'download' =>  'Download',
            'comment' =>  'Comment',
            'comments' =>  'Comments',
            'noData' =>  'No data',
            'loading' =>  'Loading...',
            'all' =>  'All',
            'choose' => 'Choose',

            // Dashboard
            'welcomeBack' =>  'Welcome Back',
            'overview' =>  'Overview',
            'totalClients' =>  'Total Clients',
            'activeTasks' =>  'Active Tasks',
            'overdueTasks' =>  'Overdue Tasks',
            'pendingDocuments' =>  'Pending Documents',
            'upcomingDeadlines' =>  'Upcoming Deadlines',
            'tasksByAccountant' =>  'Tasks by Accountant',
            'recentActivity' =>  'Recent Activity',
            'workloadDistribution' =>  'Workload Distribution',

            // Companies
            'companyName' =>  'Company Name',
            'companyNameTg' => 'Telegram Company Name',
            'companyType' => 'Type of company',
            'pib' =>  'PIB',
            'city' =>  'City',
            'sector' =>  'Sector',
            'assignedAccountant' =>  'Assigned Accountant',
            'status' =>  'Status',
            'openTasks' =>  'Open Tasks',
            'overdueCount' =>  'Overdue',
            'openProfile' =>  'Open Profile',
            'companyProfile' =>  'Company Profile',
            'contactPerson' =>  'Contact Person',
            'email' =>  'Email',
            'phone' =>  'Phone',
            'notes' =>  'Notes',
            'contacts' =>  'Contacts',
            'chat' => 'Chat',
            'addCompany' =>  'Add Company',
            'responsibleAccountant' => 'Responsible Accountant',
            'sorting' => 'Sorting',
            'byName' => 'By name',
            'byOverdue' => 'By overdue',
            'byOpenTasks' => 'By open tasks',
            'companyEditing' => 'Company editing',

            // Company statuses
            'statusActive' =>  'Active',
            'statusOnboarding' =>  'Onboarding',
            'statusPaused' =>  'Paused',
            'statusInactive' =>  'Inactive',

            // Tasks
            'taskId' =>  'ID',
            'taskType' =>  'Task Type',
            'period' =>  'Period',
            'priority' =>  'Priority',
            'dueDate' =>  'Due Date',
            'assignedTo' =>  'Assigned To',
            'lastUpdate' =>  'Last Update',
            'taskCategory' => 'Task category',
            'taskDetails' =>  'Task Details',
            'description' =>  'Description',
            'checklist' =>  'Checklist',
            'linkedDocuments' =>  'Linked Documents',
            'activityLog' =>  'Activity Log',
            'addTask' =>  'Add Task',
            'bulkActions' =>  'Bulk Actions',
            'changeStatus' =>  'Change Status',
            'assignAccountant' =>  'Assign Accountant',
            'selectedTasks' =>  'Selected Tasks',
            'taskSearch' => 'Task search…',
            'taskEditing' => 'Task editing',
            'taskCreation' => 'Task creation',

            // Task statuses
            'taskStatusNew' =>  'New',
            'taskStatusInProgress' =>  'In Progress',
            'taskStatusWaiting' =>  'Waiting for Documents',
            'taskStatusDone' =>  'Done',
            'taskStatusOverdue' =>  'Overdue',
            'taskStatusClosed' =>  'Closed',
            'taskStatusArchived' =>  'Archived',

            // Task activity steps
            'taskStepCreated' =>  'created',
            'taskStepAssigned' =>  'assigned',
            'taskStepInProgress' =>  'in progress',
            'taskStepWaiting' =>  'waiting for documents',
            'taskStepOverdue' =>  'overdue',
            'taskStepDone' =>  'done',
            'taskStepClosed' =>  'closed',
            'taskStepArchived' =>  'archived',
            'taskStepPriorityChanged' =>  'priority changed',
            'taskStepDueDateChanged' =>  'due date changed',

            // Task types
            'taskTypeVat' =>  'VAT Declaration',
            'taskTypePayroll' =>  'Payroll',
            'taskTypeAnnualReport' =>  'Annual Report',
            'taskTypeReconciliation' =>  'Reconciliation',
            'taskTypeOther' =>  'Other',

            // Priorities
            'priorityLow' =>  'Low',
            'priorityNormal' =>  'Normal',
            'priorityHigh' =>  'High',

            // Documents
            'fileName' =>  'File Name',
            'documentType' =>  'Document Type',
            'uploadedBy' =>  'Uploaded By',
            'uploadDate' =>  'Upload Date',
            'documentStatus' =>  'Status',
            'uploadDocuments' =>  'Upload Documents',
            'dragDropHint' =>  'Drag and drop files here or click to select',
            'selectCompany' =>  'Select Company',
            'selectPeriod' =>  'Select Period',
            'selectDocType' =>  'Select Document Type',
            // Document types
            'docTypeUnknown' =>  'Unknown',
            'docTypeInvoice' =>  'Invoice',
            'docTypeBankStatement' =>  'Bank Statement',
            'docTypePayroll' =>  'Payroll',
            'docTypeContract' =>  'Contract',
            'docTypeTaxReturn' =>  'Tax Return',
            'docTypeOther' =>  'Other',

            // Document statuses
            'docStatusUploaded' =>  'Uploaded',
            'docStatusChecked' =>  'Checked',
            'docStatusNeedsRevision' =>  'Needs Revision',
            'docStatusProcessing' => 'Processing',

            // Notes
            'addNote' => 'Add Note',
            // User management
            'userManagement' =>  'User Management',
            'createUser' =>  'Create User',
            'userName' =>  'Name',
            'userEmail' =>  'Email',
            'userRole' =>  'Role',
            'userStatus' =>  'Status',
            'userActive' =>  'Active',
            'userBlocked' =>  'Blocked',
            'blockUser' =>  'Block User',
            'unblockUser' =>  'Unblock User',

            // Settings
            'systemSettings' =>  'System Settings',
            'systemSettingsManaging' =>  'System Settings Managing',
            'taxPeriods' =>  'Tax Periods',
            'defaultDeadlines' =>  'Default Deadlines',
            'defaultDeadlinesForVariousTasks' =>  'Default Deadlines for Various Tasks',
            'notificationTemplates' =>  'Notification Templates',
            'notificationTemplatesForTelegram' => 'Notification Templates for Telegram',
            'set' => 'set',

            // Time
            'today' =>  'Today',
            'yesterday' =>  'Yesterday',
            'thisWeek' =>  'This Week',
            'thisMonth' =>  'This Month',
            'overdue' =>  'Overdue',
            'daysLeft' =>  'days left',
            'daysOverdue' =>  'days overdue',

            // Reminders
            'actionDate' => 'Date of Action',
            'reminderDate' => 'Reminder Date',
            'escalationDate' => 'Escalation Date',
            'firstReminder' => 'First Reminder',
            'secondReminder' => 'Second Reminder',
            'escalation' => 'Escalation',
            'pending' => 'Pending',
            'stopForThisMonth' => 'Stop for this month',
            'stop' => 'Stop',
            'notAssigned' => 'Not Assigned',
            'stopped' => 'Stopped',
            'activityType' => 'Activity Type',
            'reminderText' => 'Reminder Text',
            'reminderType' => 'Reminder Type',
            'taxCalendar' => 'Tax Calendar',
            'regularReminders' => 'Monthly Reminders',
            'thisMonthReminders' => 'This Month\'s Reminders',
            'loadTaxCalendarPage' => 'Load Tax Calendar Page',
            'editReminder' => 'Edit Reminder',
            'deadline_day' => 'Deadline Day',
            'createReminder' => 'Create Regular Reminder',
            'topic' => 'Topic',
            'text' => 'Text',
            'type_ru' => 'Topic (RU)',
            'type_rs' => 'Topic (RS)',
            'text_ru' => 'Text (RU)',
            'text_rs' => 'Text (RS)',

            // OCR
            'ocrSummary' => 'Summary',
            'ocrCategory' => 'Category',
            'ocr' => 'OCR text',
            'notRecognized' => 'Not recognized',

            // Other
            'addComment' => 'Add Comment',
            'companyInformation' => 'Company Information',
            'activity' => 'Activity',
            'viewDocument' => 'View Document',
            'open' => 'Open',
            'documentInformation' => 'Document Information',
            'fileSize' => 'File Size',
            'company' => 'Company',
            'currentStatus' => 'Current Status',
            'by_unknown' => 'Unknown',
            'unknown' => 'Unknown',
            'information' => 'Information',
            'noItemsToShow' => 'No items to show',
            'yourRequestHasBeenSent' => 'Your request has been sent. Please check in a couple of minutes.',
            'loadMonth' => 'Load Month',
            'back' => 'Back',
            'error' => 'Error',
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

    public static function getDictionaryJsFile($sort = false): string
    {
        $file = [];
        $file[] = 'const translations = {';
        foreach (self::$dictionary as $lang => $dictionary) {
            $file[] = "    {$lang}: {";
            if ($sort) {
                ksort($dictionary);
            }
            foreach ($dictionary as $key => $value) {
                $escaped = addcslashes($value, "'");
                $file[] = "        {$key}: '{$escaped}',";
            }
            $file[] = '    },';
        }
        $file[] = '};';
        $file[] = 'function dictionaryLookup(word, lang) {';
        $file[] = '    return translations[lang][word] || word;';
        $file[] = '};';
        return implode("\n", $file);
    }
}
