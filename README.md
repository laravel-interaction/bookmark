# Laravel Bookmark

User bookmark/unbookmark behaviour for Laravel.

<p align="center">
<a href="https://github.com/zingimmick/laravel-bookmark/actions"><img src="https://github.com/zingimmick/laravel-bookmark/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://codecov.io/gh/zingimmick/laravel-bookmark"><img src="https://codecov.io/gh/zingimmick/laravel-bookmark/branch/master/graph/badge.svg" alt="Code Coverage" /></a>
<a href="https://packagist.org/packages/zing/laravel-bookmark"><img src="https://poser.pugx.org/zing/laravel-bookmark/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-bookmark"><img src="https://poser.pugx.org/zing/laravel-bookmark/downloads" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/zing/laravel-bookmark"><img src="https://poser.pugx.org/zing/laravel-bookmark/v/unstable.svg" alt="Latest Unstable Version"></a>
<a href="https://packagist.org/packages/zing/laravel-bookmark"><img src="https://poser.pugx.org/zing/laravel-bookmark/license" alt="License"></a>
<a href="https://codeclimate.com/github/zingimmick/laravel-bookmark/maintainability"><img src="https://api.codeclimate.com/v1/badges/7b9401b8291d19a0ec95/maintainability" alt="Code Climate" /></a>
</p>

> **Requires [PHP 7.2.0+](https://php.net/releases/)**

Require Laravel Bookmark using [Composer](https://getcomposer.org):

```bash
composer require zing/laravel-bookmark
```

## Usage

### Setup Bookmarker

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelBookmark\Concerns\Bookmarker;

class User extends Model
{
    use Bookmarker;
}
```

### Setup Bookmarkable

```php
use Illuminate\Database\Eloquent\Model;
use Zing\LaravelBookmark\Concerns\Bookmarkable;

class Channel extends Model
{
    use Bookmarkable;
}
```

### Bookmarker

```php
use Zing\LaravelBookmark\Tests\Models\Channel;
/** @var \Zing\LaravelBookmark\Tests\Models\User $user */
/** @var \Zing\LaravelBookmark\Tests\Models\Channel $channel */
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
use Zing\LaravelBookmark\Tests\Models\User;
use Zing\LaravelBookmark\Tests\Models\Channel;
/** @var \Zing\LaravelBookmark\Tests\Models\User $user */
/** @var \Zing\LaravelBookmark\Tests\Models\Channel $channel */
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
| `Zing\LaravelBookmark\Events\Bookmarked` | When an object get bookmarked. |
| `Zing\LaravelBookmark\Events\Unbookmarked` | When an object get unbookmarked. |

## License

Laravel Bookmark is an open-sourced software licensed under the [MIT license](LICENSE).