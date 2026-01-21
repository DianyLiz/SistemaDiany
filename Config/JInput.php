<?php
namespace Config;

class JInput {
    public static function sanitizeString($value){
        if ($value === null) return '';
        $v = is_string($value) ? $value : strval($value);
        $v = trim($v);
        // remove HTML tags
        $v = strip_tags($v);
        // normalize whitespace
        $v = preg_replace('/\s+/', ' ', $v);
        return $v;
    }

    public static function sanitizeEmail($value){
        $v = self::sanitizeString($value);
        $v = filter_var($v, FILTER_SANITIZE_EMAIL);
        return $v ?: '';
    }

    public static function sanitizeArray($arr){
        $out = [];
        foreach ((array)$arr as $k => $v) {
            $key = is_string($k) ? preg_replace('/[^A-Za-z0-9_]/','', $k) : $k;
            if ($key === 'email') {
                $out[$key] = self::sanitizeEmail($v);
            } else {
                $out[$key] = self::sanitizeString($v);
            }
        }
        return $out;
    }
}
?>