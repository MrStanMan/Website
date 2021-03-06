<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * AppFixtures constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(userPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setName('Admin');
        $user->setEmail('admin@fizz.nl');
        $user->setRoles(array('ROLE_SUPER_ADMIN'));
        $user->setActive(1);

        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();


        $user = new User();
        $user->setName('Stan');
        $user->setEmail('stan@fizz.nl');
        $user->setRoles(array('ROLE_ADMIN'));
        $user->setActive(1);

        $password = $this->encoder->encodePassword($user, 'Rektnub');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();


        $user = new User();
        $user->setName('User');
        $user->setEmail('user@fizz.nl');
        $user->setRoles(array('ROLE_USER'));
        $user->setActive(1);

        $password = $this->encoder->encodePassword($user, 'password');
        $user->setPassword($password);

        $manager->persist($user);
        $manager->flush();
    }
}
