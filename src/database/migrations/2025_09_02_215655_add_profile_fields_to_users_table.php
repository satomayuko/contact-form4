<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProfileFieldsToUsersTable extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 今回は追加するフィールドはありません
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 今回は削除するフィールドはありません
        });
    }
}