<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BarangaySetting extends Model
{
    use HasFactory;

    public const DEFAULT_IDENTITY = [
        'barangay'     => 'Siempre Viva Sur',
        'municipality' => 'Mallig',
        'province'     => 'Isabela',
    ];

    public const DEFAULT_WITNESS = [
        'name'  => 'ROSEMARIE M. GALANGAM',
        'title' => 'Barangay Secretary',
    ];

    protected $fillable = [
        'captain_name',
        'captain_title',
        'show_signature',
        'lgu_logo',
        'brgy_logo',
        'qr_code',
        'captain_signature',
        // Global certificate header / footer
        'header_lines',
        'header_office',
        'footer_label',
        'footer_address',
        'footer_email',
        // Barangay identity
        'barangay_name',
        'municipality',
        'province',
        // Default witness (First Time Job Seeker)
        'witness_name',
        'witness_title',
    ];

    /** Per-request cache of the single settings row. */
    protected static ?self $stored = null;
    protected static bool $storedLoaded = false;

    protected static function booted(): void
    {
        static::saved(fn () => static::forgetCurrent());
    }

    public static function forgetCurrent(): void
    {
        static::$stored = null;
        static::$storedLoaded = false;
    }

    /** The saved settings row, or null (also null if the table is not migrated yet). */
    public static function stored(): ?self
    {
        if (! static::$storedLoaded) {
            try {
                static::$stored = static::first();
            } catch (\Throwable $e) {
                static::$stored = null;
            }
            static::$storedLoaded = true;
        }

        return static::$stored;
    }

    /** Saved settings, or an unsaved instance holding the defaults. */
    public static function current(): self
    {
        return static::stored() ?? new static();
    }

    /** The saved row, creating it first if the system is brand new. */
    public static function ensure(): self
    {
        return static::first() ?? static::create([
            'captain_name' => 'HON. JOVENCIO P. EGIPTO',
            'captain_title' => 'Punong Barangay',
            'show_signature' => true,
        ]);
    }

    /** ['barangay' => ..., 'municipality' => ..., 'province' => ...] */
    public function identity(): array
    {
        return [
            'barangay'     => trim((string) $this->barangay_name) ?: self::DEFAULT_IDENTITY['barangay'],
            'municipality' => trim((string) $this->municipality) ?: self::DEFAULT_IDENTITY['municipality'],
            'province'     => trim((string) $this->province) ?: self::DEFAULT_IDENTITY['province'],
        ];
    }

    /** ['name' => ..., 'title' => ...] */
    public function witness(): array
    {
        return [
            'name'  => trim((string) $this->witness_name) ?: self::DEFAULT_WITNESS['name'],
            'title' => trim((string) $this->witness_title) ?: self::DEFAULT_WITNESS['title'],
        ];
    }

    /** Default header lines built from the identity (used if none are saved). */
    public function defaultHeaderLines(): array
    {
        $id = $this->identity();

        return [
            'REPUBLIC OF THE PHILIPPINES',
            mb_strtoupper('PROVINCE OF ' . $id['province']),
            mb_strtoupper('MUNICIPALITY OF ' . $id['municipality']),
            mb_strtoupper('BARANGAY OF ' . $id['barangay']),
        ];
    }

    /** Header lines as an array (blank lines removed, falls back to defaults). */
    public function getHeaderLinesListAttribute(): array
    {
        $lines = preg_split('/\r\n|\r|\n/', (string) $this->header_lines);
        $lines = array_values(array_filter(array_map('trim', $lines), fn ($l) => $l !== ''));

        return $lines ?: $this->defaultHeaderLines();
    }

    /**
     * Replace the old barangay / municipality / province names with the new ones
     * inside free text, in a single pass, matching whole words only and keeping
     * ALL-CAPS text in caps (e.g. "BARANGAY OF SIEMPRE VIVA SUR").
     */
    public static function swapIdentity(?string $text, array $old, array $new): ?string
    {
        if ($text === null || $text === '') {
            return $text;
        }

        $map = [];
        foreach (['barangay', 'municipality', 'province'] as $key) {
            $o = trim((string) ($old[$key] ?? ''));
            $n = trim((string) ($new[$key] ?? ''));
            if ($o !== '' && $o !== $n && ! isset($map[mb_strtolower($o)])) {
                $map[mb_strtolower($o)] = $n;
            }
        }
        if (! $map) {
            return $text;
        }

        $names = array_keys($map);
        usort($names, fn ($a, $b) => mb_strlen($b) <=> mb_strlen($a));
        $pattern = '/(?<![\p{L}\p{N}])(' . implode('|', array_map(fn ($n) => preg_quote($n, '/'), $names)) . ')(?![\p{L}\p{N}])/iu';

        return preg_replace_callback($pattern, function ($m) use ($map) {
            $new = $map[mb_strtolower($m[1])];
            $isCaps = mb_strtoupper($m[1]) === $m[1] && mb_strtolower($m[1]) !== $m[1];

            return $isCaps ? mb_strtoupper($new) : $new;
        }, $text);
    }
}
