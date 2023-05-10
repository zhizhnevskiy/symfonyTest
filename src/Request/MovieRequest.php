<?php

namespace App\Request;

use Symfony\Component\Validator\Constraints as Assert;

class MovieRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $title;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="numeric")
     */
    public $price;

    /**
     * @Assert\NotBlank
     * @Assert\Range(
     *      min = 1,
     *      max = 100,
     *      notInRangeMessage = "The value must be between {{ min }} and {{ max }}.",
     * )
     */
    public $vat;

    /**
     * @Assert\NotBlank
     * @Assert\Type(type="string")
     */
    public $description;
}