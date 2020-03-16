<?php


namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    const MAX_USER = 25;
    private $passwordEncoder;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder   )
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $admin = new User();
        $admin->setLastname($faker->lastName);
        $admin->setFirstname($faker->firstName);
        $admin->setEmail('admin@admin.fr');
        $admin->setPassword($this->passwordEncoder->encodePassword($admin, 'admin'));
        $admin->setRoles(['ROLE_ADMIN']);
        $manager->persist($admin);
        $this->addReference('USER_0', $admin);

        for ($i = 1; $i < 5; $i++) {
            $communicator = new User();
            $communicator->setLastname($faker->lastName);
            $communicator->setFirstname($faker->firstName);
            $communicator->setEmail($faker->email);
            $communicator->setPassword($this->passwordEncoder->encodePassword($communicator, 'password'));
            $communicator->setRoles(['ROLE_COMM']);
            $manager->persist($communicator);
            $this->addReference('USER_'.$i, $communicator);
        }

        for ($i = 5; $i < 10; $i++) {
            $reviewer = new User();
            $reviewer->setLastname($faker->lastName);
            $reviewer->setFirstname($faker->firstName);
            $reviewer->setEmail($faker->email);
            $reviewer->setPassword($this->passwordEncoder->encodePassword($reviewer, 'password'));
            $reviewer->setRoles(['ROLE_REVIEWER']);
            $manager->persist($reviewer);
            $this->addReference('USER_'.$i, $reviewer);
        }

        for ($i = 10; $i < self::MAX_USER; $i++) {
            $user = new User();
            $user->setLastname($faker->lastName);
            $user->setFirstname($faker->firstName);
            $user->setEmail($faker->email);
            $user->setPassword($this->passwordEncoder->encodePassword($user, 'password'));
            $user->setRoles(['ROLE_USER']);
            $manager->persist($user);
            $this->addReference('USER_'.$i, $user);
        }

        $manager->flush();
    }
}