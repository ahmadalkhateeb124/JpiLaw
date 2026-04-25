<?php
/**
 * Admin language helpers — Arabic only.
 *
 * The DB still keeps both `_ar` and `_en` columns for forward-compatibility,
 * but the admin UI is Arabic-only. On save, the Arabic value is mirrored
 * into the English column too (handled by the saving code).
 *
 * Provides:
 *   $LANG_CODE  → 'Ar'
 *   $LANG_DIR   → 'rtl'
 *   $LANG_HTML  → 'ar'
 *   __('save')  → translated string
 *   localized($row, 'title') → returns $row['title_ar']
 */

declare(strict_types=1);

$LANG_CODE  = 'Ar';
$LANG_DIR   = 'rtl';
$LANG_HTML  = 'ar';
$LANG_FIELD = '_ar';

$GLOBALS['LANG_CODE']  = $LANG_CODE;
$GLOBALS['LANG_DIR']   = $LANG_DIR;
$GLOBALS['LANG_HTML']  = $LANG_HTML;
$GLOBALS['LANG_FIELD'] = $LANG_FIELD;

$translationsFile = BP_INCLUDES . '/translations.php';
$T = is_file($translationsFile) ? require $translationsFile : [];
$GLOBALS['T'] = $T;

function __(string $key, ...$args): string
{
    $val = $GLOBALS['T'][$key] ?? $key;
    return $args ? vsprintf($val, $args) : $val;
}

function localized(array $row, string $base, string $fallback = ''): string
{
    $val = $row[$base . '_ar'] ?? '';
    if ($val === '' || $val === null) {
        $val = $row[$base . '_en'] ?? $fallback;
    }
    return (string) $val;
}
