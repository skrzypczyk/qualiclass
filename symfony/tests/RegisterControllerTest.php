<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterControllerTest extends WebTestCase
{
    public function testRegistrationPageLoadsSuccessfully(): void
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $this->assertResponseIsSuccessful();
    }

    public function testSubmitRegistrationForm(): void
    {

        $client = static::createClient();
        $crawler = $client->request('GET', '/register');

        $form = $crawler->selectButton("S'inscrire")->form();

        $form['registration_form[firstName]'] = 'Jean';
        $form['registration_form[lastName]'] = 'Dupont';
        $form['registration_form[email]'] = 'jean.dupont@example.com';
        $form['registration_form[plainPassword][first]'] = 'MotDePasse123';
        $form['registration_form[plainPassword][second]'] = 'MotDePasse123';
        $form['registration_form[agreeTerms]'] = true;

        $client->submit($form);

        // On s'attend à être redirigé (vers home ou confirmation)
        $this->assertResponseRedirects();

        // Suivre la redirection pour vérifier le message flash
        $client->followRedirect();
        $this->assertSelectorTextContains('body', 'Veuillez activer votre compte');
    }
}
