<?php

declare(strict_types=1);

namespace App\Review\Yell;

use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use App\Review\Fetch\FetchedReview;
use App\Review\Fetch\Fetcher;
use DateTimeImmutable;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class YellFetcher implements Fetcher
{
    private HttpClientInterface $httpClient;

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(): iterable
    {
        $array = $this->httpClient->request('POST', 'https://www.yell.ru/api/v2/review/list/', [
            'headers' => [
                'User-Agent' => 'Yell-Mobile-Android',
                'X-Yell-Mobile-Token' => 'MjBlMzBhZmE0MWU0NmUzYTBiOWI0OWJiZjExOTFjYTM=',
                'X-Yell-Api-Language' => 'en',
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            'body' => [
                'company_id' => 11919722,
                'skip' => 0,
            ],
        ])->toArray();

        foreach ($array['list'] ?? [] as $item) {
            yield new FetchedReview(
                (string) $item['id'],
                ReviewSource::yell(),
                $item['user']['name'] ?? 'Аноним',
                $item['text'],
                ReviewRating::create($item['score']),
                (new DateTimeImmutable())->setTimestamp($item['added']),
                $item,
            );
        }
    }
}
