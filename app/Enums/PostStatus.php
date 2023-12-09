<?php

namespace App\Enums;

enum PostStatus: string
{
    case PUBLISH = 'publish';
    case FUTURE = 'future';
    case DRAFT = 'draft';
    case PENDING = 'pending';
    case PRIVATE = 'private';
    case TRASH = 'trash';
    case AUTO_DRAFT = 'auto-draft';
    case INHERIT = 'inherit';
    case OTHER = 'other';
}
