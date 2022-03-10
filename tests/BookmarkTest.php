<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests;

use Illuminate\Support\Carbon;
use LaravelInteraction\Bookmark\Bookmark;
use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;

/**
 * @internal
 */
final class BookmarkTest extends TestCase
{
    /**
     * @var \LaravelInteraction\Bookmark\Tests\Models\User
     */
    private $user;

    /**
     * @var \LaravelInteraction\Bookmark\Tests\Models\Channel
     */
    private $channel;

    /**
     * @var \LaravelInteraction\Bookmark\Bookmark
     */
    private $bookmark;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::query()->create();
        $this->channel = Channel::query()->create();
        $this->user->bookmark($this->channel);
        $this->bookmark = Bookmark::query()->firstOrFail();
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
        self::assertSame(config('bookmark.table_names.pivot'), $this->bookmark->getTable());
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
