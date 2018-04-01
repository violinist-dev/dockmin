<?php

namespace App\Tests\Functional\Controller;


use App\Entity\ServerCredential;
use App\Tests\WebTestCase;

class ServerCredentialsControllerTest extends WebTestCase
{

    /**
     * Test that the index page for Server Credentials works.
     */
    public function testIndex()
    {
        $this->initDB();
        $this->logIn();

        $server_credential = new ServerCredential();
        $server_credential->setOwner($this->loggedInUser);
        $server_credential->setName('Server Credential Test');
        $this->saveServerCredential($server_credential);

        $crawler = $this->client->request('GET', '/server-credential');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("Server Credential Test")'));
    }

    /**
     * Tests that we can add a Server Credential.
     */
    public function testAdd()
    {
        $this->initDB();
        $this->logIn();
        $crawler = $this->client->request('GET', '/server-credential/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('server_credential_save')->form();
        $crawler = $this->client->submit($form, ['server_credential[username]' => 'username', 'server_credential[name]' => 'Server Credential Test', 'server_credential[currentPassword]' => 's3cret']);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to /server-credential")'));
    }

    /**
     * Test that we can delete a Server Credential.
     */
    public function testDelete()
    {
        $this->initDB();
        $this->logIn();

        $server_credential = new ServerCredential();
        $server_credential->setOwner($this->loggedInUser);
        $server_credential->setName('Server Credential Test');
        $this->saveServerCredential($server_credential);

        $crawler = $this->client->request('GET', '/server-credential/delete/' . $server_credential->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('form_confirmation')->form();
        $crawler = $this->client->submit($form, ['form[currentPassword]' => 's3cret']);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to /server")'));
    }

}