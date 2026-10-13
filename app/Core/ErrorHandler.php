<?php
/**
 * File: app/Core/ErrorHandler.php
 * Pengelolaan Error, Debugging, dan Logging Terpusat
 */

namespace App\Core;

class ErrorHandler
{
    /** @var string Lokasi file log */
    private static string $logPath = __DIR__ . '/../../logs/app.log';

    /**
     * Mendaftarkan handler error, exception global, dan inisialisasi file log.
     */
    public static function register(): void
    {
        // Pastikan direktori logs tersedia
        $dir = dirname(self::$logPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        // Tangani standard PHP error
        set_error_handler([self::class, 'handleError']);

        // Tangani uncaught exception
        set_exception_handler([self::class, 'handleException']);

        // Tangani fatal shutdown error
        register_shutdown_function([self::class, 'handleShutdown']);

        // Jika file log belum ada atau kosong, tulis log inisialisasi awal
        if (!file_exists(self::$logPath) || filesize(self::$logPath) === 0) {
            self::logInfo("System initialized. Logger service started successfully.");
        }
    }

    /**
     * Callback untuk standard error PHP.
     */
    public static function handleError(int $level, string $message, string $file, int $line): bool
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        $logMsg = sprintf("PHP Error (Level %d): %s in %s on line %d", $level, $message, $file, $line);
        self::logError($logMsg);

        return true;
    }

    /**
     * Callback untuk unhandled exception.
     */
    public static function handleException(\Throwable $exception): void
    {
        $logMsg = sprintf(
            "Uncaught Exception: %s | Message: %s in %s:%d\nStack Trace:\n%s",
            get_class($exception),
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
            $exception->getTraceAsString()
        );
        self::logError($logMsg);

        // Tampilkan pesan bersahabat jika di web browser
        if (php_sapi_name() !== 'cli' && !headers_sent()) {
            http_response_code(500);
            echo "<div style='font-family:sans-serif; background:#fee2e2; border:1px solid #ef4444; color:#991b1b; padding:16px; border-radius:12px; margin:20px;'>";
            echo "<h4 style='margin:0 0 8px;'><i class='fa-solid fa-triangle-exclamation'></i> Terjadi Kendala Sistem</h4>";
            echo "<p style='margin:0;'>" . htmlspecialchars($exception->getMessage()) . "</p>";
            echo "<small style='display:block; margin-top:8px; color:#b91c1c;'>Insiden ini telah dicatat ke <code>logs/app.log</code>.</small>";
            echo "</div>";
        }
    }

    /**
     * Callback saat aplikasi berhenti mendadak (fatal error).
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error !== null && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $logMsg = sprintf("Fatal Error: %s in %s on line %d", $error['message'], $error['file'], $error['line']);
            self::logError($logMsg);
        }
    }

    /**
     * Mencatat pesan log bertipe INFO / REQUEST / ACTIVITY.
     */
    public static function logInfo(string $message): void
    {
        self::writeLog('INFO', $message);
    }

    /**
     * Mencatat pesan log bertipe WARNING.
     */
    public static function logWarning(string $message): void
    {
        self::writeLog('WARN', $message);
    }

    /**
     * Mencatat pesan log bertipe ERROR.
     */
    public static function logError(string $message): void
    {
        self::writeLog('ERROR', $message);
    }

    /**
     * Menulis entri log ke berkas logs/app.log.
     */
    private static function writeLog(string $level, string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $ip = $_SERVER['REMOTE_ADDR'] ?? 'CLI';
        $formatted = sprintf("[%s] [%s] [%s] %s%s", $timestamp, $level, $ip, $message, PHP_EOL);
        @file_put_contents(self::$logPath, $formatted, FILE_APPEND);
    }

    /**
     * Membaca baris log terbaru untuk antarmuka pemantauan.
     */
    public static function getRecentLogs(int $limit = 50): array
    {
        if (!file_exists(self::$logPath)) {
            return [];
        }

        $lines = file(self::$logPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if (!$lines) {
            return [];
        }

        return array_slice($lines, -$limit);
    }
}
