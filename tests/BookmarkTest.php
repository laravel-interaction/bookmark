<?php

declare(strict_types=1);

namespace Zing\LaravelBookmark\Tests;

use Illuminate\Support\Carbon;
use Zing\LaravelBookmark\Bookmark;
use Zing\LaravelBookmark\Tests\Models\Channel;
use Zing\LaravelBookmark\Tests\Models\User;

class BookmarkTest extends TestCase
{
    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Zing\LaravelBookmark\Tests\Models\User
     */
    protected $user;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|\Zing\LaravelBookmark\Tests\Models\Channel
     */
    protected $channel;

    /**
     * @var \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|object|\Zing\LaravelBookmark\Bookmark|null
     */
    protected $bookmark;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->bookmark($this->channel);
        $this->bookmark = Bookmark::query()->first();
    }

    public function testBookmarkTimestamp(): void
    {
        self::assertInstanceOf(Carbon::class, $this->bookmark->created_at);
        self::assertInstanceOf(Carbon::class, $this->bookmark->updated_at);
    }

    public function testScopeWithType(): void
    {
        self::assertSame(1, Bookmark::query()->withType(Channel::class)->count());
        self::assertSame(0, Bookmark::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        self::assertSame(config('bookmark.table_names.bookmarks'), $this->bookmark->getTable());
    }

    public function testBookmarker(): void
    {
        self::assertInstanceOf(User::class, $this->bookmark->bookmarker);
    }

    public function testBookmarkable(): void
    {
        self::assertInstanceOf(Channel::class, $this->bookmark->bookmarkable);
    }

    public function testUser(): void
    {
        self::assertInstanceOf(User::class, $this->bookmark->user);
    }

    public function testIsBookmarkedTo(): void
    {
        self::assertTrue($this->bookmark->isBookmarkedTo($this->channel));
        self::assertFalse($this->bookmark->isBookmarkedTo($this->user));
    }

    public function testIsBookmarkedBy(): void
    {
        self::assertFalse($this->bookmark->isBookmarkedBy($this->channel));
        self::assertTrue($this->bookmark->isBookmarkedBy($this->user));
    }
}
