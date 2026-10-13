<?php
/**
 * File: app/Core/Validator.php
 * Unit Kompetensi:
 * - J.620100.017.02 (Mengimplementasikan Pemrograman Terstruktur)
 * - J.620100.016.01 (Guidelines & Best Practices)
 */

namespace App\Core;

class Validator
{
    /** @var array Menyimpan daftar pesan kesalahan validasi */
    private array $errors = [];

    /**
     * Sanitasi data masukan teks untuk mencegah XSS.
     *
     * @param string|null $input
     * @return string
     */
    public static function sanitize(?string $input): string
    {
        if ($input === null) {
            return '';
        }
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Validasi kolom wajib diisi (Required).
     */
    public function required(string $field, mixed $value, string $customMessage = ''): self
    {
        if (empty($value) && $value !== '0' && $value !== 0) {
            $this->errors[$field] = !empty($customMessage) ? $customMessage : "Kolom {$field} wajib diisi.";
        }
        return $this;
    }

    /**
     * Validasi format nomor telepon Indonesia.
     */
    public function phone(string $field, ?string $value): self
    {
        if (!empty($value)) {
            // Minimal 10 digit, maksimal 15 digit angka (opsional awalan + atau 0)
            if (!preg_match('/^(\+62|62|0)[0-9]{8,13}$/', $value)) {
                $this->errors[$field] = "Format nomor telepon {$field} tidak valid (cth: 081234567890).";
            }
        }
        return $this;
    }

    /**
     * Validasi format alamat email.
     */
    public function email(string $field, ?string $value): self
    {
        if (!empty($value) && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = "Format email tidak valid.";
        }
        return $this;
    }

    /**
     * Validasi nilai numerik minimal.
     */
    public function minNumeric(string $field, mixed $value, float $min): self
    {
        if (!is_numeric($value) || (float)$value < $min) {
            $this->errors[$field] = "Nilai {$field} minimal harus {$min}.";
        }
        return $this;
    }

    /**
     * Validasi format tanggal (YYYY-MM-DD).
     */
    public function date(string $field, ?string $value): self
    {
        if (!empty($value)) {
            $d = \DateTime::createFromFormat('Y-m-d', $value);
            if (!($d && $d->format('Y-m-d') === $value)) {
                $this->errors[$field] = "Format tanggal harus YYYY-MM-DD.";
            }
        }
        return $this;
    }

    /**
     * Cek apakah seluruh validasi berhasil tanpa error.
     */
    public function isValid(): bool
    {
        return empty($this->errors);
    }

    /**
     * Mengambil daftar error yang terkumpul.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    /**
     * Mengambil pesan error pertama.
     */
    public function getFirstError(): ?string
    {
        if (empty($this->errors)) {
            return null;
        }
        $values = array_values($this->errors);
        return $values[0];
    }
}
