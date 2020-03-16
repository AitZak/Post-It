<?php


namespace App\DataFixtures;


use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    const MAX_COMMENT = 150;

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        for ($i = 0; $i < self::MAX_COMMENT; $i++) {
            $comment = new Comment();
            $comment->setMessage($faker->paragraph);
            $comment->setUser($this->getReference('USER_'.rand(0, UserFixtures::MAX_USER-1)));
            $comment->setContent($this->getReference('CONTENT_'.rand(0, ContentFixtures::MAX_CONTENT-1)));
            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            UserFixtures::class,
            ContentFixtures::class,
        );
    }
}