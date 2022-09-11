<?php

namespace App\DataFixtures;

use App\Entity\JobPost;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        $admin = new User();
        $pass = $this->encoder->encodePassword($admin, '123456');
        $admin
            ->setUsername('polo')
            ->setRoles(['ROLE_ADMIN'])
            ->setEmail('myemail@gmail.com')
            ->setActive(true)
            ->setToken(null)
            ->setPassword($pass);

        $manager->persist($admin);

        for ($i = 1; $i <= 5; $i++) {
            $user = new User();
            $pass = $this->encoder->encodePassword($user, '123456');
            $user
                ->setUsername($faker->name)
                ->setEmail($faker->email)
                ->setActive(true)
                ->setToken(null)
                ->setPassword($pass);

            $manager->persist($user);
        }

        for ($i = 1; $i <= 10; $i++) {
            $job = new JobPost();
            $job
                ->setTitle($faker->name)
                ->setCompany($faker->name)
                ->setContract('full-time')
                ->setExperience($faker->numberBetween(0, 10))
                ->setLocation($faker->city)
                ->setDescription($faker->text);

            $manager->persist($job);
        }

        $manager->flush();
    }
}

