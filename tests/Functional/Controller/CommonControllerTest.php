<?php

namespace App\Tests\Functional\Controller;

use App\Tests\WebTestCase;

/**
 * Class CommonControllerTest.
 *
 * @package App\Tests\Functional\Controller
 */
class CommonControllerTest extends WebTestCase
{
    public function testFrontpage()
    {
        $this->client->request('GET', '/');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}