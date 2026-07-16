<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('concerns')
            ->where('status', 'in_review')
            ->update(['status' => 'under_review']);

        DB::table('concerns')
            ->where('status', 'responded')
            ->update(['status' => 'awaiting_resident_response']);

        DB::table('concerns')
            ->orderBy('id')
            ->whereNull('concern_id')
            ->eachById(function ($concern) {
                DB::table('concerns')
                    ->where('id', $concern->id)
                    ->update([
                        'concern_id' => 'CON-LEGACY-' . str_pad((string) $concern->id, 6, '0', STR_PAD_LEFT),
                        'submitted_at' => $concern->created_at,
                        'due_at' => $concern->created_at
                            ? date('Y-m-d H:i:s', strtotime($concern->created_at . ' +48 hours'))
                            : null,
                    ]);
            });
    }

    public function down(): void
    {
        DB::table('concerns')
            ->where('concern_id', 'like', 'CON-LEGACY-%')
            ->update([
                'concern_id' => null,
                'submitted_at' => null,
                'due_at' => null,
            ]);
    }
};
