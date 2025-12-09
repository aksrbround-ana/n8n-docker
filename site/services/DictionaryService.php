<?php

namespace app\services;

class DictionaryService
{
    static array $prioritySign = [
        'low' => '↓',
        'normal' => '●',
        'high' => '↑',
    ];
    static array $dictionary = [
        'ru' =>   [
            // Navigation
            'dashboard' =>  'Главная',
            'companies' =>  'Компании',
            'tasks' =>  'Задачи',
            'documents' =>  'Документы',
            'reports' =>  'Отчёты',
            'settings' =>  'Настройки',
            'users' =>  'Пользователи',

            // Roles
            'ceo' =>  'Руководитель',
            'admin' =>  'Администратор',
            'accountant' =>  'Бухгалтер',

            // Auth
            'login' =>  'Войти',
            'password' =>  'Пароль',

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
            'chat'=> 'Чат',
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
            'taxPeriods' =>  'Налоговые периоды',
            'defaultDeadlines' =>  'Сроки по умолчанию',
            'notificationTemplates' =>  'Шаблоны уведомлений',

            // Time
            'today' =>  'Сегодня',
            'yesterday' =>  'Вчера',
            'thisWeek' =>  'Эта неделя',
            'thisMonth' =>  'Этот месяц',
            'overdue' =>  'Просрочено',
            'daysLeft' =>  'дней осталось',
            'daysOverdue' =>  'дней просрочено',
        ],
        'rs' =>   [
            // Navigation
            'dashboard' =>  'Početna',
            'companies' =>  'Kompanije',
            'tasks' =>  'Zadaci',
            'documents' =>  'Dokumenti',
            'reports' =>  'Izveštaji',
            'settings' =>  'Podešavanja',
            'users' =>  'Korisnici',

            // Roles
            'ceo' =>  'Direktor',
            'admin' =>  'Administrator',
            'accountant' =>  'Knjigovođa',

            // Auth
            'login' =>  'Prijava',
            'password' =>  'Lozinka',

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
            'chat'=> 'Chat',
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
            'taxPeriods' =>  'Poreski periodi',
            'defaultDeadlines' =>  'Podrazumevani rokovi',
            'notificationTemplates' =>  'Šabloni obaveštenja',

            // Time
            'today' =>  'Danas',
            'yesterday' =>  'Juče',
            'thisWeek' =>  'Ova nedelja',
            'thisMonth' =>  'Ovaj mesec',
            'overdue' =>  'Zakasnelo',
            'daysLeft' =>  'dana preostalo',
            'daysOverdue' =>  'dana zakašnjenja',
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
