<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use App\Entity\Topic;
use App\Repository\TopicRepository;
use Doctrine\ORM\EntityManagerInterface;

class CommentsController extends ApiTestCase
{

    private Topic $topic;

    public function setUp(): void
    {
        $topicRepository = static::getContainer()->get(TopicRepository::class);
        $this->topic = $topicRepository->findOneBy(['name' => 'test']);
    }

    private function getUser()
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneByEmail('erdos.attila@bitninja.io');
    }

    public function testCommentsEndpointsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        $client->request('GET', '/api/v1/topics/' . $this->topic->getId() . '/comments');

        $this->assertResponseIsSuccessful();
    }

    public function testCommentsEndpointsWithoutUser(): void
    {

        $endpoints = [
            'GET' => '/api/v1/topics/' . $this->topic->getId() . '/comments',
            'GET' => '/api/v1/topics/' . $this->topic->getId() . '/comments/1',
            'PATCH' => '/api/v1/topics/' . $this->topic->getId() . '/comments/1',
            'DELETE' => '/api/v1/topics/' . $this->topic->getId() . '/comments/1',
            'POST' => '/api/v1/topics/' . $this->topic->getId() . '/comments',
        ];

        $client = static::createClient();

        foreach ($endpoints as $method => $endpoint) {
            $client->request($method, $endpoint);
            $this->assertResponseStatusCodeSame(401);
        }
    }

    public function testNewCommentsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $client->request('POST', '/api/v1/topics/' . $this->topic->getId() . '/comments', [
            "json" => [
                'message' => 'test',
                'original' => 'test'
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testShowCommentsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $commentId = $this->topic->getComments()->first()->getId();

        $client->request('GET', '/api/v1/topics/' . $this->topic->getId() . "/comments/$commentId");

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEditCommentsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $commentId = $this->topic->getComments()->first()->getId();

        $client->request('PATCH', '/api/v1/topics/' . $this->topic->getId() . "/comments/$commentId", [
            'json' => [
                'message' => 'test1',
                'original' => 'test1'
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testDeleteCommentsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $commentId = $this->topic->getComments()->findFirst(function ($k,$v) {
            return $v->getUser()->getId() == $this->getUser()->getId();
        })->getId();

        $client->request('DELETE', '/api/v1/topics/' . $this->topic->getId() . "/comments/$commentId");

        $this->assertResponseIsSuccessful();
    }
}
