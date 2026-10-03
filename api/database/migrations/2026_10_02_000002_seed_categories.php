<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        $categories = [
            ['id' => '33333333-3333-4333-8333-333333333333', 'name' => 'Electricidad'],
            ['id' => '44444444-4444-4444-8444-444444444444', 'name' => 'Fontanería'],
            ['id' => '11111111-1111-4111-8111-111111111111', 'name' => 'General'],
            ['id' => '22222222-2222-4222-8222-222222222222', 'name' => 'Herramientas'],
            ['id' => '55555555-5555-4555-8555-555555555555', 'name' => 'Pinturas'],
        ];

        foreach ($categories as $cat) {
            DB::statement(
                "INSERT IGNORE INTO category (id, name) VALUES (?, ?);",
                [$cat['id'], $cat['name']]
            );
        }
    }

    public function down(): void
    {
        DB::statement("DELETE FROM category WHERE id IN (
            '33333333-3333-4333-8333-333333333333',
            '44444444-4444-4444-8444-444444444444',
            '11111111-1111-4111-8111-111111111111',
            '22222222-2222-4222-8222-222222222222',
            '55555555-5555-4555-8555-555555555555'
        );");
    }
};
