<?php

/**
 * The goal of this file is to allow developers a location
 * where they can overwrite core procedural functions and
 * replace them with their own. This file is loaded during
 * the bootstrap process and is called during the framework's
 * execution.
 *
 * This can be looked at as a `master helper` file that is
 * loaded early on, and may also contain additional functions
 * that you'd like to use throughout your entire application
 *
 * @see: https://codeigniter.com/user_guide/extending/common.html
 */

/**
 * Normaliza un nombre propio para mostrar: "ivan rafael luna" -> "Ivan Rafael Luna".
 * Respeta mayusculas internas (p.ej. "McDonald") y maneja tildes/UTF-8.
 */
if (! function_exists('cxNombre')) {
    function cxNombre(?string $nombre): string
    {
        $nombre = trim((string)$nombre);
        if ($nombre === '') {
            return '';
        }
        // Title-case por token: respeta mayusculas internas (p.ej. "McDonald")
        $tokens = preg_split('/\s+/u', $nombre, -1, PREG_SPLIT_NO_EMPTY);
        $tokens = array_map(static function ($t) {
            if ($t === mb_strtolower($t) || $t === mb_strtoupper($t)) {
                return mb_convert_case(mb_strtolower($t), MB_CASE_TITLE, 'UTF-8');
            }
            return $t;
        }, $tokens);
        return implode(' ', $tokens);
    }
}
