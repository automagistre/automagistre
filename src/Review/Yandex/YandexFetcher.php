<?php

declare(strict_types=1);

namespace App\Review\Yandex;

use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use App\Review\Fetch\FetchedReview;
use App\Review\Fetch\Fetcher;
use App\Tenant\Tenant;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use function explode;
use function str_starts_with;
use function trim;

final class YandexFetcher implements Fetcher
{
    private const PAGE_SIZE = 50;
    private const URL = 'https://yandex.ru/maps/api/business/fetchReviews';

    private HttpClientInterface $httpClient;

    private Tenant $tenant;

    public function __construct(HttpClientInterface $httpClient, Tenant $tenant)
    {
        $this->httpClient = $httpClient;
        $this->tenant = $tenant;
    }

    /**
     * {@inheritdoc}
     */
    public function fetch(): iterable
    {
        if (null === $this->tenant->toYandexMapBusinessId()) {
            return;
        }

        [$yandexUid, $csrfToken] = $this->preFlight();
        $page = 1;

        $response = $this->request($yandexUid, $csrfToken, $page);

        while (null !== $response) {
            $data = $response->toArray()['data'];

            foreach ($data['reviews'] ?? [] as $payload) {
                yield new FetchedReview(
                    $payload['reviewId'],
                    ReviewSource::yandex(),
                    $payload['author']['name'] ?? '',
                    trim($payload['text']),
                    ReviewRating::create($payload['rating']),
                    new DateTimeImmutable($payload['updatedTime']),
                    $payload,
                );
            }

            if ($data['params']['totalPages'] > $page) {
                ++$page;

                $response = $this->request($yandexUid, $csrfToken, $page);

                continue;
            }

            $response = null;
        }
    }

    private function request(string $yandexUid, string $csrfToken, int $page): ResponseInterface
    {
        return $this->httpClient->request('POST', self::URL, [
            'headers' => [
                'Cookie' => [
                    $yandexUid,
                ],
            ],
            'query' => [
                'businessId' => $this->tenant->toYandexMapBusinessId(),
                'csrfToken' => $csrfToken,
                'page' => $page,
                'pageSize' => self::PAGE_SIZE,
                'ranking' => 'by_time',
            ],
        ]);
    }

    private function preFlight(): array
    {
        $response = $this->httpClient->request('GET', self::URL);

        $yandexuid = '';
        foreach ($response->getHeaders() as $header => $value) {
            if ('set-cookie' !== $header) {
                continue;
            }

            foreach ($value as $item) {
                if (!str_starts_with($item, 'yandexuid')) {
                    continue;
                }

                $yandexuid = explode(';', $item)[0];

                break;
            }
        }

        $csrfToken = $response->toArray()['csrfToken'];

        return [$yandexuid, $csrfToken];
    }
}
