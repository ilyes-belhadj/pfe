<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
        ALTER TABLE projects
        MODIFY COLUMN status ENUM(
            'draft',
            'new_lead',
            'plan_in_progress',
            'devis_sent',
            'client_accord',
            'signed_compromis'
        ) DEFAULT 'draft'
    ");
    }


    public function down(): void
    {
        DB::statement("
        ALTER TABLE projects
        MODIFY COLUMN status VARCHAR(255) DEFAULT 'draft'
    ");
    }
};
