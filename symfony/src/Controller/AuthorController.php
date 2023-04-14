<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/author')]
class AuthorController extends AbstractController
{
    #[Route('/list', name: 'author_list')]
    public function index(AuthorRepository $authorRep): JsonResponse
    {
        $authors = $authorRep->findAll();
        $responseData = [];
        foreach($authors as $a) {
            $authorBooks = [];
            foreach($a->getBooks()->getIterator() as $book) 
                $authorBooks[] = [
                    'id' => $book->getId(),
                    'title' => $book->getTitle()
                ];

            $responseData[$a->getId()] = [
                'fullname' => $a->getFullname(),
                'books' => $authorBooks
            ];
        }

        return new JsonResponse($responseData);
    }

    #[Route('/create', name: 'author_create')]
    public function create(Request $req, AuthorRepository $authorRep): JsonResponse
    {
        if ($req->getMethod() !== 'POST') {
            return new JsonResponse([
                'error' => 'method not allowed (only POST is admissible)'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        $requestData = json_decode($req->getContent(), true);
        if(!isset($requestData['fullname'])) {
            return new JsonResponse([
                'message' => 'invalid request data',
                'errors' => ['missing "fullname" parameter']
            ], Response::HTTP_BAD_REQUEST);
        }

        $author = new Author($requestData['fullname']);
        $authorRep->save($author, true);

        return new JsonResponse([
            'message' => 'ok',
            'created_author_id' => $author->getId()
        ]);
    }

    #[Route('/{id}/update', name: 'author_update')]
    public function update($id, Request $req, AuthorRepository $authorRep): JsonResponse
    {
        if ($req->getMethod() !== 'POST') {
            return new JsonResponse([
                'error' => 'method not allowed (only POST is admissible)'
            ], Response::HTTP_METHOD_NOT_ALLOWED);
        }

        if (!is_numeric($id)) {
            return new JsonResponse([
                'error' => 'invalid id (must be numeric)'
            ], Response::HTTP_BAD_REQUEST);
        }

        $requestData = json_decode($req->getContent(), true);
        if(!isset($requestData['fullname'])) {
            return new JsonResponse([
                'message' => 'invalid request data',
                'errors' => ['missing "fullname" parameter']
            ], Response::HTTP_BAD_REQUEST);
        }

        $author = $authorRep->find($id);
        if (is_null($author)) {
            return new JsonResponse([
                'error' => 'author not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $author->setFullname($requestData['fullname']);
        $authorRep->save($author, true);

        return new JsonResponse([
            'message' => 'ok',
            'updated_author_id' => $author->getId()
        ]);
    }

    #[Route('/{id}/delete', name: 'author_delete')]
    public function delete($id, Request $req, AuthorRepository $authorRep)
    {
        if (!is_numeric($id)) {
            return new JsonResponse([
                'error' => 'invalid id (must be numeric)'
            ], Response::HTTP_BAD_REQUEST);
        }

        $author = $authorRep->find($id);
        if (is_null($author)) {
            return new JsonResponse([
                'error' => 'author not found'
            ], Response::HTTP_BAD_REQUEST);
        }

        $authorRep->remove($author, true);

        return new JsonResponse([
            'message' => 'ok',
        ]);
    }
}
