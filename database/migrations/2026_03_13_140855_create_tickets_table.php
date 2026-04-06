<?php

use App\Enums\TicketKind;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('project_id')->constrained('projects')->cascadeOnDelete();
            $table->unsignedInteger('local_id')->comment("id relative to the project")->default(0);
            $table->foreignId('created_by')->constrained('users')->cascadeOnDelete();
            $table->string('title');
            $table->enum('status', TicketStatus::cases())->default(TicketStatus::NEW);
            $table->enum('type', TicketType::cases())->nullable()->default(null);
            $table->enum('kind', TicketKind::cases());
            $table->enum('priority', TicketPriority::cases());
            $table->text('description')->default('');

            $table->string('refuse_reason', 512)->default('');
            $table->enum('previous_status', TicketStatus::cases())->nullable()->default(null);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
