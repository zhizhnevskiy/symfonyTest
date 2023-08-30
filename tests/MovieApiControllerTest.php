<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class MovieApiControllerTest extends WebTestCase
{
    private $movieId;

    public function testMovieActions()
    {
        $client = static::createClient();

        // Create a new movie
        $data = [
            'title' => 'Test Movie',
            'price' => 9.99,
            'vat' => 1.21,
            'description' => 'This is a test movie',
        ];

        $client->request('POST', '/api/movies', [], [], [], json_encode($data));

        $this->assertSame(201, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        // Get the ID of the created movie
        $responseData = json_decode($client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->movieId = $responseData['id'];

        // Update the movie
        $data = [
            'title' => 'Updated Movie',
            'price' => 9.99,
            'vat' => 1.21,
            'description' => 'This is a test movie',
        ];

        $client->request('PUT', '/api/movies/' . $this->movieId, [], [], [], json_encode($data));

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());

        // Delete the movie
        $client->request('DELETE', '/api/movies/' . $this->movieId);

        $this->assertSame(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }
}
