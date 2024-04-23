<?php

namespace App\Tests\Controller;

use App\Entity\Topic;
use App\Repository\UserRepository;
use App\Repository\TopicRepository;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class TopicsController extends ApiTestCase
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

    public function testGetTopicEndpointsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());
        $client->request('GET', '/api/v1/topics');

        $this->assertResponseIsSuccessful();
    }

    public function testTopicsEndpointsWithoutUser(): void
    {

        $endpoints = [
            'GET' => '/api/v1/topics',
            'GET' => '/api/v1/topics/' . $this->topic->getId(),
            'PATCH' => '/api/v1/topics/' . $this->topic->getId(),
            'DELETE' => '/api/v1/topics/' . $this->topic->getId(),
            'POST' => '/api/v1/topics',
        ];

        $client = static::createClient();

        foreach ($endpoints as $method => $endpoint) {
            $client->request($method, $endpoint);
            $this->assertResponseStatusCodeSame(401);
        }
    }

    public function testNewTopicsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $client->request('POST', '/api/v1/topics', [
            "json" => [
                'name' => 'test',
                'description' => 'test',
                'status' => 1
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testShowTopicsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $client->request('GET', '/api/v1/topics/'.$this->topic->getId());

        $this->assertResponseStatusCodeSame(200);
    }

    public function testEditTopicsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());


        $client->request('PATCH', '/api/v1/topics/' . $this->topic->getId(), [
            "json" => [
                'name' => 'test',
                'description' => 'test',
                'status' => 1
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testDeleteTopicsWithLoggedInUser(): void
    {
        $client = static::createClient();
        $client->loginUser($this->getUser());

        $client->request('DELETE', '/api/v1/topics/' . $this->topic->getId());

        $this->assertResponseIsSuccessful();
    }
}
