<?php
/**
 * File: tests/TableTest.php
 * Unit Kompetensi:
 * - J.620100.033.02 (Melaksanakan Pengujian Unit Program - Pengujian Meja & Validator)
 */

namespace Tests;

require_once __DIR__ . '/TestCase.php';
require_once __DIR__ . '/../app/Config/Database.php';
require_once __DIR__ . '/../app/Core/ErrorHandler.php';
require_once __DIR__ . '/../app/Core/Model.php';
require_once __DIR__ . '/../app/Core/Validator.php';
require_once __DIR__ . '/../app/Models/Table.php';

use App\Core\Validator;

class TableTest extends TestCase
{
    /**
     * Menguji sanitasi masukan teks.
     */
    public function testSanitizationRemovesXss(): void
    {
        $unsafeInput = "<script>alert('hack')</script>Meja 1";
        $safeOutput = Validator::sanitize($unsafeInput);

        $this->assertFalse(str_contains($safeOutput, "<script>"), "Tag script harus diubah ke entitas HTML");
    }

    /**
     * Menguji validasi nomor telepon format Indonesia.
     */
    public function testPhoneValidation(): void
    {
        $validatorValid = new Validator();
        $validatorValid->phone('telepon', '081234567890');
        $this->assertTrue($validatorValid->isValid(), "Nomor 081234567890 harus valid");

        $validatorInvalid = new Validator();
        $validatorInvalid->phone('telepon', 'abc-invalid');
        $this->assertFalse($validatorInvalid->isValid(), "Nomor abjad harus tidak valid");
    }

    /**
     * Menguji validasi input wajib (Required).
     */
    public function testRequiredValidation(): void
    {
        $validator = new Validator();
        $validator->required('nama', '');
        $this->assertFalse($validator->isValid(), "Field kosong harus menghasilkan error");
    }
}
