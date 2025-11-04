<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Exception;

/**
 * ConfigurationException
 *
 * Exception untuk kesalahan konfigurasi SSH
 *
 */
class ConfigurationException extends SshException
{
    /**
     * Constructor untuk ConfigurationException.
     *
     * @param string $message Pesan error konfigurasi yang spesifik.
     * @param int $code Error code.
     * @param \Throwable|null $previous Previous exception.
     */
    public function __construct(
        string $message = "Konfigurasi SSH tidak valid",
        int $code = 5001,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Membuat exception untuk config file tidak ditemukan.
     *
     * @param string $configPath Path config file.
     * @return self
     */
    public static function configFileNotFound(string $configPath): self
    {
        return new self("File konfigurasi tidak ditemukan: {$configPath}", 5002);
    }

    /**
     * Membuat exception untuk config tidak valid.
     *
     * @param string $configKey Key konfigurasi.
     * @param mixed $configValue Value konfigurasi.
     * @return self
     */
    public static function invalidConfig(string $configKey, $configValue): self
    {
        return new self("Konfigurasi tidak valid untuk '{$configKey}': " . print_r($configValue, true), 5003);
    }

    /**
     * Membuat exception untuk server tidak ditemukan.
     *
     * @param string $serverName Nama server.
     * @return self
     */
    public static function serverNotFound(string $serverName): self
    {
        return new self("Server tidak ditemukan dalam konfigurasi: {$serverName}", 5004);
    }

    /**
     * Membuat exception untuk required config missing.
     *
     * @param string $configKey Key konfigurasi yang required.
     * @return self
     */
    public static function missingRequiredConfig(string $configKey): self
    {
        return new self("Konfigurasi required tidak ditemukan: {$configKey}", 5005);
    }
}
