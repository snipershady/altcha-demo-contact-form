<?php

declare(strict_types=1);

namespace Shady\Altcha\Factory;

use AltchaOrg\Altcha\Challenge;
use AltchaOrg\Altcha\ChallengeParameters;
use AltchaOrg\Altcha\Payload;
use AltchaOrg\Altcha\Solution;

/**
 * Ricostruisce un oggetto Payload dalla stringa base64 inviata dal widget nel campo altcha.
 *
 * Formato atteso (decodificato):
 * {
 *   "challenge": { "parameters": {...}, "signature": "hex" },
 *   "solution":  { "counter": int, "derivedKey": "hex", "time": float? }
 * }
 */
class PayloadFactory
{
    /** @throws \InvalidArgumentException se il payload non è valido */
    public static function fromBase64(string $base64): Payload
    {
        $decoded = base64_decode($base64, true);
        if (false === $decoded) {
            throw new \InvalidArgumentException('Il campo altcha non è una stringa base64 valida.');
        }

        try {
            /** @var array<string, mixed> $data */
            $data = json_decode($decoded, true, 8, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $jsonException) {
            throw new \InvalidArgumentException('Il campo altcha non contiene JSON valido: '.$jsonException->getMessage(), $jsonException->getCode(), $jsonException);
        }

        if (!isset($data['challenge']['parameters'], $data['solution']['counter'], $data['solution']['derivedKey'])
            || !\is_array($data['challenge']['parameters'])
            || !\is_int($data['solution']['counter'])
            || !\is_string($data['solution']['derivedKey'])
        ) {
            throw new \InvalidArgumentException('Struttura del payload altcha non valida.');
        }

        $challengeParameters = ChallengeParameters::fromArray($data['challenge']['parameters']);
        $challenge = new Challenge(
            $challengeParameters,
            isset($data['challenge']['signature']) && \is_string($data['challenge']['signature'])
                ? $data['challenge']['signature']
                : null,
        );

        $solution = new Solution(
            counter: $data['solution']['counter'],
            derivedKey: $data['solution']['derivedKey'],
            time: isset($data['solution']['time']) && \is_float($data['solution']['time'])
                            ? $data['solution']['time']
                            : null,
        );

        return new Payload($challenge, $solution);
    }
}
