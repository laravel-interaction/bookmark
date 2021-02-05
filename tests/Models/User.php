<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelBookmark\Concerns\Bookmarkable;
use Zing\LaravelBookmark\Concerns\Bookmarker;

/**
 * @method static \Zing\LaravelBookmark\Tests\Models\User|\Illuminate\Database\Eloquent\Builder query()
 */
class User extends Model
{
    use Bookmarker;
    use Bookmarkable;
}
