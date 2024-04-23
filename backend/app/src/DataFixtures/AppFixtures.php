<?php

namespace App\DataFixtures;

use App\Entity\Admin;
use App\Entity\Comment;
use App\Entity\User;
use App\Entity\Topic;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $admin = new Admin();
        $admin->setEmail('jancsik.balazs@gmail.com');
        $admin->setPassword('12345678');
        $admin->setName('JBZ');
        $admin->setStatus(1);
        $manager->persist($admin);

        $attila = new User();
        $attila->setEmail('erdos.attila@bitninja.io');
        $attila->setPassword('12345678');
        $attila->setName('Erdős Attila');
        $manager->persist($attila);
        
        $marci = new User();
        $marci->setEmail('marcel.csendes@bitninja.io');
        $marci->setPassword('12345678');
        $marci->setName('Csendes Marcel');
        $manager->persist($marci);

        $balazs = new User();
        $balazs->setEmail('jancsik.balazs@gmail.com');
        $balazs->setPassword('12345678');
        $balazs->setName('Erdős Attila');
        $manager->persist($balazs);

        $attila2 = new User();
        $attila2->setEmail('erdos.attila2@bitninja.io');
        $attila2->setPassword('12345678');
        $attila2->setName('Erdős Attila');
        $manager->persist($attila2);

        $topic = new Topic();
        $topic->setDescription('Mikor lesz cs2? :D');
        $topic->setName('CS2 when');
        $topic->setUser($attila);
        $manager->persist($topic);

        $comment = new Comment();
        $comment->setMessage('Holnap?');
        $comment->setOriginal('Holnap?');
        $comment->setTopic($topic);
        $comment->setUser($attila);
        $manager->persist($comment);

        $comment2 = new Comment();
        $comment2->setMessage('Nekem jó!');
        $comment2->setOriginal('Nekem jó!');
        $comment2->setTopic($topic);
        $comment2->setUser($attila2);
        $manager->persist($comment2);

        $comment3 = new Comment();
        $comment3->setMessage('Én inkább rakiznék!');
        $comment3->setOriginal('Én inkább rakiznék!');
        $comment3->setTopic($topic);
        $comment3->setUser($marci);
        $manager->persist($comment3);

        $manager->flush();
    }
}
