<?php

declare(strict_types=1);

namespace App\Review\Google;

use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use App\Review\Fetch\FetchedReview;
use App\Review\Fetch\Fetcher;
use App\Review\Google\Entity\Token;
use App\Shared\Doctrine\Registry;
use DateTimeImmutable;
use Generator;
use Google_Service_MyBusiness;
use Google_Service_MyBusiness_Account;
use Google_Service_MyBusiness_Location;
use Google_Service_MyBusiness_Review;
use function is_int;
use function strpos;
use function substr;
use function trim;

final class GoogleFetcher implements Fetcher
{
    private Registry $registry;

    private Google_Service_MyBusiness $myBusiness;

    public function __construct(Registry $registry, Google_Service_MyBusiness $client)
    {
        $this->registry = $registry;
        $this->myBusiness = $client;
    }

    /**
     * {@inheritDoc}
     */
    public function fetch(): iterable
    {
        $token = $this->registry->manager()
            ->createQueryBuilder()
            ->select('t.payload')
            ->from(Token::class, 't')
            ->orderBy('t.id', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()['payload'];

        $this->myBusiness->getClient()->setAccessToken($token);

        foreach ($this->myBusiness->accounts->listAccounts() as $listAccount) {
            yield from $this->account($listAccount);
        }
    }

    private function account(Google_Service_MyBusiness_Account $account): Generator
    {
        foreach ($this->myBusiness->accounts_locations->listAccountsLocations($account->name) as $location) {
            yield from $this->location($location);
        }
    }

    private function location(Google_Service_MyBusiness_Location $location): Generator
    {
        $pageToken = null;

        do {
            $listReviewsResponse = $this->myBusiness->accounts_locations_reviews->listAccountsLocationsReviews(
                $location->name,
                [
                    'orderBy' => 'update_time desc',
                    'pageToken' => $pageToken,
                ],
            );

            foreach ($listReviewsResponse as $review) {
                yield from $this->review($review);
            }

            $pageToken = $listReviewsResponse->getNextPageToken();
        } while (null !== $pageToken);
    }

    private function review(Google_Service_MyBusiness_Review $review): Generator
    {
        $comment = $review['comment'] ?? '';
        $transPos = strpos($comment, '(Translated by Google)');

        if (is_int($transPos)) {
            $comment = substr($comment, 0, $transPos);
        }

        yield new FetchedReview(
            $review['reviewId'],
            ReviewSource::google(),
            $review['reviewer']['displayName'],
            trim($comment),
            ReviewRating::fromGoogleValue($review['starRating']),
            new DateTimeImmutable($review['createTime'] ?? $review['updatedTime']),
            (array) $review->toSimpleObject(),
        );
    }
}
