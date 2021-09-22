<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMfaTable extends Migration
{
    public function up(): void
    {
        Schema::connection(config('mfa.database_connection'))
            ->create(config('mfa.table_name'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->unsignedBigInteger('subject_id')->unique();
                $table->json('types')->nullable();
                $table->timestamps();
            });
    }

    public function down(): void
    {
        Schema::connection(config('mfa.database_connection'))
            ->dropIfExists(config('mfa.table_name'));
    }
}
