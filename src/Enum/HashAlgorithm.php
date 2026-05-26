<?php

declare(strict_types=1);

namespace Shady\Altcha\Enum;

use AltchaOrg\Altcha\Algorithm\Argon2id;
use AltchaOrg\Altcha\Algorithm\DeriveKeyInterface;
use AltchaOrg\Altcha\Algorithm\Pbkdf2;
use AltchaOrg\Altcha\Algorithm\Scrypt;
use AltchaOrg\Altcha\Algorithm\Sha;
use AltchaOrg\Altcha\Algorithm\ShaAlgorithm;
use AltchaOrg\Altcha\HmacAlgorithm;

/**
 * Tutti gli algoritmi PoW supportati dal sistema ALTCHA v2.
 *
 * Requisiti lato browser (widget JS):
 *   SHA-*        → crypto.subtle nativa, nessun polyfill
 *   PBKDF2/*     → crypto.subtle nativa, nessun polyfill
 *   ARGON2ID     → WebAssembly tramite hash-wasm (incluso in altcha@3+)
 *   SCRYPT       → WebAssembly tramite hash-wasm (incluso in altcha@3+)
 *
 * Requisiti lato server (PHP):
 *   SHA-* / PBKDF2 → nessuna estensione aggiuntiva
 *   ARGON2ID       → ext-sodium  (quasi sempre disponibile in PHP 8+)
 *   SCRYPT         → ext-scrypt  (https://github.com/DomBlack/php-scrypt)
 */
enum HashAlgorithm: string
{
    // ── SHA ────────────────────────────────────────────────────────────────────
    case SHA256 = 'SHA-256';
    case SHA384 = 'SHA-384';
    case SHA512 = 'SHA-512';

    // ── PBKDF2 ─────────────────────────────────────────────────────────────────
    case PBKDF2_SHA256 = 'PBKDF2/SHA-256';
    case PBKDF2_SHA384 = 'PBKDF2/SHA-384';
    case PBKDF2_SHA512 = 'PBKDF2/SHA-512';

    // ── Memory-hard ────────────────────────────────────────────────────────────
    case ARGON2ID = 'ARGON2ID';
    case SCRYPT = 'SCRYPT';

    /** Istanzia il DeriveKeyInterface corrispondente da passare all'API ALTCHA. */
    public function toDeriveKeyInstance(): DeriveKeyInterface
    {
        return match ($this) {
            self::SHA256 => new Sha(ShaAlgorithm::SHA256),
            self::SHA384 => new Sha(ShaAlgorithm::SHA384),
            self::SHA512 => new Sha(ShaAlgorithm::SHA512),
            self::PBKDF2_SHA256 => new Pbkdf2(HmacAlgorithm::SHA256),
            self::PBKDF2_SHA384 => new Pbkdf2(HmacAlgorithm::SHA384),
            self::PBKDF2_SHA512 => new Pbkdf2(HmacAlgorithm::SHA512),
            self::ARGON2ID => new Argon2id(),
            self::SCRYPT => new Scrypt(),
        };
    }

    /** Etichetta leggibile per log e debug. */
    public function label(): string
    {
        return match ($this) {
            self::SHA256 => 'SHA-256 (veloce, uso generale)',
            self::SHA384 => 'SHA-384',
            self::SHA512 => 'SHA-512 (più lento)',
            self::PBKDF2_SHA256 => 'PBKDF2/SHA-256 (resistente al brute-force)',
            self::PBKDF2_SHA384 => 'PBKDF2/SHA-384',
            self::PBKDF2_SHA512 => 'PBKDF2/SHA-512',
            self::ARGON2ID => 'Argon2id (memory-hard, alta sicurezza)',
            self::SCRYPT => 'Scrypt (memory-hard, richiede ext-scrypt)',
        };
    }

    /** Estensione PHP necessaria, null se non richiesta. */
    public function requiredPhpExtension(): ?string
    {
        return match ($this) {
            self::ARGON2ID => 'sodium',
            self::SCRYPT => 'scrypt',
            default => null,
        };
    }

    /** True se l'algoritmo è utilizzabile nell'ambiente corrente. */
    public function isAvailable(): bool
    {
        $ext = $this->requiredPhpExtension();

        return null === $ext || \extension_loaded($ext);
    }
}
