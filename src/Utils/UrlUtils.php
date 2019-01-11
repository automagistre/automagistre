<?php

declare(strict_types=1);

namespace App\Utils;

/**
 * @author Konstantin Grachev <me@grachevko.ru>
 */
final class UrlUtils
{
    public static function addQuery(string $url, string $key, string $value): string
    {
        $parsedUrl = \parse_url($url);

        if (!\array_key_exists('path', $parsedUrl) || null === $parsedUrl['path']) {
            $url .= '/';
        }

        if (!isset($parsedUrl['query']) || null === $parsedUrl['query'] || '' === $parsedUrl['query']) {
            $url .= '?'.$key.'='.$value;

            return $url;
        }

        $queryElements = [];
        \parse_str($parsedUrl['query'], $queryElements);

        if (\array_key_exists($key, $queryElements)) {
            return \str_replace([$key.'='.$queryElements[$key]], $key.'='.$value, $url);
        }

        $url .= '&'.$key.'='.$value;

        return $url;
    }

    public static function removeQuery(string $url, string $key): string
    {
        $query = \parse_url($url, PHP_URL_QUERY);

        if (null === $query || '' === $query) {
            return $url;
        }

        $queryElements = [];
        \parse_str($query, $queryElements);

        if (!\array_key_exists($key, $queryElements)) {
            return $url;
        }

        unset($queryElements[$key]);

        if (0 === \count($queryElements)) {
            return \str_replace('?'.$query, '', $url);
        }

        return \str_replace($query, \http_build_query($queryElements), $url);
    }
}
