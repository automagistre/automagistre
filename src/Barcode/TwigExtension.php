<?php

namespace App\Barcode;

use AdamGaskins\Barcoder\Barcoder;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class TwigExtension extends AbstractExtension
{
    /**
     * {@inheritDoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('qr_code_base64', fn (string $data) => 'data:image/svg+xml;base64,'.base64_encode(Barcoder::qrcode($data)->toSvg())),
        ];
    }
}
