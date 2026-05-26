<?php

declare(strict_types=1);

namespace Shady\Altcha\Config;

use Shady\Altcha\Enum\HashAlgorithm;

/**
 * Parametri di configurazione per la generazione di un challenge PoW ALTCHA v2.
 *
 * La difficoltà è determinata da due fattori combinati:
 *   - keyPrefix: prefisso esadecimale che la chiave derivata deve avere.
 *     Ogni byte aggiunto moltiplica per ~256 il numero medio di tentativi.
 *     Esempi: '00' ≈ 256 tentativi, '0000' ≈ 65 536 tentativi.
 *   - cost: iterazioni interne per ogni tentativo.
 *     SHA-*: 1 basta (il costo è basso per natura)
 *     PBKDF2: 100–500 è ragionevole
 *     Argon2id: 1–5 (il costo è già alto per via della memoria)
 *
 * @param HashAlgorithm $algorithm     algoritmo di derivazione della chiave
 * @param int           $cost          iterazioni interne per tentativo
 * @param int           $keyLength     lunghezza della chiave derivata in byte (default 32)
 * @param string        $keyPrefix     prefisso hex che la chiave deve iniziare (default '00')
 * @param int|null      $memoryCost    memoria in KB per Argon2id/Scrypt (null = default lib)
 * @param int|null      $parallelism   parallelismo per Argon2id/Scrypt (null = default lib)
 * @param int           $expirySeconds validità del challenge in secondi (default 120)
 */
final readonly class PowConfig
{
    public function __construct(
        public HashAlgorithm $algorithm = HashAlgorithm::ARGON2ID,
        public int $cost = 3,
        public int $keyLength = 32,
        public string $keyPrefix = '00',
        public ?int $memoryCost = 1024,
        public ?int $parallelism = null,
        public int $expirySeconds = 120,
    ) {
        if (!$this->algorithm->isAvailable()) {
            $ext = $this->algorithm->requiredPhpExtension();
            throw new \RuntimeException(sprintf("L'algoritmo %s richiede l'estensione PHP '%s', non disponibile.", $this->algorithm->value, $ext));
        }

        if ($cost < 1) {
            throw new \InvalidArgumentException('cost deve essere >= 1');
        }

        if ($keyLength < 1) {
            throw new \InvalidArgumentException('keyLength deve essere >= 1');
        }

        if ($expirySeconds < 1) {
            throw new \InvalidArgumentException('expirySeconds deve essere >= 1');
        }
    }

    public function expiryInterval(): \DateInterval
    {
        return new \DateInterval(sprintf('PT%dS', $this->expirySeconds));
    }
}
