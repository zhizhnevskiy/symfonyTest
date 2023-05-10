<?php

namespace App\Controller\Api;

use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Request\MovieRequest;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/movies", name="api_movies")
 */
class MovieApiController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ValidatorInterface $validator;

    public function __construct(
        EntityManagerInterface $entityManager,
        ValidatorInterface     $validator
    )
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @Route("", name="create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $movie = new Movie();
        $movie->setTitle($data['title']);
        $movie->setPrice($data['price']);
        $movie->setVat($data['vat']);
        $movie->setDescription($data['description']);
        $this->entityManager->persist($movie);
        $this->entityManager->flush();

        $jsonContent = $serializer->serialize($movie, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($jsonContent, Response::HTTP_CREATED, [], true);
    }

    /**
     * @Route("/{id}", name="movie_update", methods={"PUT"})
     */
    public function update(Request $request, ? Movie $movie, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        if (!$movie){
            return new JsonResponse("Please, change id for Movie", Response::HTTP_NOT_FOUND);
        }

        $data = json_decode($request->getContent(), true);

        $errors = $this->validateRequest($data);

        if (count($errors) > 0) {
            return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $movie->setTitle($data['title'] ?? $movie->getTitle());
        $movie->setPrice($data['price'] ?? $movie->getPrice());
        $movie->setVat($data['vat'] ?? $movie->getVat());
        $movie->setDescription($data['description'] ?? $movie->getDescription());
        $this->entityManager->flush();

        $jsonContent = $serializer->serialize($movie, 'json', [
            'circular_reference_handler' => function ($object) {
                return $object->getId();
            }
        ]);

        return new JsonResponse($jsonContent, Response::HTTP_OK, [], true);
    }

    /**
     * @Route("/{id}", name="movie_delete", methods={"DELETE"})
     */
    public function delete(?Movie $movie): JsonResponse
    {
        if (!$movie){
            return new JsonResponse("Please, change id for Movie", Response::HTTP_NOT_FOUND);
        }

        $this->entityManager->remove($movie);
        $this->entityManager->flush();

        return new JsonResponse("Movie has been deleted", Response::HTTP_OK);
    }

    private function validateRequest($data): array
    {
        $movieRequest = new MovieRequest();
        $movieRequest->title = $data['title'] ?? null;
        $movieRequest->price = $data['price'] ?? null;
        $movieRequest->vat = $data['vat'] ?? null;
        $movieRequest->description = $data['description'] ?? null;

        $errors = $this->validator->validate($movieRequest);
        $errorMessages = [];

        foreach ($errors as $error) {
            $errorMessages[] = $error->getPropertyPath() . ": " . $error->getMessage();
        }

        return $errorMessages;
    }
}
