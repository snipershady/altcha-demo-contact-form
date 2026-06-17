<?php

declare(strict_types=1);

namespace Shady\Altcha\Enum;

use AltchaOrg\Altcha\HmacAlgorithm;

/**
 * Preset difficulty levels for PBKDF2 PoW challenges.
 * 
 * approx ops = cost × 256^keyPrefixLength
 *            = 50.000 × 65.536
 *            = 3.280.000.000 ≈ 3.28 B
 *
 * Total work per solve ≈ cost × 256^keyPrefixLength HMAC operations.
 *
 * | Case        | cost    | keyPrefixLength | algorithm  | avg attempts | approx ops       |
 * |-------------|---------|-----------------|------------|--------------|------------------|
 * | LOW         |  10 000 |        1        | SHA-256    |        256   |     2.56 M       |
 * | MEDIUM      |  50 000 |        2        | SHA-384    |     65 536   |     3.28 B       |
 * | MEDIUM_HIGH |  75 000 |        2        | SHA-512    |     65 536   |     4.92 B       |
 * | HIGH        | 100 000 |        2        | SHA-512    |     65 536   |     6.55 B       |
 * | VERY_HIGH   | 200 000 |        3        | SHA-512    | 16 777 216   |     3.36 T       |
 */
enum Pbkdf2Difficulty
{
    case LOW;
    case MEDIUM;
    case MEDIUM_HIGH;
    case HIGH;
    case VERY_HIGH;

    public function cost(): int
    {
        return match ($this) {
            self::LOW         => 10000,
            self::MEDIUM      => 50000,
            self::MEDIUM_HIGH => 75000,
            self::HIGH        => 100000,
            self::VERY_HIGH   => 200000,
        };
    }

    public function keyPrefixLength(): int
    {
        return match ($this) {
            self::LOW         => 1,
            self::MEDIUM      => 2,
            self::MEDIUM_HIGH => 2,
            self::HIGH        => 2,
            self::VERY_HIGH   => 3,
        };
    }

    public function hmacAlgorithm(): HmacAlgorithm
    {
        return match ($this) {
            self::LOW    => HmacAlgorithm::SHA256,
            self::MEDIUM => HmacAlgorithm::SHA384,
            self::MEDIUM_HIGH, self::HIGH, self::VERY_HIGH => HmacAlgorithm::SHA512,
        };
    }

    /** Average number of PBKDF2 derivations the solver must compute. */
    public function averageAttempts(): int
    {
        return 256 ** $this->keyPrefixLength();
    }
}
