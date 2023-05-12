<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieApiControllerTest extends WebTestCase
{
    public function testCreateMovie()
    {
        $client = static::createClient();

        $data = [
            'title' => 'Test Movie',
            'price' => 9.99,
            'vat' => 1.21,
            'description' => 'This is a test movie',
        ];

        $client->request('POST', '/api/movies', [], [], [], json_encode($data));

        $this->assertSame(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        // Additional assertions for the created movie, if needed
    }

    public function testUpdateMovie()
    {
        $client = static::createClient();

        $data = [
            'title' => 'Updated Movie',
            'price' => 9.99,
            'vat' => 1.21,
            'description' => 'This is a test movie',
        ];

        $movieId = 2; // Replace with the ID of the movie you want to update

        $client->request('PUT', '/api/movies/' . $movieId, [], [], [], json_encode($data));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        // Additional assertions for the updated movie, if needed
    }

    public function testDeleteMovie()
    {
        $client = static::createClient();

        $movieId = 4; // Replace with the ID of the movie you want to delete

        $client->request('DELETE', '/api/movies/' . $movieId);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        // Additional assertions for the deleted movie, if needed
    }
}