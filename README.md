# Laravel Bookmark

User bookmark/unbookmark behaviour for Laravel.

<p align="center">
<a href="https://packagist.org/packages/laravel-interaction/bookmark"><img src="https://poser.pugx.org/laravel-interaction/bookmark/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/bookmark"><img src="https://poser.pugx.org/laravel-interaction/bookmark/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel-interaction/bookmark"><img src="https://poser.pugx.org/laravel-interaction/bookmark/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/laravel-interaction/bookmark"><img src="https://poser.pugx.org/laravel-interaction/bookmark/license" alt="License"></a>
</p>

> **Requires [PHP 7.2.0+](https://php.net/releases/)**

Require Laravel Bookmark using [Composer](https://getcomposer.org):

```bash
composer require laravel-interaction/bookmark
```

## Usage

### Setup Bookmarker

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Bookmark\Concerns\Bookmarker;

class User extends Model
{
    use Bookmarker;
}
```

### Setup Bookmarkable

```php
use Illuminate\Database\Eloquent\Model;
use LaravelInteraction\Bookmark\Concerns\Bookmarkable;

class Channel extends Model
{
    use Bookmarkable;
}
```

### Bookmarker

```php
use LaravelInteraction\Bookmark\Tests\Models\Channel;
/** @var \LaravelInteraction\Bookmark\Tests\Models\User $user */
/** @var \LaravelInteraction\Bookmark\Tests\Models\Channel $channel */
// Bookmark to Bookmarkable
$user->bookmark($channel);
$user->unbookmark($channel);
$user->toggleBookmark($channel);

// Compare Bookmarkable
$user->hasBookmarked($channel);
$user->hasNotBookmarked($channel);

// Get bookmarked info
$user->bookmarkableBookmarks()->count(); 

// with type
$user->bookmarkableBookmarks()->withType(Channel::class)->count(); 

// get bookmarked channels
Channel::query()->whereBookmarkedBy($user)->get();

// get bookmarked channels doesnt bookmarked
Channel::query()->whereNotBookmarkedBy($user)->get();
```

### Bookmarkable

```php
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
/** @var \LaravelInteraction\Bookmark\Tests\Models\User $user */
/** @var \LaravelInteraction\Bookmark\Tests\Models\Channel $channel */
// Compare Bookmarker
$channel->isBookmarkedBy($user); 
$channel->isNotBookmarkedBy($user);
// Get bookmarkers info
$channel->bookmarkers->each(function (User $user){
    echo $user->getKey();
});

$channels = Channel::query()->withCount('bookmarkers')->get();
$channels->each(function (Channel $channel){
    echo $channel->bookmarkers()->count(); // 1100
    echo $channel->bookmarkers_count; // "1100"
    echo $channel->bookmarkersCount(); // 1100
    echo $channel->bookmarkersCountForHumans(); // "1.1K"
});
```

### Events

| Event | Fired |
| --- | --- |
| `LaravelInteraction\Bookmark\Events\Bookmarked` | When an object get bookmarked. |
| `LaravelInteraction\Bookmark\Events\Unbookmarked` | When an object get unbookmarked. |

## License

Laravel Bookmark is an open-sourced software licensed under the [MIT license](LICENSE).
