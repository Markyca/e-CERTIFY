<?php

namespace App\Support;

/**
 * Builds the names and wording printed on certificates.
 * Uses multibyte functions so names with Ñ, Á, É, etc. are handled correctly.
 */
class NameFormatter
{
    /** Women with these civil statuses are addressed as "Mrs." (separated women are still legally married in the Philippines). */
    public const MRS_STATUSES = ['married', 'widowed', 'widow', 'separated'];

    protected static function lower(?string $value): string
    {
        return mb_strtolower(trim((string) $value), 'UTF-8');
    }

    public static function upper(?string $value): string
    {
        return mb_strtoupper(trim((string) $value), 'UTF-8');
    }

    /** MR. for men; MS. for single women; MRS. for married, widowed, or separated women. */
    public static function honorific(?string $gender, ?string $civilStatus): string
    {
        if (self::lower($gender) !== 'female') {
            return 'MR.';
        }

        return in_array(self::lower($civilStatus), self::MRS_STATUSES, true) ? 'MRS.' : 'MS.';
    }

    /** First letter of the middle name with a period, e.g. "Ñ." */
    public static function initial(?string $middleName): string
    {
        $middle = trim((string) $middleName);

        return $middle === '' ? '' : mb_strtoupper(mb_substr($middle, 0, 1, 'UTF-8'), 'UTF-8') . '.';
    }

    /** JUAN D. CRUZ JR. (no honorific); includes the name extension when there is one. */
    public static function standard($resident): string
    {
        $parts = [
            self::upper($resident->first_name),
            self::initial($resident->middle_name),
            self::upper($resident->last_name),
            self::upper($resident->extension_name),
        ];

        return trim(preg_replace('/\s+/u', ' ', implode(' ', array_filter($parts, fn ($p) => $p !== ''))));
    }

    /** MR. JUAN D. CRUZ JR. / MRS. MARIA S. CRUZ */
    public static function formal($resident): string
    {
        return self::honorific($resident->gender, $resident->civil_status) . ' ' . self::standard($resident);
    }

    public static function hisHer(?string $gender): string
    {
        return self::lower($gender) === 'female' ? 'her' : 'his';
    }

    public static function heShe(?string $gender): string
    {
        return self::lower($gender) === 'female' ? 'she' : 'he';
    }

    /** "of legal age" for adults, otherwise the actual age. */
    public static function legalAge(int $age): string
    {
        return $age >= 18 ? 'of legal age' : $age . ' years of age';
    }

    /**
     * Put the purpose into a sentence: "Scholarship" -> "scholarship", "Bank Requirement" -> "bank requirement",
     * but keep acronyms and brand names as typed ("SSS Loan" -> "SSS loan", "PhilHealth", "4Ps").
     */
    public static function purposeText(?string $purpose): string
    {
        $tokens = preg_split('/(\s+)/u', trim((string) $purpose), -1, PREG_SPLIT_DELIM_CAPTURE);

        return implode('', array_map(function ($token) {
            if (preg_match('/^(\p{Lu}\p{Ll}*)([^\p{L}\p{N}]*)$/u', $token, $m)) {
                return mb_strtolower($m[1], 'UTF-8') . $m[2];
            }

            return $token;
        }, $tokens));
    }
}
