<?php

namespace Tests\Unit\Support;

use App\Support\ArabicNormalizer;
use Tests\TestCase;

/**
 * Exercises the exact fold table specified in /search-arabic Phase 2, plus
 * the phone-splitting cases. See KNOWN facts in that command for the "why"
 * behind each rule.
 */
class ArabicNormalizerTest extends TestCase
{
    // ------------------------------------------------------------------
    // name()
    // ------------------------------------------------------------------

    public function test_name_folds_alef_hamza_variants_to_bare_alef(): void
    {
        $this->assertSame('احمد', ArabicNormalizer::name('أحمد'));
    }

    public function test_name_folds_ta_marbuta_to_ha(): void
    {
        $this->assertSame('فاطمه', ArabicNormalizer::name('فاطمة'));
    }

    public function test_name_folds_alef_maqsura_to_ya(): void
    {
        $this->assertSame('مصطفي', ArabicNormalizer::name('مصطفى'));
    }

    public function test_name_strips_harakat(): void
    {
        $this->assertSame('محمد', ArabicNormalizer::name('مُحَمَّد'));
    }

    public function test_name_strips_tatweel(): void
    {
        $this->assertSame('محمد', ArabicNormalizer::name('مـــحـــمـــد'));
    }

    public function test_name_collapses_double_spaces(): void
    {
        $this->assertSame('عبد الرحمن', ArabicNormalizer::name('عبد  الرحمن'));
    }

    public function test_name_folds_waw_hamza_and_ya_hamza(): void
    {
        $this->assertSame('مومن', ArabicNormalizer::name('مؤمن'));
        $this->assertSame('بير', ArabicNormalizer::name('بئر'));
    }

    public function test_name_drops_standalone_hamza(): void
    {
        // جزء (juz', "part") ends in a standalone hamza with no seat letter —
        // distinct from ئ/ؤ/أ/إ/آ, which each fold to a different letter.
        $this->assertSame('جز', ArabicNormalizer::name('جزء'));
    }

    public function test_name_converts_arabic_indic_and_extended_digits(): void
    {
        $this->assertSame('محمد 5', ArabicNormalizer::name('محمد ٥'));
        $this->assertSame('محمد 5', ArabicNormalizer::name('محمد ۵'));
    }

    public function test_name_lowercases_latin_letters(): void
    {
        $this->assertSame('ahmed', ArabicNormalizer::name('AHMED'));
    }

    public function test_name_strips_blacklisted_punctuation(): void
    {
        $this->assertSame('بن قصير', ArabicNormalizer::name('بن-قصير'));
        $this->assertSame('بن قصير', ArabicNormalizer::name('بن/قصير'));
    }

    public function test_name_trims_and_handles_empty_string(): void
    {
        $this->assertSame('', ArabicNormalizer::name(''));
        $this->assertSame('', ArabicNormalizer::name('   '));
        $this->assertSame('احمد', ArabicNormalizer::name('  أحمد  '));
    }

    // ------------------------------------------------------------------
    // phone() — single number
    // ------------------------------------------------------------------

    public function test_phone_strips_non_digits(): void
    {
        $this->assertSame('0654689876', ArabicNormalizer::phone('065-468-9876'));
    }

    public function test_phone_rewrites_00213_prefix_to_leading_zero(): void
    {
        $this->assertSame('0654689876', ArabicNormalizer::phone('00213654689876'));
    }

    public function test_phone_rewrites_plus_213_prefix_to_leading_zero(): void
    {
        $this->assertSame('0654689876', ArabicNormalizer::phone('+213654689876'));
    }

    public function test_phone_converts_arabic_indic_digits(): void
    {
        $this->assertSame('0654689876', ArabicNormalizer::phone('٠٦٥٤٦٨٩٨٧٦'));
    }

    public function test_phone_keeps_malformed_nine_digit_number_as_is(): void
    {
        $this->assertSame('054789632', ArabicNormalizer::phone('054789632'));
    }

    public function test_phone_does_not_misfire_the_213_prefix_rule_on_a_short_number(): void
    {
        // A 9-digit number can't accidentally match the length-gated 213/00213 rewrite.
        $this->assertSame('213456789', ArabicNormalizer::phone('213456789'));
    }

    // ------------------------------------------------------------------
    // phones() — splitting a multi-number field
    // ------------------------------------------------------------------

    public function test_phones_splits_on_forward_slash(): void
    {
        $this->assertSame(['0745876577', '0654689876'], ArabicNormalizer::phones('0745876577/0654689876'));
    }

    public function test_phones_splits_on_slash_with_surrounding_spaces(): void
    {
        $this->assertSame(['0745876577', '0654689876'], ArabicNormalizer::phones('0745876577 / 0654689876'));
    }

    public function test_phones_normalizes_international_prefix(): void
    {
        $this->assertSame(['0654689876'], ArabicNormalizer::phones('+213654689876'));
    }

    public function test_phones_converts_arabic_indic_digits(): void
    {
        $this->assertSame(['0654689876'], ArabicNormalizer::phones('٠٦٥٤٦٨٩٨٧٦'));
    }

    public function test_phones_keeps_malformed_number_not_discarded(): void
    {
        $this->assertSame(['054789632'], ArabicNormalizer::phones('054789632'));
    }

    public function test_phones_returns_empty_array_for_empty_string(): void
    {
        $this->assertSame([], ArabicNormalizer::phones(''));
    }

    public function test_phones_splits_on_comma_and_arabic_comma_and_semicolons(): void
    {
        $this->assertSame(['0745876577', '0654689876'], ArabicNormalizer::phones('0745876577,0654689876'));
        $this->assertSame(['0745876577', '0654689876'], ArabicNormalizer::phones('0745876577،0654689876'));
        $this->assertSame(['0745876577', '0654689876'], ArabicNormalizer::phones('0745876577;0654689876'));
    }

    public function test_phones_deduplicates(): void
    {
        $this->assertSame(['0654689876'], ArabicNormalizer::phones('0654689876/0654689876'));
    }
}
