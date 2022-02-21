<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use LaravelInteraction\Bookmark\Bookmark;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Bookmark\Bookmark[] $bookmarkerBookmarks
 * @property-read int|null $bookmarkerBookmarks_count
 */
trait Bookmarker
{
    public function bookmark(Model $object): Bookmark
    {
        $attributes = [
            'bookmarkable_id' => $object->getKey(),
            'bookmarkable_type' => $object->getMorphClass(),
        ];

        return $this->bookmarkerBookmarks()
            ->where($attributes)
            ->firstOr(function () use ($attributes) {
                $bookmarkerBookmarksLoaded = $this->relationLoaded('bookmarkerBookmarks');
                if ($bookmarkerBookmarksLoaded) {
                    $this->unsetRelation('bookmarkerBookmarks');
                }

                return $this->bookmarkerBookmarks()
                    ->create($attributes);
            });
    }

    public function bookmarkerBookmarks(): HasMany
    {
        return $this->hasMany(
            config('bookmark.models.bookmark'),
            config('bookmark.column_names.user_foreign_key'),
            $this->getKeyName()
        );
    }

    public function hasBookmarked(Model $object): bool
    {
        return ($this->relationLoaded(
            'bookmarkerBookmarks'
        ) ? $this->bookmarkerBookmarks : $this->bookmarkerBookmarks())
            ->where('bookmarkable_id', $object->getKey())
            ->where('bookmarkable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function hasNotBookmarked(Model $object): bool
    {
        return ! $this->hasBookmarked($object);
    }

    /**
     * @return bool|\LaravelInteraction\Bookmark\Bookmark
     */
    public function toggleBookmark(Model $object)
    {
        return $this->hasBookmarked($object) ? $this->unbookmark($object) : $this->bookmark($object);
    }

    public function unbookmark(Model $object): bool
    {
        $hasNotBookmarked = $this->hasNotBookmarked($object);
        if ($hasNotBookmarked) {
            return true;
        }

        $bookmarkerBookmarksLoaded = $this->relationLoaded('bookmarkerBookmarks');
        if ($bookmarkerBookmarksLoaded) {
            $this->unsetRelation('bookmarkerBookmarks');
        }

        return (bool) $this->bookmarks(\get_class($object))
            ->detach($object->getKey());
    }

    protected function bookmarks(string $class): MorphToMany
    {
        return $this->morphedByMany(
            $class,
            'bookmarkable',
            config('bookmark.models.bookmark'),
            config('bookmark.column_names.user_foreign_key')
        )
            ->withTimestamps();
    }
}
