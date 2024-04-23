<?php

namespace App\Tests\Controller;

use App\Repository\UserRepository;
use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;

class UsersController extends ApiTestCase
{

    private function getUser($email = "erdos.attila@bitninja.io")
    {
        $userRepository = static::getContainer()->get(UserRepository::class);
        return $userRepository->findOneByEmail($email);
    }

    public function testUsersEndpointsWithoutUser(): void
    {
        $userId = $this->getUser()->getId();

        $endpoints = [
            'GET' => '/api/v1/users/' . $userId,
            'PATCH' => '/api/v1/users/' . $userId,
            'DELETE' => '/api/v1/users/' . $userId,
        ];

        $client = static::createClient();

        foreach ($endpoints as $method => $endpoint) {
            $client->request($method, $endpoint);
            $this->assertResponseStatusCodeSame(401);
        }
    }

    public function testNewUsersWithLoggedInUser(): void
    {
        $client = static::createClient();

        $client->request('POST', '/api/v1/users', [
            "json" => [
                'email' => 'randomEmail@randommail.com',
                'name' => 'test',
                'password' => "1234567"
            ]
        ]);

        $this->assertResponseStatusCodeSame(201);
    }

    public function testShowUsersWithLoggedInUser(): void
    {
        $user = $this->getUser();
        $client = static::createClient();
        $client->loginUser($user);

        $client->request('GET', '/api/v1/users/' . $user->getId());

        $this->assertResponseStatusCodeSame(200);
    }

    public function testShowUsersWithAnotherLoggedInUser(): void
    {
        $user = $this->getUser('erdos.attila2@bitninja.io');
        $anotherUser = $this->getUser();
        $client = static::createClient();
        $client->loginUser($user);

        $reponse = $client->request('GET', '/api/v1/users/' . $anotherUser->getId());

        $data = json_decode($reponse->getContent(), true);

        $this->assertResponseStatusCodeSame(200);
        $this->assertArrayNotHasKey('email', $data);
    }

    public function testEditUsersWithLoggedInUser(): void
    {
        $user = $this->getUser();
        $client = static::createClient();
        $client->loginUser($user);


        $client->request('PATCH', '/api/v1/users/' . $user->getId(), [
            "json" => [
                'name' => 'test',
                'description' => 'test',
                'status' => 1
            ]
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testDeleteUsersWithLoggedInUser(): void
    {
        $user = $this->getUser();
        $client = static::createClient();
        $client->loginUser($user);

        $client->request('DELETE', '/api/v1/users/' . $user->getId());

        $this->assertResponseIsSuccessful();
    }

    public function testDeleteUsersWithAnotherLoggedInUser(): void
    {
        $user = $this->getUser();
        $anotherUser = $this->getUser('erdos.attila2@bitninja.io');
        $client = static::createClient();
        $client->loginUser($user);

        $client->request('DELETE', '/api/v1/users/' . $anotherUser->getId());

        $this->assertResponseStatusCodeSame(403);
    }
}
