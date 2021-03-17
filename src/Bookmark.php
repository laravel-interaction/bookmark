<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use LaravelInteraction\Bookmark\Events\Bookmarked;
use LaravelInteraction\Bookmark\Events\Unbookmarked;
use LaravelInteraction\Support\InteractionList;
use LaravelInteraction\Support\Models\Interaction;

/**
 * @property \Illuminate\Database\Eloquent\Model $user
 * @property \Illuminate\Database\Eloquent\Model $bookmarker
 * @property \Illuminate\Database\Eloquent\Model $bookmarkable
 *
 * @method static \LaravelInteraction\Bookmark\Bookmark|\Illuminate\Database\Eloquent\Builder withType(string $type)
 * @method static \LaravelInteraction\Bookmark\Bookmark|\Illuminate\Database\Eloquent\Builder query()
 */
class Bookmark extends Interaction
{
    protected $interaction = InteractionList::BOOKMARK;

    protected $tableNameKey = 'bookmarks';

    protected $morphTypeName = 'bookmarkable';

    protected $dispatchesEvents = [
        'created' => Bookmarked::class,
        'deleted' => Unbookmarked::class,
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function bookmarkable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function bookmarker(): BelongsTo
    {
        return $this->user();
    }

    public function isBookmarkedBy(Model $user): bool
    {
        return $user->is($this->bookmarker);
    }

    public function isBookmarkedTo(Model $object): bool
    {
        return $object->is($this->bookmarkable);
    }
}
