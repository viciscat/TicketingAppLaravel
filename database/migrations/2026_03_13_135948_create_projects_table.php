<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('projects', function (Blueprint $table) {
            $table->id();

            $table->string('name', 63);
            $table->string('issue_prefix', 4)->unique();
            $table->foreignId('contract_id')->constrained("contracts")->cascadeOnDelete();
            $table->unsignedInteger('next_ticket_id')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
