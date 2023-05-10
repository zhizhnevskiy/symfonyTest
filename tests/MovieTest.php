<?php

namespace App\Tests;

use App\Entity\Movie;
use PHPUnit\Framework\TestCase;

class MovieTest extends TestCase
{
    public function testSetTitle()
    {
        $movie = new Movie();
        $title = "Sample Movie Title";
        $movie->setTitle($title);

        $this->assertEquals($title, $movie->getTitle());
    }

    public function testSetPrice()
    {
        $movie = new Movie();
        $price = 9.99;
        $movie->setPrice($price);

        $this->assertEquals($price, $movie->getPrice());
    }

    public function testSetVat()
    {
        $movie = new Movie();
        $vat = 0.21;
        $movie->setVat($vat);

        $this->assertEquals($vat, $movie->getVat());
    }

    public function testSetDescription()
    {
        $movie = new Movie();
        $description = "Sample movie description";
        $movie->setDescription($description);

        $this->assertEquals($description, $movie->getDescription());
    }
}