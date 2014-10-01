<?php
/**
 * Cosyx Bitrix Extender
 *
 * @package core
 * @author Peredelskiy Aleksey <info@web-n-roll.ru>
 * @version $Id$
 */

/**
 *
 * @package core
 */
class CSX_Date
{
    public static function dateRUToArray($date)
    {
        $dd = array();
        $a = explode(' ', $date);
        if (count($a == 2)) {
            $d = $a[0];
            $t = $a[1];
            $t = explode(':', $t);
            $dd['hh'] = isset($t[0]) ? $t[0] : NULL;
            $dd['mm'] = isset($t[1]) ? $t[1] : NULL;
            $dd['ss'] = isset($t[2]) ? $t[2] : NULL;
        } else {
            $d = $date;
            $dd['hh'] = NULL;
            $dd['mm'] = NULL;
            $dd['ss'] = NULL;
        }
        $d = explode('.', $d);
        if (count($d == 3)) {
            $dd['y'] = $d[2];
            $dd['m'] = $d[1];
            $dd['d'] = $d[0];
        } else {
            $dd = NULL;
        }
        return $dd;
    }

    public static function dateRU($date)
    {
        $d = explode(' ', $date);
        if (count($d == 2)) {
            $d = $d[0];
        } else {
            $d = $date;
        }
        $d = explode('-', $d);
        if (count($d == 3)) {
            $d = "{$d[2]}.{$d[1]}.{$d[0]}";
        } else {
            $d = NULL;
        }
        return $d;
    }

    public static function dateMD($date)
    {
        $d = explode('.', $date);
        if (count($d == 3)) {
            $d = "{$d[2]}-{$d[1]}-{$d[0]}";
        } else {
            $d = NULL;
        }
        return $d;
    }

    public static function dateArr($start, $finish)
    {
        $dates = array();
        if (is_array($start)) {
            $mindate_ = $start;
        } else {
            $mindate_ = Util::dateRUToArray($start);
        }
        if (is_array($finish)) {
            $maxdate_ = $finish;
        } else {
            $maxdate_ = Util::dateRUToArray($finish);
        }
        $start = mktime(0, 0, 0, $mindate_['m'], $mindate_['d'], $mindate_['y']);
        $finish = mktime(0, 0, 0, $maxdate_['m'], $maxdate_['d'], $maxdate_['y']);
        $currdate = $start;
        $i = 0;
        while ($currdate < $finish) {
            $currdate = mktime(0, 0, 0, $mindate_['m'], $mindate_['d'] + $i, $mindate_['y']);
            $dates[] = date('Y-m-d', $currdate);
            $i++;
        }
        return $dates;
    }

    public static function toMySqlDateTime($v)
    {
        if (!is_numeric($v)) {
            $v = strtotime($v);
        }

        return FormatDate('Y-m-d H:i:s', $v);
    }

    private static $DayEncoder = array(
        "monday" => "понедельник",
        "tuesday" => "вторник",
        "wednesday" => "среда",
        "thursday" => "четверг",
        "friday" => "пятница",
        "saturday" => "суббота",
        "sunday" => "воскресенье",
    );

    private static $DayEncoderShort = array(
        "mon" => "пн",
        "tue" => "вт",
        "wed" => "ср",
        "thu" => "чт",
        "fri" => "пт",
        "sat" => "сб",
        "sun" => "вс",
    );

    /**
     * @param string $s
     * @return string
     */
    public static function encodeDay($s)
    {
        $s = CSX_String::toLower($s);

        if (array_key_exists($s, self::$DayEncoder)) {
            return self::$DayEncoder[$s];
        }

        return $s;
    }

    /**
     * @param string $s
     * @return string
     */
    public static function encodeDayShort($s)
    {
        $s = CSX_String::toLower($s);

        if (array_key_exists($s, self::$DayEncoderShort)) {
            return self::$DayEncoderShort[$s];
        }

        return $s;
    }

    private static $MonthEncoder = array(
        '_' => array("january", "february", "march", "april", "may", "june", "july", "august", "september", "october", "november", "december"),
        'i' => array("январь", "февраль", "март", "апрель", "май", "июнь", "июль", "август", "сентябрь", "октябрь", "ноябрь", "декабрь"),
        'r' => array("января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"),
        't' => array("январе", "феврале", "марте", "апреле", "мае", "июне", "июле", "августе", "сентябре", "октябре", "ноябре", "декабре"),
    );

    private static $MonthEncoderShort = array(
        "jan" => "янв",
        "feb" => "фев",
        "mar" => "мар",
        "apr" => "апр",
        "may" => "мая",
        "jun" => "июн",
        "jul" => "июл",
        "aug" => "авг",
        "sep" => "сен",
        "oct" => "окт",
        "nov" => "ноя",
        "dec" => "дек",
    );

    /**
     * @param string $s
     * @param string $pad
     * @return string
     */
    public static function encodeMonth($s, $pad = 'i')
    {
        $s = CSX_String::toLower($s);

        for ($i = 0; $i < count(self::$MonthEncoder[$pad]); $i++) {
            $monthSrc = self::$MonthEncoder['_'][$i];
            $monthRepl = self::$MonthEncoder[$pad][$i];
            $s = str_replace($monthSrc, $monthRepl, $s);
        }

        return $s;
    }

    /**
     * @param string $s
     * @return string
     */
    public static function encodeMonthShort($s)
    {
        $s = CSX_String::toLower($s);

        if (array_key_exists($s, self::$MonthEncoderShort)) {
            return self::$MonthEncoderShort[$s];
        }

        return $s;
    }

    public static function getMonthName($n) {
        return self::$MonthEncoder['i'][$n-1];
    }

    public static function isWeekend($date) {
        $n = date('N', $date);
        return in_array($n, array(6, 7));
    }

    /**
     * Convert DateTime to Bitrix representation on queries
     *
     * @param $v int|DateTime
     * @return string
     */
    public static function toBitrixQueryDateTime($v)
    {
        if ($v instanceof DateTime) {
            $v = $v->getTimestamp();
        }

        $v = ConvertTimeStamp($v, 'SHORT', SITE_ID);
        return ConvertDateTime($v, "YYYY-MM-DD HH:MI:SS");
    }
}