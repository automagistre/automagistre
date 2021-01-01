<?php

declare(strict_types=1);

namespace App\Google\Command;

use App\Google\Entity\Review;
use App\Google\Entity\Token;
use App\Shared\Doctrine\Registry;
use Google_Service_MyBusiness;
use Google_Service_MyBusiness_Account;
use Google_Service_MyBusiness_Location;
use Google_Service_MyBusiness_Review;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

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

        $this->myBusiness->getClient()->setAccessToken($token['access_token']);

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
        $exists = $conn->fetchOne('SELECT 1 FROM google_review WHERE review_id = :reviewId', [
            'reviewId' => $reviewId,
        ]);
        if (1 === $exists) {
            return;
        }

        $this->registry->add(
            Review::create(
                $reviewId,
                (array) $review->toSimpleObject(),
            ),
        );
    }
}
