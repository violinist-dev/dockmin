<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Server;
use App\Entity\ServerCredential;
use App\Tests\WebTestCase;

/**
 * Class ServerControllerTest.
 *
 * @package App\Tests\Functional\Controller
 */
class ServerControllerTest extends WebTestCase
{

    /**
     * Tests that the index page can be displayed.
     */
    public function testIndex()
    {
        $this->initDB();
        $this->logIn();

        $server_credential = new ServerCredential();
        $server_credential->setOwner($this->loggedInUser);
        $this->saveServerCredential($server_credential);

        $server = new Server();
        $server->setName('Test server on index page');
        $server->setActive(1);
        $server->setOwner($this->loggedInUser);
        $server->setIp('127.0.0.1');
        $server->setOs('Test Server OS');
        $server->setDockerInfo(['version' => "1.0"]);
        $server->setServerCredential($server_credential);
        $this->saveServer($server);

        $crawler = $this->client->request('GET', '/server');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("Test server on index page")'));
    }

    /**
     * Tests that the Add Server page works.
     */
    public function testAdd()
    {
        $this->initDB();
        $this->logIn();
        $crawler = $this->client->request('GET', '/server/add');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('server_save')->form();
        $crawler = $this->client->submit($form, ['server[ip]' => '127.0.0.1', 'server[name]' => 'Test server on index page']);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to /server")'));
    }

    /**
     * Tests that a server can be edited.
     */
    public function testEdit()
    {
        $this->initDB();
        $this->logIn();

        $server_credential = new ServerCredential();
        $server_credential->setOwner($this->loggedInUser);
        $this->saveServerCredential($server_credential);

        $server = new Server();
        $server->setActive(1);
        $server->setOwner($this->loggedInUser);
        $server->setIp('127.0.0.1');
        $server->setOs('Test Server OS');
        $server->setDockerInfo([]);
        $server->setServerCredential($server_credential);
        $this->saveServer($server);

        $crawler = $this->client->request('GET', '/server/edit/' . $server->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $form = $crawler->selectButton('server_save')->form();
        $crawler = $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to /server")'));
    }

    /**
     * Tests that a server can be deleted.
     */
    public function testDelete()
    {
        $this->initDB();
        $this->logIn();

        $server = new Server();
        $server->setActive(1);
        $server->setOwner($this->loggedInUser);
        $server->setIp('127.0.0.1');
        $server->setOs('Test Server OS');
        $server->setDockerInfo([]);
        $this->saveServer($server);

        $crawler = $this->client->request('GET', '/server/delete/' . $server->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form = $crawler->selectButton('form_confirmation')->form();
        $crawler = $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to /server")'));
    }

    /**
     * Tests that a server can be viewed.
     */
    public function testView()
    {
        $this->initDB();
        $this->logIn();

        $server = new Server();
        $server->setActive(1);
        $server->setOwner($this->loggedInUser);
        $server->setIp('127.0.0.1');
        $server->setOs('Test Server OS');
        $server->setDockerInfo([]);
        $this->saveServer($server);

        $crawler = $this->client->request('GET', '/server/view/' . $server->getId());
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
        $this->assertCount(1, $crawler->filter('html:contains("Docker info:")'));
        $this->assertCount(1, $crawler->filter('html:contains("Docker Images:")'));
        $this->assertCount(1, $crawler->filter('html:contains("Docker Containers:")'));
    }
}