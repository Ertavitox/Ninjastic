<?php

namespace App\DataFixtures;

use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Topic;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('erdos.attila@bitninja.io');
        $user->setPassword('12345678');
        $user->setName('Erdős Attila');
        $manager->persist($user);

        $user2 = new User();
        $user2->setEmail('erdos.attila2@bitninja.io');
        $user2->setPassword('12345678');
        $user2->setName('Erdős Attila');
        $manager->persist($user2);

        $topic = new Topic();
        $topic->setDescription('test');
        $topic->setName('test');
        $topic->setUser($user);
        $manager->persist($topic);

        $comment = new Comment();
        $comment->setMessage('test');
        $comment->setOriginal('test');
        $comment->setTopic($topic);
        $comment->setUser($user);
        $manager->persist($comment);

        $comment2 = new Comment();
        $comment2->setMessage('test');
        $comment2->setOriginal('test');
        $comment2->setTopic($topic);
        $comment2->setUser($user2);
        $manager->persist($comment2);

        $manager->flush();
    }
}
