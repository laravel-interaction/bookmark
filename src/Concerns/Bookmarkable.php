<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Concerns;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use LaravelInteraction\Support\Interaction;
use function is_a;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Bookmark\Bookmark[] $bookmarkableBookmarks
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Bookmark\Concerns\Bookmarker[] $bookmarkers
 * @property-read string|int|null $bookmarkers_count
 *
 * @method static static|\Illuminate\Database\Eloquent\Builder whereBookmarkedBy(\Illuminate\Database\Eloquent\Model $user)
 * @method static static|\Illuminate\Database\Eloquent\Builder whereNotBookmarkedBy(\Illuminate\Database\Eloquent\Model $user)
 */
trait Bookmarkable
{
    public function bookmarkableBookmarks(): MorphMany
    {
        return $this->morphMany(config('bookmark.models.bookmark'), 'bookmarkable');
    }

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

    /**
     * @param array<int, string>|null $divisors
     */
    public function bookmarkersCountForHumans(
        int $precision = 1,
        int $mode = PHP_ROUND_HALF_UP,
        $divisors = null
    ): string {
        return Interaction::numberForHumans(
            $this->bookmarkersCount(),
            $precision,
            $mode,
            $divisors ?? config('bookmark.divisors')
        );
    }

    public function isBookmarkedBy(Model $user): bool
    {
        if (! is_a($user, config('bookmark.models.user'))) {
            return false;
        }

        $bookmarkersLoaded = $this->relationLoaded('bookmarkers');

        if ($bookmarkersLoaded) {
            return $this->bookmarkers->contains($user);
        }

        return ($this->relationLoaded(
            'bookmarkableBookmarks'
        ) ? $this->bookmarkableBookmarks : $this->bookmarkableBookmarks())
            ->where(config('bookmark.column_names.user_foreign_key'), $user->getKey())
            ->count() > 0;
    }

    public function isNotBookmarkedBy(Model $user): bool
    {
        return ! $this->isBookmarkedBy($user);
    }

    public function scopeWhereBookmarkedBy(Builder $query, Model $user): Builder
    {
        return $query->whereHas(
            'bookmarkers',
            function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }

    public function scopeWhereNotBookmarkedBy(Builder $query, Model $user): Builder
    {
        return $query->whereDoesntHave(
            'bookmarkers',
            function (Builder $query) use ($user): Builder {
                return $query->whereKey($user->getKey());
            }
        );
    }
}
