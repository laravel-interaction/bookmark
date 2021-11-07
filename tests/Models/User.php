<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Bookmark\Concerns\Bookmarkable;
use LaravelInteraction\Bookmark\Concerns\Bookmarker;

/**
 * @method static \LaravelInteraction\Bookmark\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Bookmarkable;
    use Bookmarker;
}
