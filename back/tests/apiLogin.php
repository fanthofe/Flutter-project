<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;

class apiLogin extends WebTestCase
{       
    private $url = 'https://127.0.0.1:8000';

    public function testOnKernelRequestWithAuthentication()
    {   
        $client = new Client();
        $response = $client->request('POST', $this->url . '/auth' , [
            'json' => [
                'email' => 'admin@obaby.fr',
                'password' => 'password',
            ],
        ]);
        $this->assertSame(Response::HTTP_OK, $response->getStatusCode());
        
        $data = json_decode($response->getBody(), true);
        $this->assertIsArray($data);
        $this->assertArrayHasKey('token', $data);
        $this->assertNotNull($data['token']);
        $this->assertArrayHasKey('user', $data);
        $this->assertIsArray($data['user']);
        $this->assertArrayHasKey('id', $data['user']);
        $this->assertArrayHasKey('email', $data['user']);
        $this->assertArrayHasKey('firstName', $data['user']);
        $this->assertArrayHasKey('lastName', $data['user']);
        $this->assertArrayHasKey('isSubscriber', $data['user']);
        $this->assertArrayHasKey('parent', $data['user']);
        foreach ($data['user'] as $key => $value) {
            $this->assertNotNull($value);
        }

        $usersResponse = $client->request('GET', $this->url . '/api/users', [
            'headers' => [
                'Authorization' => 'Bearer ' . $data['token'],
            ],
        ]);
        $this->assertSame(Response::HTTP_OK, $usersResponse->getStatusCode());
    }
}
