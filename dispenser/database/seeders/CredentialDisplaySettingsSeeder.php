<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CredentialDisplaySettingsSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('credential_display_settings')->insert([
            ['section' => 'voucher',    'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['section' => 'satp',       'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['section' => 'schoology',  'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['section' => 'kumosoft',   'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
            ['section' => 'email',      'is_enabled' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
