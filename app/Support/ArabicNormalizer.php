<?php

namespace App\Support;

/**
 * Single source of truth for Arabic-tolerant name/phone normalization used
 * by the debt search feature. Pure, static, no dependencies.
 */
final class ArabicNormalizer
{
    private const NAME_PUNCTUATION_PATTERN = '/[.,\-_\'"\/\\\\()،؛:!؟?]/u';

    private const ARABIC_INDIC_DIGITS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    private const EXTENDED_ARABIC_INDIC_DIGITS = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    private const WESTERN_DIGITS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    /**
     * Normalize an Arabic (or mixed) name for tokenized, fold-tolerant search.
     */
    public static function name(string $raw): string
    {
        $value = mb_strtolower(self::collapseWhitespace(trim($raw)));

        $value = str_replace("\u{0640}", '', $value); // tatweel
        $value = preg_replace('/[\x{064B}-\x{065F}\x{0670}]/u', '', $value); // harakat / diacritics
        $value = preg_replace('/[\x{0623}\x{0625}\x{0622}\x{0671}\x{0672}\x{0673}]/u', "\u{0627}", $value); // أ إ آ ٱ ٲ ٳ -> ا
        $value = str_replace("\u{0629}", "\u{0647}", $value); // ة -> ه
        $value = str_replace("\u{0649}", "\u{064A}", $value); // ى -> ي
        $value = str_replace("\u{0624}", "\u{0648}", $value); // ؤ -> و
        $value = str_replace("\u{0626}", "\u{064A}", $value); // ئ -> ي
        $value = str_replace("\u{0621}", '', $value); // drop standalone ء

        $value = self::digitsToWestern($value);
        // Replaced with a space, not deleted outright — in Arabic names this
        // punctuation almost always separates two words ("بن-قصير"), so
        // deleting it outright would wrongly fuse them into one token.
        $value = preg_replace(self::NAME_PUNCTUATION_PATTERN, ' ', $value);

        return self::collapseWhitespace($value);
    }

    /**
     * Normalize a single phone number: digits only, then rewrite a leading
     * 00213 / +213 / 213 country-code prefix to a local leading zero.
     */
    public static function phone(string $raw): string
    {
        $digits = preg_replace('/[^0-9]/', '', self::digitsToWestern($raw));

        if (strlen($digits) === 14 && str_starts_with($digits, '00213')) {
            return '0' . substr($digits, 5);
        }

        if (strlen($digits) === 12 && str_starts_with($digits, '213')) {
            return '0' . substr($digits, 3);
        }

        return $digits;
    }

    /**
     * Split a raw phone field that may contain multiple numbers, normalize
     * each one, drop empties, and de-duplicate. Order is preserved.
     *
     * @return list<string>
     */
    public static function phones(string $raw): array
    {
        $parts = preg_split('/[\/\\\\,،;|\s]+/u', trim($raw), -1, PREG_SPLIT_NO_EMPTY);

        $result = [];
        foreach ($parts as $part) {
            $normalized = self::phone($part);
            if ($normalized !== '' && !in_array($normalized, $result, true)) {
                $result[] = $normalized;
            }
        }

        return $result;
    }

    private static function collapseWhitespace(string $value): string
    {
        return trim(preg_replace('/\s+/u', ' ', $value));
    }

    private static function digitsToWestern(string $value): string
    {
        $value = str_replace(self::ARABIC_INDIC_DIGITS, self::WESTERN_DIGITS, $value);

        return str_replace(self::EXTENDED_ARABIC_INDIC_DIGITS, self::WESTERN_DIGITS, $value);
    }
}
