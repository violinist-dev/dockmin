<?php

namespace App\Tests\Functional\Controller;

use App\Tests\WebTestCase;

class AccountControllerTest extends WebTestCase
{

    /**
     * Verify that a user can see its account overview page.
     */
    public function testIndex()
    {
        $this->logIn();
        $this->client->request('GET', '/account');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testEdit()
    {
        $this->logIn();
        $this->client->request('GET', '/account/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

}