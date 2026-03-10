<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxOutbox — исходящие документы организации (излазна пошта).
 *
 * Контекст:
 *   Outbox — это очередь исходящих документов, автоматически формируемая
 *   Minimax при выставлении счетов (IR), создании накладных (DO) и расчёте
 *   зарплаты (OP). Документы из Outbox отправляются клиентам/сотрудникам
 *   по email или через электронные каналы.
 *
 *   В отличие от Inbox, Outbox доступен только для чтения — документы
 *   попадают сюда автоматически, управлять ими через API нельзя.
 *
 * Эндпоинты:
 *   GET api/orgs/{orgId}/outbox        — список
 *   GET api/orgs/{orgId}/outbox/{id}   — по ID
 *
 * Структура объекта Outbox:
 * {
 *   "OutboxId":    1,
 *   "Customer":    { "ID": 456, "Name": "Firma d.o.o.", "ResourceUrl": "..." },
 *   "Employee":    null,
 *   "OutboxDate":  "2024-06-01T00:00:00",    // readonly
 *   "OutboxType":  "IR",                     // тип, см. константы TYPE_*
 *   "Description": "Faktura 2024/001",
 *   "Attachments": [
 *     {
 *       "OutboxAttachmentId":  10,
 *       "Outbox":              { "ID": 1, ... },
 *       "AttachmentData":      "JVBERi0...",              // base64
 *       "AttachmentDate":      "2024-06-01T00:00:00",    // readonly
 *       "AttachmentFileName":  "faktura_2024_001.pdf",
 *       "AttachmentMimeType":  "application/pdf",
 *       "RowVersion":          "AAA...="
 *     }
 *   ],
 *   "RecordDtModified": "2024-06-01T00:00:00",
 *   "RowVersion":       "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $outbox = Yii::$app->minimax->outbox($orgId);
 *
 * // Список исходящих счетов за период
 * $invoices = $outbox->list([
 *     'OutboxType' => MinimaxOutbox::TYPE_INVOICE,
 *     'DateFrom'   => '2024-06-01',
 *     'DateTo'     => '2024-06-30',
 * ]);
 *
 * // Сохранить вложение первого элемента на диск
 * $item = $outbox->get(1);
 * $attachment = $item['Attachments'][0];
 * $outbox->saveAttachment($attachment, '/tmp/faktura.pdf');
 *
 * // Все исходящие для конкретного клиента
 * $list = $outbox->listByCustomer(456);
 * ```
 */
class MinimaxOutbox extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы OutboxType
    // -----------------------------------------------------------------

    /** Выставленные счета */
    public const TYPE_INVOICE       = 'IR';
    /** Накладные (otpremnica) */
    public const TYPE_DELIVERY_NOTE = 'DO';
    /** Зарплатные ведомости */
    public const TYPE_PAYROLL       = 'OP';

    // -----------------------------------------------------------------
    // Ресурс
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        return 'outbox';
    }

    // -----------------------------------------------------------------
    // Стандартные методы (только чтение)
    // -----------------------------------------------------------------

    /**
     * Список исходящих документов с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - OutboxType  : тип документа (TYPE_*)
     *   - CustomerId  : ID клиента
     *   - EmployeeId  : ID сотрудника
     *   - DateFrom    : дата от
     *   - DateTo      : дата до
     *   - CurrentPage : номер страницы (с 1)
     *   - PageSize    : размер страницы
     *   - SortField   : поле сортировки
     *   - Order       : A / D
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
    // Хелперы
    // -----------------------------------------------------------------

    /**
     * Список исходящих документов конкретного клиента.
     *
     * @throws MinimaxApiException
     */
    public function listByCustomer(
        int|string  $customerId,
        string|null $type     = null,
        int         $pageSize = 50,
    ): array {
        $params = ['CustomerId' => $customerId, 'PageSize' => $pageSize];

        if ($type !== null) {
            $params['OutboxType'] = $type;
        }

        return $this->list($params);
    }

    /**
     * Сохранить вложение Outbox на диск.
     * Декодирует AttachmentData (base64) и записывает файл.
     *
     * @param  array  $attachment  Элемент из $outboxItem['Attachments']
     * @param  string $targetPath  Путь для сохранения
     * @return string              Путь к сохранённому файлу
     * @throws \RuntimeException если не удалось декодировать или сохранить
     */
    public function saveAttachment(array $attachment, string $targetPath): string
    {
        $content = base64_decode($attachment['AttachmentData'] ?? '');

        if ($content === false || $content === '') {
            $id = $attachment['OutboxAttachmentId'] ?? '?';
            throw new \RuntimeException(
                "MinimaxOutbox: не удалось декодировать вложение {$id}."
            );
        }

        if (file_put_contents($targetPath, $content) === false) {
            throw new \RuntimeException(
                "MinimaxOutbox: не удалось записать файл в {$targetPath}."
            );
        }

        return $targetPath;
    }

    // -----------------------------------------------------------------
    // Заблокированные методы — Outbox только для чтения
    // -----------------------------------------------------------------

    public function create(array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxOutbox: исходящие документы формируются автоматически, создание недоступно.'
        );
    }

    public function update(int|string $id, array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxOutbox: исходящие документы доступны только для чтения.'
        );
    }

    public function delete(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxOutbox: исходящие документы доступны только для чтения.'
        );
    }
}
