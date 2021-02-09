<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Bookmark\Concerns\Bookmarkable;

/**
 * @method static \LaravelInteraction\Bookmark\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Bookmarkable;
}
