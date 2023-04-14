<?php

namespace App\ResponseCreator;

use App\Entity\Book;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class BookResponseCreator
{
    public static function index_ok(array $books): JsonResponse
    {
        $responseData = [];
        foreach($books as $book) {
            $responseData[$book->getId()] = [
                'title' => $book->getTitle(),
                'description' => $book->getDescription(),
                'written_at' => $book->getWrittenAt()
            ];
        }

        return new JsonResponse($responseData);
    }

    public static function index_invalidDates(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'invalid dates format'
        ], Response::HTTP_BAD_REQUEST);
    }



    public static function book_invalidId(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'invalid id (must be numeric)'
        ], Response::HTTP_BAD_REQUEST);
    }

    public static function book_notFound(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'book not found'
        ], Response::HTTP_BAD_REQUEST);
    }

    public static function book_ok(Book $book, $authors, $genres): JsonResponse
    {
        return new JsonResponse([
            'id' => $book->getId(),
            'title' => $book->getTitle(),
            'description' => $book->getDescription(),
            'written_at' => $book->getWrittenAt(),
            'authors' => $authors,
            'genres' => $genres,
        ]);
    }



    public static function create_invalidRequest(array $errors)
    {
        return new JsonResponse([
            'message' => 'invalid request data',
            'errors' => $errors
        ], Response::HTTP_BAD_REQUEST);
    }

    public static function create_ok($bookId)
    {
        return new JsonResponse([
            'message' => 'ok',
            'created_book_id' => $bookId
        ]);
    }
    
    
    
    public static function update_invalidMethod(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'method not allowed (only POST is admissible)'
        ], Response::HTTP_METHOD_NOT_ALLOWED);
    }

    public static function update_emptyRequest(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'empty request'
        ], Response::HTTP_BAD_REQUEST);
    }

    public static function update_invalidDate(): JsonResponse
    {
        return new JsonResponse([
            'error' => 'invalid date type (it must be d.m.Y)',
            'valid_date_example' => '11.08.1998'
        ], Response::HTTP_BAD_GATEWAY);
    }

    public static function update_ok($bookId): JsonResponse
    {
        return new JsonResponse([
            'message' => 'ok',
            'id' => $bookId
        ]);
    }
}