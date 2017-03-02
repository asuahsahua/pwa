<?php

namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CharacterControllerTest extends WebTestCase
{
    public function testNew()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/character/new');
    }

    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/character');
    }

    public function testDelete()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/character/delete');
    }

}
