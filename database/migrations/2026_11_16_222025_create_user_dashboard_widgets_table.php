<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_dashboard_widgets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('widget_key', 100);

            $table->boolean('is_visible')->default(true);

            $table->timestamps();

            $table->unique(
                ['user_id', 'widget_key'],
                'user_dashboard_widgets_user_widget_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_dashboard_widgets');
    }
};
