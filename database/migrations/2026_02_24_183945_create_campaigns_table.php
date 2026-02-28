<?php

use App\Enums\CampaignStatus;
use App\Models\BrandVoiceProfile;
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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('public_id');
            $table->foreignIdFor(Workspace::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(BrandVoiceProfile::class)->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->jsonb('brief');
            $table->string('job_batch_id')->nullable()->index();
            $table->timestamp('deadline')->nullable();
            $table->enum('status', CampaignStatus::values());
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
