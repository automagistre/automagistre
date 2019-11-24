<?php

declare(strict_types=1);

namespace App\Mercure;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

final class JwtProvider
{
    public function __invoke(): string
    {
        return (string) (new Builder())
            ->withClaim('mercure', ['publish' => []])
            ->getToken(new Sha256(), new Key('aVerySecretKey'));
    }
}
