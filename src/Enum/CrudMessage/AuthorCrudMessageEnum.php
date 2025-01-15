<?php

namespace App\Enum\CrudMessage;

enum AuthorCrudMessageEnum: string
{
    case AUTHOR_CREATED = 'Author created';
    case AUTHOR_UPDATED = 'Author updated';
    case AUTHOR_DELETED = 'Author deleted';
    case AUTHOR_NOT_FOUND = 'Author not found';
}
