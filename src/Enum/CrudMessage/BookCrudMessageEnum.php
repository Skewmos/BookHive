<?php

namespace App\Enum\CrudMessage;

enum BookCrudMessageEnum: string
{
    case BOOK_CREATED = 'Book created';
    case BOOK_UPDATED = 'Book updated';
    case BOOK_DELETED = 'Book deleted';
    case BOOK_NOT_FOUND = 'Book not found';

}
