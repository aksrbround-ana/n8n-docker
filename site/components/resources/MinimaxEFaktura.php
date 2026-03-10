<?php

namespace app\components\resources;

use app\components\MinimaxHttpClient;
use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxEFaktura — справочник участников сербской системы электронных счетов (SEF).
 *
 * ВАЖНО — понимание архитектуры:
 * ─────────────────────────────────────────────────────────────────────
 * EFaktura в контексте Minimax API — это НЕ сервис отправки счетов.
 * Это справочник компаний, зарегистрированных в государственной системе
 * Sistem E-Faktura (www.efaktura.gov.rs).
 *
 * Фактическая отправка счетов в SEF управляется полем ForwardToSEF
 * в объекте IssuedInvoice:
 *   'ForwardToSEF' => 'Eracun'     — отправить как e-счёт
 *   'ForwardToSEF' => 'Zbirno'     — групповая отправка
 *   'ForwardToSEF' => 'Posamicno'  — индивидуальная отправка
 *   'ForwardToSEF' => null         — не отправлять
 *
 * Этот справочник используется для:
 *   1. Проверки, зарегистрирован ли контрагент в SEF
 *      (перед отправкой ему e-счёта)
 *   2. Получения корректного VatIdentificationNumber
 *      для заполнения полей счёта
 *   3. Определения, является ли клиент бюджетным пользователем
 *      (BudgetUserNumber → ForwardToCRF = 'D')
 * ─────────────────────────────────────────────────────────────────────
 *
 * Эндпоинты:
 *   GET api/efaktura/list   — список участников SEF (глобальный, без organisationId)
 *
 * Структура объекта EFakturaEntry:
 * {
 *   "RegistrationNumber":      "12345678",    // матični број (PIB)
 *   "BudgetUserNumber":        "",            // номер бюджетного пользователя (JBKJS)
 *                                            // заполнен только у госструктур
 *   "VatIdentificationNumber": "RS12345678",  // ПИБ с префиксом RS
 *   "Name":                    "Firma d.o.o." // название компании
 * }
 *
 * Особенности:
 *   - Эндпоинт глобальный: URL не содержит organisationId
 *   - Справочник доступен только на чтение (только GET)
 *   - Данные берутся из государственного реестра SEF
 *
 * Использование:
 * ```php
 * $ef = Yii::$app->minimax->eFaktura($orgId);
 *
 * // Проверить, есть ли контрагент в SEF по ИНН
 * $entry = $ef->findByTaxNumber('12345678');
 * if ($entry !== null) {
 *     // Контрагент зарегистрирован в SEF, можно отправить e-счёт
 * }
 *
 * // Поиск по названию
 * $list = $ef->list(['Name' => 'Firma']);
 *
 * // Проверка при создании счёта:
 * $isSefRegistered = $ef->isRegisteredInSef('12345678');
 * $isBudgetUser    = $ef->isBudgetUser('12345678');
 *
 * // Получить нужный ForwardToSEF для счёта
 * $forwardToSef = $ef->resolveForwardToSef('12345678');
 * // → 'Eracun' если зарегистрирован, null если нет
 * ```
 */
class MinimaxEFaktura extends MinimaxResource
{
    // -----------------------------------------------------------------
    // Константы ForwardToSEF (используются в IssuedInvoice)
    // -----------------------------------------------------------------

    /** Отправить как полноценный e-счёт через SEF */
    public const FORWARD_ERACUN    = 'Eracun';

    /** Групповая отправка данных в SEF */
    public const FORWARD_ZBIRNO    = 'Zbirno';

    /** Индивидуальная отправка данных в SEF */
    public const FORWARD_POSAMICNO = 'Posamicno';

    // -----------------------------------------------------------------
    // EFaktura использует глобальный URL без organisationId
    // -----------------------------------------------------------------

    protected function getResourceName(): string
    {
        // Не используется — URL строится вручную в каждом методе
        return 'efaktura';
    }

    // -----------------------------------------------------------------
    // Публичные методы
    // -----------------------------------------------------------------

    /**
     * Список участников SEF с фильтрацией.
     *
     * Поддерживаемые ключи $params:
     *   - RegistrationNumber      : поиск по матичном броj-у
     *   - BudgetUserNumber        : поиск по JBKJS (только бюджетные орг.)
     *   - VatIdentificationNumber : поиск по ПИБ (например: 'RS12345678')
     *   - Name                    : поиск по названию компании
     *   - CurrentPage             : номер страницы (с 1)
     *   - PageSize                : размер страницы
     *   - SortField               : поле сортировки
     *   - Order                   : A / D
     *
     * @param  array $params
     * @return array SearchResult { Rows, TotalRows, CurrentPageNumber, PageSize }
     * @throws MinimaxApiException
     */
    public function list(array $params = []): array
    {
        $params = array_merge(['CurrentPage' => 1, 'PageSize' => 50], $params);

        return $this->getClient()->get('api/efaktura/list', $params);
    }

    /**
     * Найти участника SEF по налоговому номеру (матични број / RegistrationNumber).
     * Возвращает первую найденную запись или null если не найдено.
     *
     * @throws MinimaxApiException
     */
    public function findByTaxNumber(string $taxNumber): ?array
    {
        $result = $this->list([
            'RegistrationNumber' => $taxNumber,
            'PageSize'           => 1,
        ]);

        return $result['Rows'][0] ?? null;
    }

    /**
     * Найти участника SEF по ПИБ (VatIdentificationNumber).
     * ПИБ обычно имеет вид 'RS12345678'.
     *
     * @throws MinimaxApiException
     */
    public function findByVatNumber(string $vatNumber): ?array
    {
        $result = $this->list([
            'VatIdentificationNumber' => $vatNumber,
            'PageSize'                => 1,
        ]);

        return $result['Rows'][0] ?? null;
    }

    /**
     * Проверить, зарегистрирован ли контрагент в SEF.
     * Используйте перед выставлением e-счёта.
     *
     * @param  string $taxNumber  Матични број контрагента
     * @throws MinimaxApiException
     */
    public function isRegisteredInSef(string $taxNumber): bool
    {
        return $this->findByTaxNumber($taxNumber) !== null;
    }

    /**
     * Проверить, является ли контрагент бюджетным пользователем (госструктура).
     * Бюджетные пользователи имеют заполненный BudgetUserNumber (JBKJS).
     * Для них в IssuedInvoice нужно устанавливать ForwardToCRF = 'D'.
     *
     * @param  string $taxNumber  Матични број контрагента
     * @throws MinimaxApiException
     */
    public function isBudgetUser(string $taxNumber): bool
    {
        $entry = $this->findByTaxNumber($taxNumber);

        return $entry !== null && !empty($entry['BudgetUserNumber']);
    }

    /**
     * Определить значение ForwardToSEF для счёта на основе регистрации контрагента.
     *
     * Возвращает:
     *   self::FORWARD_ERACUN  — если контрагент зарегистрирован в SEF
     *   null                  — если контрагент не найден в SEF
     *
     * Использование при создании счёта:
     * ```php
     * $forwardToSef = $ef->resolveForwardToSef($customer['TaxNumber']);
     * $invoice = $mm->issuedInvoice($orgId)->create([
     *     ...
     *     'ForwardToSEF'  => $forwardToSef,
     *     'ForwardToCRF'  => $ef->isBudgetUser($customer['TaxNumber']) ? 'D' : 'N',
     * ]);
     * ```
     *
     * @throws MinimaxApiException
     */
    public function resolveForwardToSef(string $taxNumber): ?string
    {
        return $this->isRegisteredInSef($taxNumber)
            ? self::FORWARD_ERACUN
            : null;
    }

    // -----------------------------------------------------------------
    // Заблокированные методы — EFaktura только для чтения
    // -----------------------------------------------------------------

    public function get(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxEFaktura: получение записи по ID не поддерживается. ' .
                'Используйте list(), findByTaxNumber() или findByVatNumber().'
        );
    }

    public function create(array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxEFaktura: справочник SEF доступен только для чтения.'
        );
    }

    public function update(int|string $id, array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxEFaktura: справочник SEF доступен только для чтения.'
        );
    }

    public function delete(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxEFaktura: справочник SEF доступен только для чтения.'
        );
    }
}
