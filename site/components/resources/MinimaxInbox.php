<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxInbox — входящие документы организации (входна пошта).
 *
 * Контекст:
 *   Inbox — это очередь входящих документов (счета от поставщиков, банковские
 *   выписки, прочие документы), поступающих через электронные каналы (SEF, email)
 *   или загружаемых вручную. Документы из Inbox проходят процесс одобрения,
 *   после чего могут быть преобразованы в ReceivedInvoice.
 *
 *   Типичный workflow:
 *   1. Документ попадает в Inbox (автоматически из SEF или вручную через addFile())
 *   2. Бухгалтер проверяет документ → approve() или reject()
 *   3. При approve() с параметром createReceivedInvoice=true — автоматически
 *      создаётся ReceivedInvoice
 *
 * Эндпоинты:
 *   GET    api/orgs/{orgId}/inbox                                         — список
 *   GET    api/orgs/{orgId}/inbox/{id}                                    — по ID
 *   POST   api/orgs/{orgId}/inbox                                         — создать элемент
 *   POST   api/orgs/{orgId}/inbox/{id}                                    — добавить вложения (!)
 *   PUT    api/orgs/{orgId}/inbox/{id}/actions/{actionName}               — custom action
 *   DELETE api/orgs/{orgId}/inbox/{id}                                    — удалить элемент
 *   DELETE api/orgs/{orgId}/inbox/{id}/attachments/{attachmentId}         — удалить вложение
 *
 * Структура объекта Inbox:
 * {
 *   "InboxId":                  1,
 *   "Customer":                 { "ID": 456, "Name": "Firma d.o.o.", ... },
 *   "Employee":                 null,
 *   "InboxDate":                "2024-06-01T00:00:00",    // readonly
 *   "DateApproved":             null,                     // readonly, только для RS
 *   "InboxType":                "PR",                     // тип, см. константы TYPE_*
 *   "Description":              "Faktura br. 2024/001",
 *   "StatusOfReceivedInvoice":  null,                     // readonly, см. константы STATUS_*
 *   "BookkeepingAllowed":       "N",                      // readonly
 *   "EProvider":                "SEF",                    // readonly, провайдер
 *   "Attachments": [
 *     {
 *       "InboxAttachmentId":   10,
 *       "Inbox":               { "ID": 1, ... },
 *       "AttachmentData":      "JVBERi0...",              // base64 (в ответе GetInboxItem)
 *       "AttachmentDate":      "2024-06-01T00:00:00",    // readonly
 *       "AttachmentFileName":  "faktura.pdf",
 *       "AttachmentMimeType":  "application/pdf",
 *       "RowVersion":          "AAA...="
 *     }
 *   ],
 *   "RecordDtModified":         "2024-06-01T00:00:00",
 *   "RowVersion":               "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $inbox = Yii::$app->minimax->inbox($orgId);
 *
 * // Список необработанных входящих счетов
 * $pending = $inbox->listPending();
 *
 * // Одобрить документ и сразу создать ReceivedInvoice
 * $inbox->approve(1, createReceivedInvoice: true, reason: 'Sve ok');
 *
 * // Отклонить с причиной
 * $inbox->reject(1, reason: 'Pogrešan iznos');
 *
 * // Загрузить PDF вручную в Inbox
 * $item = $inbox->create([
 *     'InboxType'   => MinimaxInbox::TYPE_RECEIVED_INVOICE,
 *     'Description' => 'Faktura od dobavljača',
 *     'Customer'    => ['ID' => 456],
 * ]);
 * $inbox->addFile($item['InboxId'], '/path/to/faktura.pdf');
 * ```
 */
class MinimaxInbox extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы InboxType
    // -----------------------------------------------------------------

    /** Входящий счёт (primljena faktura) */
    public const TYPE_RECEIVED_INVOICE = 'PR';
    /** Банковская выписка (izvod po platnom prometu) */
    public const TYPE_BANK_STATEMENT   = 'IZP';
    /** Прочий тип */
    public const TYPE_OTHER            = 'Ostalo';
    /** Неизвестный тип */
    public const TYPE_UNKNOWN          = 'Neznano';

    // -----------------------------------------------------------------
    // Константы StatusOfReceivedInvoice (readonly, сербские значения)
    // -----------------------------------------------------------------

    /** Отклонён */
    public const STATUS_REJECTED  = 'Zavrnjen';
    /** Аннулирован */
    public const STATUS_REVOKED   = 'Storniran';
    /** Одобрен */
    public const STATUS_APPROVED  = 'Odobren';
    /** Отменён */
    public const STATUS_CANCELED  = 'Preklican';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'inbox';
    }

    // -----------------------------------------------------------------
    // Стандартные методы
    // -----------------------------------------------------------------

    /**
     * Список элементов Inbox с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - CustomerId   : ID клиента (поставщика)
     *   - InboxType    : тип элемента (TYPE_*)
     *   - DateFrom     : дата от
     *   - DateTo       : дата до
     *   - CurrentPage  : номер страницы (с 1)
     *   - PageSize     : размер страницы
     *   - SortField    : поле сортировки
     *   - Order        : A / D
     *
     * @param  array $params
     * @return array SearchResult { Rows, TotalRows, CurrentPageNumber, PageSize }
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 50], $params);

        return $this->getClient()->get($this->buildPath(), $params);
    }

    // -----------------------------------------------------------------
    // Custom actions
    // -----------------------------------------------------------------

    /**
     * Одобрить элемент Inbox.
     *
     * PUT api/orgs/{orgId}/inbox/{id}/actions/approve
     *
     * @param  int|string $id
     * @param  bool       $createReceivedInvoice  Автоматически создать ReceivedInvoice
     * @param  string     $reason                 Причина одобрения (approvalReason)
     * @return array      Обновлённый элемент
     * @throws MinimaxApiException
     */
    public function approve(
        int|string $id,
        bool       $createReceivedInvoice = false,
        string     $reason = '',
    ): array {
        $data = [];

        if ($reason !== '') {
            $data['approvalReason'] = $reason;
        }

        if ($createReceivedInvoice) {
            $data['createReceivedInvoice'] = true;
        }

        return $this->getClient()->put(
            $this->buildPath($id, 'actions/approve'),
            $data
        );
    }

    /**
     * Отклонить элемент Inbox.
     *
     * PUT api/orgs/{orgId}/inbox/{id}/actions/reject
     *
     * @param  int|string $id
     * @param  string     $reason  Причина отклонения (rejectionReason)
     * @return array      Обновлённый элемент
     * @throws MinimaxApiException
     */
    public function reject(int|string $id, string $reason = ''): array
    {
        $data = [];

        if ($reason !== '') {
            $data['rejectionReason'] = $reason;
        }

        return $this->getClient()->put(
            $this->buildPath($id, 'actions/reject'),
            $data
        );
    }

    // -----------------------------------------------------------------
    // Управление вложениями
    // -----------------------------------------------------------------

    /**
     * Добавить вложения к элементу Inbox.
     *
     * ВАЖНО: эндпоинт нестандартный — POST на inbox/{id}, не на inbox/{id}/attachments.
     *
     * POST api/orgs/{orgId}/inbox/{id}
     *
     * @param  int|string $id          InboxId
     * @param  array      $attachments Массив вложений: [{ AttachmentData, AttachmentFileName, AttachmentMimeType }]
     * @return array      Обновлённый элемент Inbox
     * @throws MinimaxApiException
     */
    public function addAttachments(int|string $id, array $attachments): array
    {
        return $this->getClient()->post(
            $this->buildPath($id),
            ['Attachments' => $attachments]
        );
    }

    /**
     * Удалить вложение из элемента Inbox.
     *
     * DELETE api/orgs/{orgId}/inbox/{id}/attachments/{attachmentId}
     *
     * @throws MinimaxApiException
     */
    public function deleteAttachment(int|string $id, int|string $attachmentId): array
    {
        return $this->getClient()->delete(
            $this->buildPath($id, "attachments/{$attachmentId}")
        );
    }

    /**
     * Загрузить файл с диска и добавить как вложение к элементу Inbox.
     *
     * @param  int|string $id        InboxId
     * @param  string     $filePath  Путь к файлу
     * @return array      Обновлённый элемент Inbox
     * @throws \RuntimeException если файл не найден
     * @throws MinimaxApiException
     */
    public function addFile(int|string $id, string $filePath): array
    {
        if (!is_readable($filePath)) {
            throw new \RuntimeException(
                "MinimaxInbox: файл не найден или недоступен: {$filePath}"
            );
        }

        $content  = file_get_contents($filePath);
        $fileName = basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return $this->addAttachments($id, [[
            'AttachmentData'     => base64_encode($content),
            'AttachmentFileName' => $fileName,
            'AttachmentMimeType' => $mimeType,
        ]]);
    }

    // -----------------------------------------------------------------
    // Хелперы для фильтрации
    // -----------------------------------------------------------------

    /**
     * Список необработанных входящих счетов (без статуса одобрения/отклонения).
     *
     * @throws MinimaxApiException
     */
    public function listPending(int $pageSize = 50): array
    {
        $result = $this->list([
            'InboxType' => self::TYPE_RECEIVED_INVOICE,
            'PageSize'  => $pageSize,
        ]);

        $filtered = array_filter(
            $result['Rows'],
            fn(array $row): bool => empty($row['StatusOfReceivedInvoice'])
        );

        return array_values($filtered);
    }

    /**
     * Список одобренных элементов Inbox.
     *
     * @throws MinimaxApiException
     */
    public function listApproved(int $pageSize = 50): array
    {
        $result   = $this->list(['PageSize' => $pageSize]);
        $filtered = array_filter(
            $result['Rows'],
            fn(array $row): bool => ($row['StatusOfReceivedInvoice'] ?? '') === self::STATUS_APPROVED
        );

        return array_values($filtered);
    }

    // -----------------------------------------------------------------
    // update() не поддерживается — Inbox обновляется только через actions
    // -----------------------------------------------------------------

    public function update(int|string $id, array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxInbox: прямое обновление недоступно. ' .
                'Используйте approve() или reject().'
        );
    }
}
