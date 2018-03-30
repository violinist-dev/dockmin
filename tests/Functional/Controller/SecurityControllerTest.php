<?php

namespace App\Tests\Functional\Controller;

use App\Tests\WebTestCase;

/**
 * Class SecurityControllerTest.
 *
 * @package App\Tests\Functional\Controller
 */
class SecurityControllerTest extends WebTestCase
{
    public function testLogin()
    {
        parent::initDB();

        // Create a user through the register form.
        $crawler = $this->client->request('GET', '/register');
        $form = $crawler->selectButton('register-submit')->form();
        $form_values = [
            'user_register[email]' => 'test@example.com',
            'user_register[username]' => 'test_user',
            'user_register[plainPassword][first]' => 's3cret',
            'user_register[plainPassword][second]' => 's3cret',
        ];
        $this->client->submit($form, $form_values);

        $this->client->request('GET', '/login');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        // Test the login form, with the known username and password.
        // Wrong credentials.
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('login-submit')->form();
        $crawler = $this->client->submit($form, ['_username' => 'test_user', '_password' => 'WrongPassword']);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to http://localhost/login")'));

        // Correct credentials.
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('login-submit')->form();
        $crawler = $this->client->submit($form, ['_username' => 'test_user', '_password' => 's3cret']);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $crawler->filter('html:contains("Redirecting to http://localhost/server")'));
    }

    public function testRegister()
    {
        parent::initDB();
        $crawler = $this->client->request('GET', '/register');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form_values = [
            'user_register[email]' => 'test@example.com',
            'user_register[username]' => 'test_user',
            'user_register[plainPassword][first]' => 's3cret',
            'user_register[plainPassword][second]' => 's3cret',
        ];

        $form = $crawler->selectButton('register-submit')->form();

        // Test that a form submission fails if no values are inserted.
        $submit = $this->client->submit($form);
        $this->assertEquals(1, $submit->filter('html:contains("This value should not be blank.")')->count());

        // The form should fail if a single field is missing input.
        // Missing email.
        $values = $form_values;
        unset($values['user_register[email]']);
        $form = $crawler->selectButton('register-submit')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value should not be blank.")'));

        // Missing username.
        $values = $form_values;
        unset($values['user_register[username]']);
        $form = $crawler->selectButton('register-submit')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value should not be blank.")'));

        // Missing password first.
        $values = $form_values;
        unset($values['user_register[plainPassword][first]']);
        $form = $crawler->selectButton('register-submit')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value is not valid.")'));

        // Missing password second.
        $values = $form_values;
        unset($values['user_register[plainPassword][second]']);
        $form = $crawler->selectButton('register-submit')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value is not valid.")'));

        // If the passwords are not matching.
        $values = $form_values;
        $values['user_register[plainPassword][second]'] = 'SomethingElse';
        $form = $crawler->selectButton('register-submit')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value is not valid.")'));

        // If all values are valid we should be able to process a submission.
        $values = $form_values;
        $form = $crawler->selectButton('register-submit')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertTrue($this->client->getResponse()->isRedirection());
        $this->assertCount(1, $submit->filter('html:contains("Redirecting to /login")'));

    }
}