<?php


namespace App\DataFixtures;


use App\Entity\Content;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Bundle\SecurityBundle\DependencyInjection\Security\UserProvider\UserProviderFactoryInterface;

class ContentFixtures extends Fixture implements DependentFixtureInterface
{
    const MAX_CONTENT = 50;
    private $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Load data fixtures with the passed EntityManager
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();
        $date = new \DateTime();

        for ($i = 0; $i < self::MAX_CONTENT; $i++) {
            $content = new Content();
            $content->setTitle($faker->sentence);
            $content->setDescription($faker->text);
            $content->setUserSubmit($this->getReference('USER_'.rand(0, UserFixtures::MAX_USER-1)));
            $content->setSubmitDate($faker->dateTimeThisMonth());
            $content->setStatut($statut = rand(0, 3));
            if ($statut == 1 || $statut == 2) {
                $content->setApprovalDate($date);
            }
            if ($statut == 3) {
                $content->setApprovalDate($date->modify(date('Y-m-d H:i:s', strtotime('-1 hour'))));
                $content->setPublicationDate($date);
            }
            if (rand(0,100) % 5 == 0){
                $content->setFile('https://i.picsum.photos/id/'.rand(0,200).'/200/300.jpg');
            }
            $manager->persist($content);
            $this->addReference('CONTENT_'.$i, $content);
        }

        $manager->flush();

    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
        );
    }
}