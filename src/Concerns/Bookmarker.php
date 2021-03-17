<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

/**
 * @property-read \Illuminate\Database\Eloquent\Collection|\LaravelInteraction\Bookmark\Bookmark[] $bookmarkerBookmarks
 * @property-read int|null $bookmarkerBookmarks_count
 */
trait Bookmarker
{
    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function bookmark(Model $object): void
    {
        if ($this->hasBookmarked($object)) {
            return;
        }

        $this->bookmarks(get_class($object))->attach($object->getKey());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function bookmarkerBookmarks(): HasMany
    {
        return $this->hasMany(config('bookmark.models.bookmark'), config('bookmark.column_names.user_foreign_key'), $this->getKeyName());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     *
     * @return bool
     */
    public function hasBookmarked(Model $object): bool
    {
        return ($this->relationLoaded('bookmarkerBookmarks') ? $this->bookmarkerBookmarks : $this->bookmarkerBookmarks())
            ->where('bookmarkable_id', $object->getKey())
            ->where('bookmarkable_type', $object->getMorphClass())
            ->count() > 0;
    }

    public function hasNotBookmarked(Model $object): bool
    {
        return ! $this->hasBookmarked($object);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function toggleBookmark(Model $object): void
    {
        $this->bookmarks(get_class($object))->toggle($object->getKey());
    }

    /**
     * @param \Illuminate\Database\Eloquent\Model $object
     */
    public function unbookmark(Model $object): void
    {
        if ($this->hasNotBookmarked($object)) {
            return;
        }

        $this->bookmarks(get_class($object))->detach($object->getKey());
    }

    /**
     * @param string $class
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    protected function bookmarks(string $class): MorphToMany
    {
        return $this->morphedByMany($class, 'bookmarkable', config('bookmark.models.bookmark'), config('bookmark.column_names.user_foreign_key'), 'bookmarkable_id')->withTimestamps();
    }
}
