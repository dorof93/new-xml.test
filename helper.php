<?php
class Helper {
    public static function checkElemActive ($val, $true_val, $text = '') {
        if ($val == $true_val) {
            return $text;
        }
        return '';
    }
}