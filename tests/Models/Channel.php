<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Zing\LaravelBookmark\Concerns\Bookmarkable;

/**
 * @method static \Zing\LaravelBookmark\Tests\Models\Channel|\Illuminate\Database\Eloquent\Builder query()
 */
class Channel extends Model
{
    use Bookmarkable;
}
