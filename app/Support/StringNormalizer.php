<?php

namespace App\Support;

/**
 * Normaliza cadenas antes de persistir: trim, colapsar espacios múltiples
 * y eliminar caracteres de control (evita problemas en BD y exports).
 */
final class StringNormalizer
{
    /** Caracteres de control (0x00-0x1F y 0x7F) para eliminar */
    private const CONTROL_CHARS_REGEX = '/[\x00-\x1F\x7F]/u';

    public static function normalize(?string $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }
        $s = trim($value);
        $s = (string) preg_replace(self::CONTROL_CHARS_REGEX, '', $s);
        $s = (string) preg_replace('/\s+/u', ' ', $s);
        return trim($s);
    }
}
