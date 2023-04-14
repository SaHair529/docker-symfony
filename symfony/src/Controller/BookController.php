<?php

namespace App\Controller;

use App\Entity\Author;
use App\Entity\Book;
use App\Entity\Genre;
use App\Repository\AuthorRepository;
use App\Repository\BookRepository;
use App\Repository\GenreRepository;
use App\ResponseCreator\BookResponseCreator;
use App\Validator\BookValidator;
use DateTimeImmutable;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/book')]
class BookController extends AbstractController
{
    #[Route('/list', name: 'book_list')]
    public function index(Request $req, BookRepository $bookRep): JsonResponse
    {
        if (!is_null($req->query->get('authors')) || 
            !is_null($req->query->get('genres')) ||
            !is_null($req->query->get('dates')))
        {
            $authors = $req->query->get('authors');
            $genres = $req->query->get('genres');
            $dates = $req->query->get('dates');

            $relations = [];
            if ($authors !== null) 
                $relations['authors'] = explode(',', $authors);
            if ($genres !== null) 
                $relations['genres'] = explode(',', $genres);
            if ($dates !== null) {
                $datesArray = explode('-', $dates);
                $start = DateTimeImmutable::createFromFormat('d.m.Y', $datesArray[0]);
                $end = DateTimeImmutable::createFromFormat('d.m.Y', $datesArray[1]);
                if ($start === false || $end === false) {
                    return BookResponseCreator::index_invalidDates();
                }
                $relations['dates'] = [
                    'start' => $start,
                    'end' => $end
                ];

            }
            
            return BookResponseCreator::index_ok($bookRep->findByRelations($relations));
        }
        
        return BookResponseCreator::index_ok($bookRep->findAll());
    }


    #[Route('/create', name: 'book_create')]
    public function create(
        Request $req, 
        BookRepository $bookRep,
        AuthorRepository $authorRep,
        GenreRepository $genreRep): JsonResponse
    {
        if ($req->getMethod() !== 'POST')
            return BookResponseCreator::update_invalidMethod();
        
        $requestData = json_decode($req->getContent(), true);
        $validationErrors = BookValidator::create_validateRequest($requestData);
        if (!empty($validationErrors))
            return BookResponseCreator::create_invalidRequest($validationErrors);
        
        $authors = [];
        foreach($requestData['authors'] as $currentKey => $fullname) {
            $flush = $currentKey === array_key_last($requestData['authors']);
            $author = $authorRep->findOneBy(['fullname' => $fullname])
                ?? new Author($fullname);
            
            $authorRep->save($author, $flush);
            $authors[] = $author;
        }

        $genres = [];
        foreach($requestData['genres'] as $g) {
            $flush = $currentKey === array_key_last($requestData['authors']);
            $genre = $genreRep->findOneBy(['title' => $g])
                ?? new Genre($g);
            
            $genreRep->save($genre, $flush);
            $genres[] = $genre;
        }

        $book = new Book();
        $book->setTitle($requestData['title']);
        $book->setDescription($requestData['description']);
        $book->setWrittenAt(DateTimeImmutable::createFromFormat('d.m.Y', $requestData['written_at']));

        $book->addAuthors($authors);
        $book->addGenres($genres);

        $bookRep->save($book, true);
        return BookResponseCreator::create_ok($book->getId());
    }



    #[Route('/{id}', name: 'book')]
    public function book($id, BookRepository $bookRep): JsonResponse
    {
        if (!is_numeric($id))
            return BookResponseCreator::book_invalidId();
        
        if (is_null($book = $bookRep->find($id)))
            return BookResponseCreator::book_notFound();

        $genres = '';
        foreach($book->getGenres()->getIterator() as $genre)
            $genres .= $genre->getTitle().', ';
        
        $authors = '';
        foreach($book->getAuthors()->getIterator() as $author)
            $authors .= $author->getFullname().', ';
        
        return BookResponseCreator::book_ok($book, $authors, $genres);
    }

    

    #[Route('/{id}/update', name: 'book_update')]
    public function update($id, Request $req, BookRepository $bookRep): JsonResponse
    {
        if ($req->getMethod() !== 'POST')
            return BookResponseCreator::update_invalidMethod();

        if (!is_numeric($id))
            return BookResponseCreator::book_invalidId();
        
        if (is_null($book = $bookRep->find($id)))
            return BookResponseCreator::book_notFound();

        $requestData = json_decode($req->getContent(), true);
        if (empty($requestData))
            return BookResponseCreator::update_emptyRequest();
        
        if (isset($requestData['written_at'])){
            $writtenAtDTI = DateTimeImmutable::createFromFormat('d.m.Y', $requestData['written_at']);
            if ($writtenAtDTI === false)
                return BookResponseCreator::update_invalidDate();
            
            $book->setWrittenAt($writtenAtDTI);
        }
        if (isset($requestData['title']))
            $book->setTitle($requestData['title']);
        if (isset($requestData['description']))
            $book->setDescription($requestData['description']);
        
        $bookRep->save($book, true);

        return BookResponseCreator::update_ok($book->getId());
    }
}
