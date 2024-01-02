<?php

namespace App\Tests\Controller\Security;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuthControllerTest extends WebTestCase
{
    public function testLoginPageIsSuccessful()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Page de connexion');
    }

    public function testLoginFormIsDisplayed()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $this->assertCount(1, $crawler->filter('form'));
    }

    public function testLoginWithValidCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('login')->form();
        $form['email'] = 'admin@obaby.fr';
        $form['password'] = 'password';

        $client->submit($form);

        $this->assertResponseRedirects('/admin');
    }

    public function testLoginWithInvalidCredentials()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/login');

        $form = $crawler->selectButton('login')->form();
        $form['email'] = 'invalid_username';
        $form['password'] = 'invalid_password';

        $client->submit($form);

        $this->assertResponseRedirects('/login');
        // $this->assertSelectorTextContains('div', 'Identifiants invalides.');
    }

    public function testLogoutIsSuccessful()
    {
        $client = static::createClient();
        $client->request('GET', '/logout');

        $this->assertResponseRedirects('/login');
    }
}

