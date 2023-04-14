<?php

namespace App\Validator;

use DateTimeImmutable;

class BookValidator
{
    public static function create_validateRequest($requestData)
    {
        $errors = [];

        if (!isset($requestData['title']))
            $errors[] = 'missing "title" parameter';
        if (!isset($requestData['description']))
            $errors[] = 'missing "description" parameter';

        if (!isset($requestData['written_at']))
            $errors[] = 'missing "written_at" parameter';
        elseif(DateTimeImmutable::createFromFormat('d.m.Y', $requestData['written_at']) === false)
            $errors[] = 'invalid "written_at" date format. Date format must be d.m.Y. Example: 11.08.1998';
        
        if (!isset($requestData['authors']))
            $errors[] = 'missing "authors" parameter';
        elseif (is_array($requestData['authors']) && array_key_first($requestData['authors']) !== 0) {
            $errors[] = 'invalid "authors" type. it must be array and can not be empty';
        }

        if (!isset($requestData['genres']))
            $errors[] = 'missing "genres" parameter';
        elseif (is_array($requestData['genres']) && array_key_first($requestData['genres']) !== 0) {
            $errors[] = 'invalid "genres" type. it must be array and can not be empty';
        }
            
        return $errors;
    }
}