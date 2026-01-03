<?php

namespace app\services;

use Yii;
use app\models\Accountant;

class CalendarService
{
    const getHolidaysUrl = 'https://date.nager.at/api/v3/PublicHolidays/{{year}}/RS';

    public static function getSerbianHolidays()
    {
        $year = date('Y');
        $url = str_replace('{{year}}', $year, self::getHolidaysUrl);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($curl);
        return json_decode($response, true);
    }

    public static function getClosestWorkingDay($date)
    {
        $holidays = self::getSerbianHolidays();
        $holidayDates = array_map(function ($holiday) {
            return $holiday['date'];
        }, $holidays);

        $currentDate = strtotime($date);
        while (in_array(date('Y-m-d', $currentDate), $holidayDates) || date('N', $currentDate) >= 6) {
            $currentDate = strtotime('-1 day', $currentDate);
        }
        return date('Y-m-d', $currentDate);
    }
}
