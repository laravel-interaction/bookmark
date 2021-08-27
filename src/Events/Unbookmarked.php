<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Events;

use Illuminate\Database\Eloquent\Model;

class Unbookmarked
{
    /**
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $bookmark;

    public function __construct(Model $bookmark)
    {
        $this->bookmark = $bookmark;
    }
}
