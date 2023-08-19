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
    private \LaravelInteraction\Bookmark\Tests\Models\User $user;

    private \LaravelInteraction\Bookmark\Tests\Models\Channel $channel;

    private \LaravelInteraction\Bookmark\Bookmark $bookmark;

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
        $this->assertInstanceOf(Carbon::class, $this->bookmark->created_at);
        $this->assertInstanceOf(Carbon::class, $this->bookmark->updated_at);
    }

    public function testScopeWithType(): void
    {
        $this->assertSame(1, Bookmark::query()->withType(Channel::class)->count());
        $this->assertSame(0, Bookmark::query()->withType(User::class)->count());
    }

    public function testGetTable(): void
    {
        $this->assertSame(config('bookmark.table_names.pivot'), $this->bookmark->getTable());
    }

    public function testBookmarker(): void
    {
        $this->assertInstanceOf(User::class, $this->bookmark->bookmarker);
    }

    public function testBookmarkable(): void
    {
        $this->assertInstanceOf(Channel::class, $this->bookmark->bookmarkable);
    }

    public function testUser(): void
    {
        $this->assertInstanceOf(User::class, $this->bookmark->user);
    }

    public function testIsBookmarkedTo(): void
    {
        $this->assertTrue($this->bookmark->isBookmarkedTo($this->channel));
        $this->assertFalse($this->bookmark->isBookmarkedTo($this->user));
    }

    public function testIsBookmarkedBy(): void
    {
        $this->assertFalse($this->bookmark->isBookmarkedBy($this->channel));
        $this->assertTrue($this->bookmark->isBookmarkedBy($this->user));
    }
}
