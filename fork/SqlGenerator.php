<?php

declare(strict_types=1);

namespace Doctrine\Migrations\Generator;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\Migrations\Configuration\Configuration;
use Doctrine\Migrations\Metadata\Storage\TableMetadataStorageConfiguration;
use Doctrine\SqlFormatter\NullHighlighter;
use Doctrine\SqlFormatter\SqlFormatter;
use function implode;
use function sprintf;
use function stripos;
use function strlen;

class SqlGenerator
{
    /** @var Configuration */
    private $configuration;

    /** @var AbstractPlatform */
    private $platform;

    public function __construct(Configuration $configuration, AbstractPlatform $platform)
    {
        $this->configuration = $configuration;
        $this->platform = $platform;
    }

    public function generate(
        array $sql,
        bool $formatted = false,
        int $lineLength = 120,
        bool $checkDbPlatform = true,
    ): string {
        $code = [];

        $storageConfiguration = $this->configuration->getMetadataStorageConfiguration();
        foreach ($sql as $query) {
            if (
                $storageConfiguration instanceof TableMetadataStorageConfiguration
                && false !== stripos($query, $storageConfiguration->getTableName())
            ) {
                continue;
            }

            if (strpos($query, 'hdb_catalog')) {
                continue; // skip hasura metadata schema
            }

            if ($formatted) {
                $maxLength = $lineLength - 18 - 8; // max - php code length - indentation

                if (strlen($query) > $maxLength) {
                    $query = (new SqlFormatter(new NullHighlighter()))->format($query);
                }
            }

            $code[] = sprintf('%s;', $query);
        }

        return implode("\n", $code);
    }
}
