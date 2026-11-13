<?php
namespace App\Core;

class Helper {
    public static function url($path = '') {
        $path = ltrim($path, '/');
        if (function_exists('base_url')) {
            return base_url($path);
        }
        return '/' . $path;
    }
}

