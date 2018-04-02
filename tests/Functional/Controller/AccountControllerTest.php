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
        $this->initDB();
        $this->logIn();
        $crawler = $this->client->request('GET', '/account/edit');
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());

        $form_values = [
            'user_edit[email]' => 'test@example.com',
            'user_edit[username]' => 'test_user',
            'user_edit[currentPassword]' => 's3cret',
            'user_edit[plainPassword][first]' => 'ChangedPassword',
            'user_edit[plainPassword][second]' => 'ChangedPassword',
        ];

        $form = $crawler->selectButton('user_edit_save')->form();

        // Test that a form submission fails if no values are inserted.
        $submit = $this->client->submit($form);
        $this->assertEquals(1, $submit->filter('html:contains("This value should be the user\'s current password.")')->count());

        // The form should fail if a single field is missing input.
        // Missing email.
        $values = $form_values;
        $values['user_edit[email]'] = '';
        $form = $crawler->selectButton('user_edit_save')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value should not be blank.")'));

        // Missing username.
        $values = $form_values;
        $values['user_edit[username]'] = '';
        $form = $crawler->selectButton('user_edit_save')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value should not be blank.")'));

        // Missing password first.
        $values = $form_values;
        $values['user_edit[plainPassword][first]'] = '';
        $form = $crawler->selectButton('user_edit_save')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value is not valid.")'));

        // Missing password second.
        $values = $form_values;
        $values['user_edit[plainPassword][second]'] = '';
        $form = $crawler->selectButton('user_edit_save')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value is not valid.")'));

        // If the passwords are not matching.
        $values = $form_values;
        $values['user_edit[plainPassword][second]'] = 'SomethingElse';
        $form = $crawler->selectButton('user_edit_save')->form();
        $submit = $this->client->submit($form, $values);
        $this->assertCount(1, $submit->filter('html:contains("This value is not valid.")'));

    }

}