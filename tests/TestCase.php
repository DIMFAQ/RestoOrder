<?php
/**
 * File: tests/TestCase.php
 * Unit Kompetensi:
 * - J.620100.033.02 (Melaksanakan Pengujian Unit Program)
 * - J.620100.018.02 (OOP - Unit Test Base Class)
 */

namespace Tests;

abstract class TestCase
{
    protected int $assertionsCount = 0;
    protected array $testResults = [];

    /**
     * Memeriksa kesamaan nilai (Equality assertion).
     */
    public function assertEquals(mixed $expected, mixed $actual, string $message = ''): void
    {
        $this->assertionsCount++;
        $passed = ($expected === $actual);
        $this->recordAssertion($passed, $message, "Expected: " . json_encode($expected) . ", Got: " . json_encode($actual));
    }

    /**
     * Memeriksa bahwa nilai adalah true.
     */
    public function assertTrue(bool $condition, string $message = ''): void
    {
        $this->assertionsCount++;
        $this->recordAssertion($condition, $message, "Expected true, got false");
    }

    /**
     * Memeriksa bahwa nilai adalah false.
     */
    public function assertFalse(bool $condition, string $message = ''): void
    {
        $this->assertionsCount++;
        $this->recordAssertion(!$condition, $message, "Expected false, got true");
    }

    /**
     * Memeriksa bahwa array atau string tidak kosong.
     */
    public function assertNotEmpty(mixed $value, string $message = ''): void
    {
        $this->assertionsCount++;
        $passed = !empty($value);
        $this->recordAssertion($passed, $message, "Expected non-empty value");
    }

    private function recordAssertion(bool $passed, string $message, string $detail): void
    {
        $this->testResults[] = [
            'passed'  => $passed,
            'message' => $message,
            'detail'  => $detail,
        ];

        if (!$passed) {
            throw new \Exception("Assertion Failed: {$message} ({$detail})");
        }
    }

    /**
     * Menjalankan seluruh method pengujian dalam class.
     */
    public function run(): array
    {
        $methods = get_class_methods($this);
        $summary = [
            'class'      => static::class,
            'passed'     => 0,
            'failed'     => 0,
            'tests'      => []
        ];

        foreach ($methods as $method) {
            if (str_starts_with($method, 'test')) {
                try {
                    $this->{$method}();
                    $summary['passed']++;
                    $summary['tests'][] = [
                        'method' => $method,
                        'status' => 'PASS',
                        'error'  => null
                    ];
                } catch (\Throwable $e) {
                    $summary['failed']++;
                    $summary['tests'][] = [
                        'method' => $method,
                        'status' => 'FAIL',
                        'error'  => $e->getMessage()
                    ];
                }
            }
        }

        return $summary;
    }
}
