<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxAnalytic — аналитические признаки (шифранти аналитике) организации.
 *
 * Контекст:
 *   Analytic — это дополнительное измерение для бухгалтерских проводок,
 *   позволяющее группировать операции по проектам, отделам, направлениям.
 *   Например: "Projekat A", "Sektor prodaje", "Filijala Beograd".
 *   Используется в JournalEntry.Analytic, IssuedInvoice.Analytic,
 *   ReceivedInvoice.Analytic.
 *
 *   Аналитики могут быть иерархическими: у каждой может быть ParentAnalytic.
 *   Например:
 *     Srbija (родитель)
 *       └─ Beograd (дочерний)
 *       └─ Novi Sad (дочерний)
 *
 * Эндпоинты:
 *   GET    api/orgs/{organisationId}/analytics                        — список
 *   GET    api/orgs/{organisationId}/analytics/{id}                   — по ID
 *   GET    api/orgs/{organisationId}/analytics/synccandidates         — для синхронизации
 *   POST   api/orgs/{organisationId}/analytics                        — создать
 *   PUT    api/orgs/{organisationId}/analytics/{id}                   — обновить
 *   DELETE api/orgs/{organisationId}/analytics/{id}                   — удалить
 *
 * Структура объекта Analytic:
 * {
 *   "AnalyticId":       1,
 *   "Code":             "PROJ-A",
 *   "Name":             "Projekat A",
 *   "UsageEndDate":     null,           // null = активен, иначе дата окончания
 *   "ParentAnalytic":   null,           // { "ID": 5, "Name": "Srbija", ... }
 *   "RecordDtModified": "2024-01-01T00:00:00",
 *   "RowVersion":       "AAA...="
 * }
 *
 * UsageEndDate:
 *   null — аналитик активен (можно использовать в проводках)
 *   дата — аналитик деактивирован после этой даты
 *
 * Использование:
 * ```php
 * $analytics = Yii::$app->minimax->analytic($orgId);
 *
 * // Список всех аналитиков
 * $list = $analytics->list();
 *
 * // Только активные (без даты окончания)
 * $active = $analytics->listActive();
 *
 * // Только корневые (без родителя)
 * $roots = $analytics->listRoots();
 *
 * // Дочерние аналитики конкретного родителя
 * $children = $analytics->listChildren(5);
 *
 * // Карта [AnalyticId => Name] для select-списков
 * $map = $analytics->getIdNameMap();
 *
 * // Создать новый аналитик
 * $new = $analytics->create([
 *     'Code'          => 'PROJ-B',
 *     'Name'          => 'Projekat B',
 *     'ParentAnalytic' => ['ID' => 5],  // опционально
 * ]);
 *
 * // Деактивировать аналитик (задать дату окончания)
 * $analytics->deactivate(1, '2024-12-31T00:00:00');
 *
 * // Кандидаты для синхронизации
 * $sync = $analytics->getSyncCandidates();
 * ```
 */
class MinimaxAnalytic extends MinimaxResource
{
    protected function getResourceName(): string
    {
        return 'analytics';
    }

    // -----------------------------------------------------------------
    // Стандартные методы с документированными параметрами фильтрации
    // -----------------------------------------------------------------

    /**
     * Список аналитиков с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - SearchString : поиск по коду или названию
     *   - CurrentPage  : номер страницы (с 1)
     *   - PageSize     : размер страницы
     *   - SortField    : поле сортировки ('Code', 'Name')
     *   - Order        : A / D
     *
     * @param  array $params
     * @return array SearchResult { Rows, TotalRows, CurrentPageNumber, PageSize }
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 100], $params);

        return $this->getClient()->get($this->buildPath(), $params);
    }

    // -----------------------------------------------------------------
    // Дополнительные методы
    // -----------------------------------------------------------------

    /**
     * Получить кандидатов для синхронизации.
     *
     * GET api/orgs/{organisationId}/analytics/synccandidates
     *
     * @throws MinimaxApiException
     */
    public function getSyncCandidates(array $params = []): array
    {
        return $this->getClient()->get($this->buildPath('synccandidates'), $params);
    }

    /**
     * Список только активных аналитиков (UsageEndDate = null или в будущем).
     * API не поддерживает прямой фильтрации, фильтруем на стороне клиента.
     *
     * @throws MinimaxApiException
     */
    public function listActive(int $pageSize = 200): array
    {
        $result = $this->list(['PageSize' => $pageSize]);
        $now    = new \DateTimeImmutable();

        $filtered = array_filter(
            $result['Rows'],
            function (array $row) use ($now): bool {
                if (empty($row['UsageEndDate'])) {
                    return true;
                }
                $endDate = new \DateTimeImmutable($row['UsageEndDate']);
                return $endDate >= $now;
            }
        );

        return array_values($filtered);
    }

    /**
     * Список корневых аналитиков (ParentAnalytic = null).
     * Удобно для построения дерева аналитик в UI.
     *
     * @throws MinimaxApiException
     */
    public function listRoots(int $pageSize = 200): array
    {
        $result   = $this->list(['PageSize' => $pageSize]);
        $filtered = array_filter(
            $result['Rows'],
            fn(array $row): bool => empty($row['ParentAnalytic']['ID'])
        );

        return array_values($filtered);
    }

    /**
     * Список дочерних аналитиков для заданного родителя.
     *
     * @param  int|string $parentId  AnalyticId родительского аналитика
     * @throws MinimaxApiException
     */
    public function listChildren(int|string $parentId, int $pageSize = 200): array
    {
        $result   = $this->list(['PageSize' => $pageSize]);
        $filtered = array_filter(
            $result['Rows'],
            fn(array $row): bool => ($row['ParentAnalytic']['ID'] ?? null) == $parentId
        );

        return array_values($filtered);
    }

    /**
     * Карта [AnalyticId => Name] для всех активных аналитиков.
     * Удобно для select-списков.
     *
     * @throws MinimaxApiException
     */
    public function getIdNameMap(int $pageSize = 200): array
    {
        $rows = $this->listActive($pageSize);
        $map  = [];

        foreach ($rows as $row) {
            $map[$row['AnalyticId']] = $row['Name'];
        }

        return $map;
    }

    /**
     * Деактивировать аналитик — задать дату окончания использования.
     * После деактивации аналитик нельзя использовать в новых проводках.
     *
     * Автоматически получает свежий RowVersion перед обновлением.
     *
     * @param  int|string $id
     * @param  string     $endDate  Формат: 'YYYY-MM-DDTHH:MM:SS'
     * @return array      Обновлённый аналитик
     * @throws MinimaxApiException
     */
    public function deactivate(int|string $id, string $endDate): array
    {
        $current = $this->get($id);

        return $this->update($id, array_merge($current, [
            'UsageEndDate' => $endDate,
        ]));
    }
}
