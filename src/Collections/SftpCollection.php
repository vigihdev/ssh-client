<?php

declare(strict_types=1);

namespace Vigihdev\Ssh\Collections;

use ArrayIterator;
use Closure;
use IteratorAggregate;
use JsonSerializable;
use phpseclib3\Net\SFTP;
use Stringable;

/**
 * Koleksi wrapper untuk item pada koneksi SFTP.
 *
 * Menyediakan helper chainable untuk memfilter/mentransform dan mengambil item
 * (file/direktori) dari sebuah koneksi SFTP.
 *
 * Implementasi: IteratorAggregate, JsonSerializable, Stringable.
 *
 * @psalm-immutable
 */
final class SftpCollection implements IteratorAggregate, JsonSerializable, Stringable
{

    /**
     * SftpCollection constructor.
     *
     * @param SFTP $sftp Instance SFTP yang digunakan untuk pemeriksaan file/dir
     * @param array<int,string> $items Daftar path item pada remote SFTP
     */
    public function __construct(
        private readonly SFTP $sftp,
        private array $items
    ) {}

    /**
     * Kembalikan koleksi yang hanya berisi file.
     *
     * @return SftpCollection
     */
    public function filesOnly(): self
    {
        $items = array_filter($this->items, fn($item) => $this->sftp->is_file($item));
        return new self($this->sftp, $items);
    }

    /**
     * Kembalikan koleksi yang hanya berisi direktori.
     *
     * @return SftpCollection
     */
    public function directoriesOnly(): self
    {
        $items = array_filter($this->items, fn($item) => $this->sftp->is_dir($item));
        return new self($this->sftp, $items);
    }

    /**
     * Filter koleksi berdasarkan ekstensi file.
     *
     * @param string $extension tanpa titik (mis. "txt")
     * @return SftpCollection
     */
    public function withExtension(string $extension): self
    {
        $items = array_filter($this->items, function ($item) use ($extension) {
            return $this->sftp->is_file($item) &&
                pathinfo($item, PATHINFO_EXTENSION) === $extension;
        });
        return new self($this->sftp, $items);
    }

    /**
     * Buang file/direktori tersembunyi (nama diawali '.').
     *
     * @return SftpCollection
     */
    public function withoutHidden(): self
    {
        $items = array_filter($this->items, fn($item) => !str_starts_with(basename($item), '.'));
        return new self($this->sftp, $items);
    }

    /**
     * Terapkan transform pada setiap item dan kembalikan koleksi baru.
     *
     * @param Closure(string): mixed $callback
     * @return SftpCollection
     */
    public function map(Closure $callback): self
    {
        return new self($this->sftp, array_map($callback, $this->items));
    }

    /**
     * Terapkan filter kustom dan kembalikan koleksi baru.
     *
     * @param Closure(string): bool $callback
     * @return SftpCollection
     */
    public function filter(Closure $callback): self
    {
        return new self($this->sftp, array_filter($this->items, $callback));
    }

    /**
     * @return ArrayIterator<int,string>
     */
    public function getIterator(): \Traversable
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Kembalikan representasi array untuk json_encode.
     *
     * @return array<int,string>
     */
    public function jsonSerialize(): array
    {
        return $this->items;
    }

    /**
     * Kembalikan representasi string.
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('SftpCollection(%d items)', count($this->items));
    }

    /**
     * Ambil item pertama atau null.
     */
    public function first(): ?string
    {
        return $this->items[0] ?? null;
    }

    /**
     * Ambil item terakhir atau null.
     */
    public function last(): ?string
    {
        return $this->items[array_key_last($this->items)] ?? null;
    }

    /**
     * Apakah koleksi kosong?
     * 
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Hitung jumlah item.
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * Kembalikan semua item sebagai array.
     *
     * @return array<int,string>
     */
    public function all(): array
    {
        return $this->items;
    }
}
