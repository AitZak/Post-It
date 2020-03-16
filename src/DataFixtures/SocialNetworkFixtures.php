<?php


namespace App\DataFixtures;


use App\Entity\SocialNetwork;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class SocialNetworkFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $socialNetwork = new SocialNetwork();
        $socialNetwork->setName('Twitter');
        $socialNetwork->setClientId(sha1(mt_rand(1, 90000) . 'SALT'));
        $socialNetwork->setClientSecret(sha1(mt_rand(1, 90000) . 'SALT'));
        $manager->persist($socialNetwork);

        $socialNetwork = new SocialNetwork();
        $socialNetwork->setName('Tumblr');
        $socialNetwork->setClientId(sha1(mt_rand(1, 90000) . 'SALT'));
        $socialNetwork->setClientSecret(sha1(mt_rand(1, 90000) . 'SALT'));
        $manager->persist($socialNetwork);

        $socialNetwork = new SocialNetwork();
        $socialNetwork->setName('YouTube');
        $socialNetwork->setClientId(sha1(mt_rand(1, 90000) . 'SALT'));
        $socialNetwork->setClientSecret(sha1(mt_rand(1, 90000) . 'SALT'));
        $manager->persist($socialNetwork);

        $manager->flush();
    }
}