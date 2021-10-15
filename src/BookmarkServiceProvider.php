<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark;

use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\InteractionServiceProvider;

class BookmarkServiceProvider extends InteractionServiceProvider
{
    /**
     * @var string
     */
    protected $interaction = InteractionList::BOOKMARK;
}
