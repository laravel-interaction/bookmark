<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;
use LaravelInteraction\Bookmark\Events\Bookmarked;
use LaravelInteraction\Bookmark\Events\Unbookmarked;

/**
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $bookmarker
 * @property \Illuminate\Database\Eloquent\Model $bookmarkable
 *
 * @method static \LaravelInteraction\Bookmark\Bookmark|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Bookmark\Bookmark|\Illuminate\Database\Eloquent\Builder query()
 */
class Bookmark extends MorphPivot
{
    /**
     * @var array<string, class-string<\LaravelInteraction\Bookmark\Events\Bookmarked>>|array<string, class-string<\LaravelInteraction\Bookmark\Events\Unbookmarked>>
     */
    protected $dispatchesEvents = [
        'created' => Bookmarked::class,
        'deleted' => Unbookmarked::class,
    ];

    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    public function bookmarker(): BelongsTo
    {
        return $this->user();
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(
            function (self $like): void {
                if ($like->uuids()) {
                    $like->{$like->getKeyName()} = Str::orderedUuid();
                }
            }
        );
    }

    /**
     * @var bool
     */
    public $incrementing = true;

    public function getIncrementing(): bool
    {
        if ($this->uuids()) {
            return false;
        }

        return parent::getIncrementing();
    }

    public function getKeyName(): string
    {
        return $this->uuids() ? 'uuid' : parent::getKeyName();
    }

    public function getKeyType(): string
    {
        return $this->uuids() ? 'string' : parent::getKeyType();
    }

    public function getTable()
    {
        return config('bookmark.table_names.bookmarks') ?: parent::getTable();
    }

    public function isBookmarkedBy(Model $user): bool
    {
        return $user->is($this->bookmarker);
    }

    public function isBookmarkedTo(Model $object): bool
    {
        return $object->is($this->bookmarkable);
    }

    public function scopeWithType(Builder $query, string $type): Builder
    {
        return $query->where('bookmarkable_type', app($type)->getMorphClass());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(config('bookmark.models.user'), config('bookmark.column_names.user_foreign_key'));
    }

    protected function uuids(): bool
    {
        return (bool) config('bookmark.uuids');
    }
}
