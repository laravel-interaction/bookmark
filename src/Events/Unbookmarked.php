<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Events;

use Illuminate\Database\Eloquent\Model;

class Unbookmarked
{
    public function __construct(
        public Model $model
    ) {
    }
}
