<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMfaTotpTable extends Migration
{
    public function up(): void
    {
        Schema::connection(config('mfa_totp.database_connection'))
            ->create(config('mfa_totp.table_name'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->foreignId('mfa_id')->unique()
                    ->constrained('user_mfa')
                    ->cascadeOnDelete();
                $table->text('secret')->nullable();
                $table->timestamps();
            });
    }

    public function down(): void
    {
        Schema::connection(config('mfa_totp.database_connection'))
            ->dropIfExists(config('mfa_totp.table_name'));
    }
}
