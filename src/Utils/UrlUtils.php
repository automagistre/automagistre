<?php

declare(strict_types=1);

namespace App\Utils;

use function array_key_exists;
use function count;
use function http_build_query;
use function is_array;
use LogicException;
use function parse_str;
use function parse_url;
use function str_replace;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UrlUtils
{
    public static function addQuery(string $url, string $key, string $value): string
    {
        $parsedUrl = parse_url($url);
        if (!is_array($parsedUrl)) {
            throw new LogicException('ParseUrl should return array.');
        }

        if (!array_key_exists('path', $parsedUrl) || '' === $parsedUrl['path']) {
            $url .= '/';
        }

        if (!array_key_exists('query', $parsedUrl) || '' === $parsedUrl['query']) {
            $url .= '?'.$key.'='.$value;

            return $url;
        }

        $queryElements = [];
        parse_str($parsedUrl['query'], $queryElements);

        if (array_key_exists($key, $queryElements)) {
            return str_replace([$key.'='.$queryElements[$key]], $key.'='.$value, $url);
        }

        $url .= '&'.$key.'='.$value;

        return $url;
    }

    public static function removeQuery(string $url, string $key): string
    {
        $query = parse_url($url, PHP_URL_QUERY);

        if (null === $query || '' === $query) {
            return $url;
        }

        $queryElements = [];
        parse_str($query, $queryElements);

        if (!array_key_exists($key, $queryElements)) {
            return $url;
        }

        unset($queryElements[$key]);

        if (0 === count($queryElements)) {
            return str_replace('?'.$query, '', $url);
        }

        return str_replace($query, http_build_query($queryElements), $url);
    }
}
