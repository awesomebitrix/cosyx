<?php
class CSX_Filter {
    protected static $prefix = 'CSX_FILTER_';

    public static function registerFilter($name, $filter) {
        $GLOBALS[self::$prefix . $name] = $filter;
    }

    public static function getName($name) {
        return self::$prefix . $name;
    }

    public static function getFilter($name) {
        return $GLOBALS[self::$prefix . $name];
    }

    public static function isFilterExists($name) {
        return isset($GLOBALS[self::$prefix . $name]);
    }
}