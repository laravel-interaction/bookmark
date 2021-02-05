<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelBookmark\Bookmark[] $bookmarkableBookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection|\Zing\LaravelBookmark\Concerns\Bookmarker[] $bookmarkers
 * @property-read int|null $bookmarkers_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereBookmarkedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotBookmarkedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Bookmarkable
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $user
     *
     * @return bool
     */
    public function isBookmarkedBy(Model $user): bool
    {
        if (! is_a($user, config('bookmark.models.user'))) {
            return false;
        }

        if ($this->relationLoaded('bookmarkers')) {
            return $this->bookmarkers->contains($user);
        }

        return ($this->relationLoaded('bookmarkableBookmarks') ? $this->bookmarkableBookmarks : $this->bookmarkableBookmarks())
            ->where(config('bookmark.column_names.user_foreign_key'), $user->getKey())->count() > 0;
    }

    public function isNotBookmarkedBy(Model $user): bool
    {
        return ! $this->isBookmarkedBy($user);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphMany
     */
    public function bookmarkableBookmarks(): MorphMany
    {
        return $this->morphMany(config('bookmark.models.bookmark'), 'bookmarkable');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function bookmarkers(): BelongsToMany
    {
        return $this->morphToMany(
            config('bookmark.models.user'),
            'bookmarkable',
            config('bookmark.models.bookmark'),
            null,
            config('bookmark.column_names.user_foreign_key')
        )->withTimestamps();
    }

    public function bookmarkersCount(): int
    {
        if ($this->bookmarkers_count !== null) {
            return (int) $this->bookmarkers_count;
        }

        $this->loadCount('bookmarkers');

        return (int) $this->bookmarkers_count;
    }

    public function bookmarkersCountForHumans($precision = 1, $mode = PHP_ROUND_HALF_UP, $divisors = null): string
    {
        $number = $this->bookmarkersCount();
        $divisors = collect($divisors ?? config('bookmark.divisors'));
        $divisor = $divisors->keys()->filter(
            function ($divisor) use ($number) {
                return $divisor <= abs($number);
            }
        )->last(null, 1);

        if ($divisor === 1) {
            return (string) $number;
        }

        return number_format(round($number / $divisor, $precision, $mode), $precision) . $divisors->get($divisor);
    }

    public function scopeWhereBookmarkedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'bookmarkers',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereNotBookmarkedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'bookmarkers',
            function (Builder $query) use ($user) {
                return $query->whereKey($user->getKey());
            }
        );
    }
}
