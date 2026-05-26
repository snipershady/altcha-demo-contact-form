<?php

declare(strict_types=1);

namespace Shady\Altcha\Factory;

use AltchaOrg\Altcha\Algorithm\DeriveKeyInterface;
use Shady\Altcha\Enum\HashAlgorithm;

/**
 * Restituisce il DeriveKeyInterface corretto a partire dal nome stringa dell'algoritmo
 * (come presente nel campo parameters.algorithm del challenge JSON).
 */
class AlgorithmFactory
{
    public static function fromName(string $algorithmName): DeriveKeyInterface
    {
        $alg = HashAlgorithm::tryFrom($algorithmName);

        if (null === $alg) {
            throw new \InvalidArgumentException(sprintf("Algoritmo non supportato: '%s'", $algorithmName));
        }

        if (!$alg->isAvailable()) {
            $ext = $alg->requiredPhpExtension();
            throw new \RuntimeException(sprintf("L'algoritmo %s richiede l'estensione PHP '%s', non disponibile.", $algorithmName, $ext));
        }

        return $alg->toDeriveKeyInstance();
    }
}
