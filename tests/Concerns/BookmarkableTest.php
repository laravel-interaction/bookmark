<?php

declare(strict_types=1);

namespace LaravelInteraction\Bookmark\Tests\Concerns;

use LaravelInteraction\Bookmark\Tests\Models\Channel;
use LaravelInteraction\Bookmark\Tests\Models\User;
use LaravelInteraction\Bookmark\Tests\TestCase;

/**
 * @internal
 */
final class BookmarkableTest extends TestCase
{
    /**
     * @return \Iterator<array<class-string<\LaravelInteraction\Bookmark\Tests\Models\Channel|\LaravelInteraction\Bookmark\Tests\Models\User>>>
     */
    public static function provideModelClasses(): \Iterator
    {
        yield [Channel::class];

        yield [User::class];
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testBookmarkableBookmarks(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkableBookmarks()->count());
        self::assertSame(1, $model->bookmarkableBookmarks->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testBookmarkersCount(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkersCount());
        $user->unbookmark($model);
        self::assertSame(1, $model->bookmarkersCount());
        $model->loadCount('bookmarkers');
        self::assertSame(0, $model->bookmarkersCount());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testBookmarkersCountForHumans(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame('1', $model->bookmarkersCountForHumans());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testIsBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertFalse($model->isBookmarkedBy($model));
        $user->bookmark($model);
        self::assertTrue($model->isBookmarkedBy($user));
        $model->load('bookmarkers');
        $user->unbookmark($model);
        self::assertTrue($model->isBookmarkedBy($user));
        $model->load('bookmarkers');
        self::assertFalse($model->isBookmarkedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testIsNotBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        self::assertTrue($model->isNotBookmarkedBy($model));
        $user->bookmark($model);
        self::assertFalse($model->isNotBookmarkedBy($user));
        $model->load('bookmarkers');
        $user->unbookmark($model);
        self::assertFalse($model->isNotBookmarkedBy($user));
        $model->load('bookmarkers');
        self::assertTrue($model->isNotBookmarkedBy($user));
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testBookmarkers(string $modelClass): void
    {
        $user = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $model->bookmarkers()->count());
        $user->unbookmark($model);
        self::assertSame(0, $model->bookmarkers()->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(1, $modelClass::query()->whereBookmarkedBy($user)->count());
        self::assertSame(0, $modelClass::query()->whereBookmarkedBy($other)->count());
    }

    /**
     * @dataProvider provideModelClasses
     *
     * @param class-string<\LaravelInteraction\Bookmark\Tests\Models\User|\LaravelInteraction\Bookmark\Tests\Models\Channel> $modelClass
     */
    public function testScopeWhereNotBookmarkedBy(string $modelClass): void
    {
        $user = User::query()->create();
        $other = User::query()->create();
        $model = $modelClass::query()->create();
        $user->bookmark($model);
        self::assertSame(
            $modelClass::query()->whereKeyNot($model->getKey())->count(),
            $modelClass::query()->whereNotBookmarkedBy($user)->count()
        );
        self::assertSame($modelClass::query()->count(), $modelClass::query()->whereNotBookmarkedBy($other)->count());
    }
}
