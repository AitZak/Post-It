<?php


namespace App\DataFixtures;


use App\Entity\Approval;
use App\Manager\ApprovalManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ApprovalFixtures extends Fixture implements DependentFixtureInterface
{
    const MAX_APPROVAL = 150;
    private $approvalManager;

    public function __construct(ApprovalManager $approvalManager)
    {
        $this->approvalManager = $approvalManager;
    }

    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < self::MAX_APPROVAL; $i++){
            $approval = new Approval();
            $approval->setContent($this->getReference('CONTENT_'.rand(0, ContentFixtures::MAX_CONTENT-1)));
            $approval->setUser($this->getReference('USER_'.rand(0, UserFixtures::MAX_USER-1)));
            $approval->setStatus(random_int(1, 2));

            $review = $this->approvalManager->alreadyReviewed($approval);

            if (!$review) {
                $manager->persist($approval);
                $manager->flush();
            }
        }

    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ContentFixtures::class,
        );
    }
}