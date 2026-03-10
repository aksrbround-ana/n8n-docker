<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxDocument — общие документы организации с вложениями.
 *
 * Контекст:
 *   Document — универсальный контейнер для хранения произвольных документов
 *   (договоры, акты, сканы, справки и т.д.) привязанных к клиенту или сотруднику.
 *   Каждый документ может содержать несколько вложений (файлов).
 *   Вложения хранятся как base64-закодированные данные.
 *
 * Эндпоинты — документы:
 *   GET    api/orgs/{orgId}/documents                                    — список
 *   GET    api/orgs/{orgId}/documents/{id}                               — по ID
 *   GET    api/orgs/{orgId}/documents/synccandidates                     — для синхронизации
 *   POST   api/orgs/{orgId}/documents                                    — создать
 *   PUT    api/orgs/{orgId}/documents/{id}                               — обновить
 *   DELETE api/orgs/{orgId}/documents/{id}                               — удалить
 *
 * Эндпоинты — вложения:
 *   GET    api/orgs/{orgId}/documents/{id}/attachments/{attachId}        — получить вложение
 *   POST   api/orgs/{orgId}/documents/{id}/attachments                   — добавить вложение
 *   PUT    api/orgs/{orgId}/documents/{id}/attachments/{attachId}        — обновить вложение
 *   DELETE api/orgs/{orgId}/documents/{id}/attachments/{attachId}        — удалить вложение
 *
 * Структура объекта Document:
 * {
 *   "DocumentId":       1,
 *   "DocumentDate":     "2024-06-01T00:00:00",
 *   "Customer":         { "ID": 456, "Name": "Firma d.o.o.", "ResourceUrl": "..." },
 *   "Employee":         null,
 *   "Description":      "Ugovor o saradnji 2024",
 *   "Attachments": [
 *     {
 *       "DocumentAttachmentId": 10,
 *       "Description":          "Potpisan ugovor",
 *       "FileName":             "ugovor_2024.pdf",
 *       "MimeType":             "application/pdf",
 *       "EntryDate":            "2024-06-01T00:00:00"
 *       // AttachmentData отсутствует в списке — только при GetDocumentAttachment
 *     }
 *   ],
 *   "RecordDtModified": "2024-06-01T00:00:00",
 *   "RowVersion":       "AAA...="
 * }
 *
 * Структура объекта DocumentAttachment (полная, при GET):
 * {
 *   "DocumentAttachmentId": 10,
 *   "Document":             { "ID": 1, "Name": "Ugovor...", "ResourceUrl": "..." },
 *   "Description":          "Potpisan ugovor",
 *   "AttachmentData":       "JVBERi0xLjQ...",  // base64
 *   "FileName":             "ugovor_2024.pdf",
 *   "MimeType":             "application/pdf",
 *   "EntryDate":            "2024-06-01T00:00:00",
 *   "RecordDtModified":     "2024-06-01T00:00:00",
 *   "RowVersion":           "AAA...="
 * }
 *
 * Использование:
 * ```php
 * $docs = Yii::$app->minimax->document($orgId);
 *
 * // Создать документ
 * $doc = $docs->create([
 *     'DocumentDate' => '2024-06-01T00:00:00',
 *     'Customer'     => ['ID' => 456],
 *     'Description'  => 'Ugovor o saradnji 2024',
 * ]);
 *
 * // Прикрепить файл из пути
 * $docs->attachFile($doc['DocumentId'], '/path/to/ugovor.pdf', 'Potpisan ugovor');
 *
 * // Получить вложение и сохранить на диск
 * $docs->saveAttachment($doc['DocumentId'], 10, '/tmp/ugovor.pdf');
 *
 * // Список документов клиента
 * $list = $docs->list(['CustomerId' => 456]);
 * ```
 */
class MinimaxDocument extends MinimaxResource
{
    protected function getResourceName(): string
    {
        return 'documents';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список документов с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - CustomerId    : ID клиента
     *   - EmployeeId    : ID сотрудника
     *   - DateFrom      : дата документа от
     *   - DateTo        : дата документа до
     *   - SearchString  : поиск по описанию
     *   - CurrentPage   : номер страницы (с 1)
     *   - PageSize      : размер страницы
     *   - SortField     : поле сортировки
     *   - Order         : A / D
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
    // Методы для работы с вложениями
    // -----------------------------------------------------------------

    /**
     * Получить вложение с полными данными (включая base64 AttachmentData).
     *
     * GET api/orgs/{orgId}/documents/{documentId}/attachments/{attachmentId}
     *
     * @throws MinimaxApiException
     */
    public function getAttachment(int|string $documentId, int|string $attachmentId): array
    {
        return $this->getClient()->get(
            $this->buildPath($documentId, "attachments/{$attachmentId}")
        );
    }

    /**
     * Добавить вложение к документу.
     *
     * POST api/orgs/{orgId}/documents/{documentId}/attachments
     *
     * @param  int|string $documentId
     * @param  array      $data  Поля: Description, AttachmentData (base64), FileName, MimeType
     * @return array      Созданное вложение
     * @throws MinimaxApiException
     */
    public function addAttachment(int|string $documentId, array $data): array
    {
        return $this->getClient()->post(
            $this->buildPath($documentId, 'attachments'),
            $data
        );
    }

    /**
     * Обновить вложение.
     *
     * PUT api/orgs/{orgId}/documents/{documentId}/attachments/{attachmentId}
     *
     * @throws MinimaxApiException
     */
    public function updateAttachment(
        int|string $documentId,
        int|string $attachmentId,
        array      $data,
    ): array {
        return $this->getClient()->put(
            $this->buildPath($documentId, "attachments/{$attachmentId}"),
            $data
        );
    }

    /**
     * Удалить вложение.
     *
     * DELETE api/orgs/{orgId}/documents/{documentId}/attachments/{attachmentId}
     *
     * @throws MinimaxApiException
     */
    public function deleteAttachment(int|string $documentId, int|string $attachmentId): array
    {
        return $this->getClient()->delete(
            $this->buildPath($documentId, "attachments/{$attachmentId}")
        );
    }

    /**
     * Получить кандидатов для синхронизации.
     *
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    // -----------------------------------------------------------------
    // Хелперы для работы с файлами
    // -----------------------------------------------------------------

    /**
     * Прикрепить файл с диска к документу.
     * Автоматически кодирует файл в base64 и определяет MIME-тип.
     *
     * @param  int|string $documentId
     * @param  string     $filePath    Путь к файлу на диске
     * @param  string     $description Описание вложения
     * @return array      Созданное вложение
     * @throws \RuntimeException если файл не найден или не читается
     * @throws MinimaxApiException
     */
    public function attachFile(
        int|string $documentId,
        string     $filePath,
        string     $description = '',
    ): array {
        if (!is_readable($filePath)) {
            throw new \RuntimeException(
                "MinimaxDocument: файл не найден или недоступен для чтения: {$filePath}"
            );
        }

        $content  = file_get_contents($filePath);
        $fileName = basename($filePath);
        $mimeType = mime_content_type($filePath) ?: 'application/octet-stream';

        return $this->addAttachment($documentId, [
            'Description'    => $description ?: $fileName,
            'AttachmentData' => base64_encode($content),
            'FileName'       => $fileName,
            'MimeType'       => $mimeType,
        ]);
    }

    /**
     * Сохранить вложение документа на диск.
     * Декодирует base64 из AttachmentData и записывает файл.
     *
     * @param  int|string $documentId
     * @param  int|string $attachmentId
     * @param  string     $targetPath   Путь для сохранения
     * @return string                   Путь к сохранённому файлу
     * @throws \RuntimeException если не удалось сохранить
     * @throws MinimaxApiException
     */
    public function saveAttachment(
        int|string $documentId,
        int|string $attachmentId,
        string     $targetPath,
    ): string {
        $attachment = $this->getAttachment($documentId, $attachmentId);
        $content    = base64_decode($attachment['AttachmentData'] ?? '');

        if ($content === false || $content === '') {
            throw new \RuntimeException(
                "MinimaxDocument: не удалось декодировать вложение {$attachmentId}."
            );
        }

        if (file_put_contents($targetPath, $content) === false) {
            throw new \RuntimeException(
                "MinimaxDocument: не удалось записать файл в {$targetPath}."
            );
        }

        return $targetPath;
    }
}
