<?php

use App\Enums\CampaignStatus;
use App\Models\Campaign;
use App\Models\Workspace;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('campaign_items', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignIdFor(Workspace::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Campaign::class)->constrained()->cascadeOnDelete();
            $table->string('content_type', 255);
            $table->text('output')->nullable();
            $table->enum('status', CampaignStatus::values());
            $table->integer('retry_count');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_items');
    }
};
