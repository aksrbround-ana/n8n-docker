<?php

namespace app\components\resources;

use app\components\MinimaxResource;
use app\components\exceptions\MinimaxApiException;

/**
 * MinimaxExchangeRate — курсы валют по данным НБС (Народна банка Србије).
 *
 * Контекст:
 *   ExchangeRate — справочник только для чтения. Курсы берутся из официального
 *   источника (НБС) и используются при создании валютных счетов и проводок.
 *   Нужны для корректного заполнения поля ExchangeRate в IssuedInvoice,
 *   ReceivedInvoice и JournalEntry при работе с иностранной валютой.
 *
 * Эндпоинты:
 *   GET api/orgs/{orgId}/exchange-rates/code({currencyCode})?date={date}  — по коду валюты
 *   GET api/orgs/{orgId}/exchange-rates/{currencyId}?date={date}          — по ID валюты
 *
 * Структура ответа:
 * {
 *   "Date":     "2024-06-01T00:00:00",
 *   "Currency": { "ID": 2, "Name": "EUR", "ResourceUrl": "..." },
 *   "MidRate":  117.1234   // средний курс к RSD
 * }
 *
 * Использование:
 * ```php
 * $er = Yii::$app->minimax->exchangeRate($orgId);
 *
 * // Курс EUR на сегодня
 * $rate = $er->getByCode('EUR');
 * echo $rate['MidRate']; // 117.1234
 *
 * // Курс EUR на конкретную дату
 * $rate = $er->getByCode('EUR', '2024-01-15');
 *
 * // Курс по ID валюты
 * $rate = $er->getById(2);
 *
 * // Конвертировать сумму в RSD
 * $rsd = $er->convertToRsd(1000, 'EUR');
 * ```
 */
class MinimaxExchangeRate extends MinimaxResource
{
    protected function getResourceName(): string
    {
        return 'exchange-rates';
    }

    // -----------------------------------------------------------------
    // Публичные методы (только чтение)
    // -----------------------------------------------------------------

    /**
     * Получить курс валюты по коду (EUR, USD, CHF...) на заданную дату.
     * Если дата не указана — возвращает курс на сегодня.
     *
     * GET api/orgs/{orgId}/exchange-rates/code({currencyCode})?date={date}
     *
     * @param  string      $currencyCode  Код валюты, например: 'EUR', 'USD'
     * @param  string|null $date          Дата в формате 'YYYY-MM-DD', null = сегодня
     * @return array       { Date, Currency, MidRate }
     * @throws MinimaxApiException
     */
    public function getByCode(string $currencyCode, string|null $date = null): array
    {
        $params = [];
        if ($date !== null) {
            $params['date'] = $date;
        }

        return $this->getClient()->get(
            $this->buildPath("code({$currencyCode})"),
            $params
        );
    }

    /**
     * Получить курс валюты по её ID в Minimax на заданную дату.
     *
     * GET api/orgs/{orgId}/exchange-rates/{currencyId}?date={date}
     *
     * @param  int|string  $currencyId  ID валюты из справочника Currency
     * @param  string|null $date        Дата в формате 'YYYY-MM-DD', null = сегодня
     * @return array       { Date, Currency, MidRate }
     * @throws MinimaxApiException
     */
    public function getById(int|string $currencyId, string|null $date = null): array
    {
        $params = [];
        if ($date !== null) {
            $params['date'] = $date;
        }

        return $this->getClient()->get($this->buildPath($currencyId), $params);
    }

    /**
     * Конвертировать сумму в динары (RSD) по курсу на заданную дату.
     *
     * @param  float       $amount        Сумма в иностранной валюте
     * @param  string      $currencyCode  Код валюты: 'EUR', 'USD', 'CHF'...
     * @param  string|null $date          Дата курса, null = сегодня
     * @return float       Сумма в RSD, округлённая до 2 знаков
     * @throws MinimaxApiException
     */
    public function convertToRsd(
        float       $amount,
        string      $currencyCode,
        string|null $date = null,
    ): float {
        $rate = $this->getByCode($currencyCode, $date);

        return round($amount * (float)$rate['MidRate'], 2);
    }

    /**
     * Конвертировать сумму из динаров (RSD) в иностранную валюту.
     *
     * @param  float       $amountRsd     Сумма в RSD
     * @param  string      $currencyCode  Код валюты: 'EUR', 'USD', 'CHF'...
     * @param  string|null $date          Дата курса, null = сегодня
     * @return float       Сумма в иностранной валюте, округлённая до 4 знаков
     * @throws MinimaxApiException
     * @throws \DivisionByZeroError если курс равен нулю
     */
    public function convertFromRsd(
        float       $amountRsd,
        string      $currencyCode,
        string|null $date = null,
    ): float {
        $rate    = $this->getByCode($currencyCode, $date);
        $midRate = (float)$rate['MidRate'];

        if ($midRate == 0.0) {
            throw new \DivisionByZeroError(
                "MinimaxExchangeRate: курс {$currencyCode} равен нулю."
            );
        }

        return round($amountRsd / $midRate, 4);
    }

    // -----------------------------------------------------------------
    // Заблокированные методы — только для чтения
    // -----------------------------------------------------------------

    public function list(array $params = []): array
    {
        throw new \BadMethodCallException(
            'MinimaxExchangeRate: список курсов недоступен. ' .
                'Используйте getByCode() или getById().'
        );
    }

    public function get(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxExchangeRate: используйте getByCode() или getById().'
        );
    }

    public function create(array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxExchangeRate: курсы валют доступны только для чтения.'
        );
    }

    public function update(int|string $id, array $data): array
    {
        throw new \BadMethodCallException(
            'MinimaxExchangeRate: курсы валют доступны только для чтения.'
        );
    }

    public function delete(int|string $id): array
    {
        throw new \BadMethodCallException(
            'MinimaxExchangeRate: курсы валют доступны только для чтения.'
        );
    }
}
