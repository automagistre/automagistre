<?php

declare(strict_types=1);

namespace App\Review\Google\Command;

use App\Review\Entity\Review;
use App\Review\Enum\ReviewRating;
use App\Review\Enum\ReviewSource;
use App\Review\Google\Entity\Token;
use App\Shared\Doctrine\Registry;
use DateTimeImmutable;
use Google_Service_MyBusiness;
use Google_Service_MyBusiness_Account;
use Google_Service_MyBusiness_Location;
use Google_Service_MyBusiness_Review;
use function is_int;
use function strpos;
use function substr;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function trim;

final class GoogleReviewCommand extends Command
{
    protected static $defaultName = 'google:review:fetch';

    private Registry $registry;

    private Google_Service_MyBusiness $myBusiness;

    public function __construct(Registry $registry, Google_Service_MyBusiness $client)
    {
        parent::__construct();

        $this->registry = $registry;
        $this->myBusiness = $client;
    }

    /**
     * {@inheritDoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
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
            $this->account($listAccount);
        }

        return 0;
    }

    private function account(Google_Service_MyBusiness_Account $account): void
    {
        foreach ($this->myBusiness->accounts_locations->listAccountsLocations($account->name) as $location) {
            $this->location($location);
        }
    }

    private function location(Google_Service_MyBusiness_Location $location): void
    {
        $pageToken = null;
        do {
            $listReviewsResponse = $this->myBusiness->accounts_locations_reviews->listAccountsLocationsReviews(
                $location->name,
                [
                    'orderBy' => 'update_time desc',
                    'pageToken' => $pageToken,
                ]
            );

            foreach ($listReviewsResponse as $review) {
                $this->review($review);
            }

            $pageToken = $listReviewsResponse->getNextPageToken();
        } while (null !== $pageToken);
    }

    private function review(Google_Service_MyBusiness_Review $review): void
    {
        $reviewId = $review->getReviewId();

        $conn = $this->registry->connection();
        $exists = $conn->fetchOne('SELECT 1 FROM review WHERE source = :source AND source_id = :sourceId', [
            'source' => ReviewSource::google(),
            'sourceId' => $reviewId,
        ]);
        if (1 === $exists) {
            return;
        }

        $payload = (array) $review->toSimpleObject();

        $comment = $payload['comment'] ?? '';
        $transPos = strpos($comment, '(Translated by Google)');
        if (is_int($transPos)) {
            $comment = trim(substr($comment, 0, $transPos));
        }

        $this->registry->add(
            new Review(
                $reviewId,
                $payload['reviewId'],
                ReviewSource::google(),
                $payload['reviewer']['displayName'],
                $comment,
                ReviewRating::fromGoogleValue($payload['starRating']),
                new DateTimeImmutable($payload['createTime'] ?? $payload['updatedTime']),
                $payload,
            ),
        );
    }
}
