<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookmarksTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create(
            config('bookmark.table_names.bookmarks'),
            function (Blueprint $table): void {
                config('bookmark.uuids') ? $table->uuid('uuid') : $table->bigIncrements('id');
                $table->unsignedBigInteger(config('bookmark.column_names.user_foreign_key'))
                    ->index()
                    ->comment('user_id');
                $table->morphs('bookmarkable');
                $table->timestamps();
                $table->unique(
                    [config('bookmark.column_names.user_foreign_key'), 'bookmarkable_type', 'bookmarkable_id']
                );
            }
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists(config('bookmark.table_names.bookmarks'));
    }
}
