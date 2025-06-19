<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ArtikelMediaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $media = [
            [
                'id_artikel' => 2,
                'path' => 'artikel-media/ChatGPT Image 17 Jun 2025, 19.44.03 (1)_1750177141_2bf01f9b.png',
                'type' => 'image/png',
                'name' => 'ChatGPT Image 17 Jun 2025, 19.44.03 (1).png',
                'url' => 'https://oeqrcmehjunaylaaiuuv.supabase.co/storage/v1/object/sign/media/artikel-media/ChatGPT%20Image%2017%20Jun%202025,%2019.44.03%20(1)_1750177141_2bf01f9b.png?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8wNDgyNWM4OC1lNWIxLTQxMGItODM5MC1jN2UwYzcyY2E4YmYiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJtZWRpYS9hcnRpa2VsLW1lZGlhL0NoYXRHUFQgSW1hZ2UgMTcgSnVuIDIwMjUsIDE5LjQ0LjAzICgxKV8xNzUwMTc3MTQxXzJiZjAxZjliLnBuZyIsImlhdCI6MTc1MDE3NzE0NCwiZXhwIjoxODM2NDkwNzQ0fQ.4chy07IWl8yyYF5Aep6OqHU1ZS8UOy7592QcRDyUpaA',
                'created_at' => '2025-06-17 16:19:03',
                'updated_at' => '2025-06-17 16:19:03'
            ],
            [
                'id_artikel' => 3,
                'path' => 'artikel-media/82f6471d-6f1a-4a7e-9f62-017b80682613 (1)_1750177039_6a77c54d.png',
                'type' => 'image/png',
                'name' => '82f6471d-6f1a-4a7e-9f62-017b80682613 (1).png',
                'url' => 'https://oeqrcmehjunaylaaiuuv.supabase.co/storage/v1/object/sign/media/artikel-media/82f6471d-6f1a-4a7e-9f62-017b80682613%20(1)_1750177039_6a77c54d.png?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8wNDgyNWM4OC1lNWIxLTQxMGItODM5MC1jN2UwYzcyY2E4YmYiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJtZWRpYS9hcnRpa2VsLW1lZGlhLzgyZjY0NzFkLTZmMWEtNGE3ZS05ZjYyLTAxN2I4MDY4MjYxMyAoMSlfMTc1MDE3NzAzOV82YTc3YzU0ZC5wbmciLCJpYXQiOjE3NTAxNzcwNDIsImV4cCI6MTgzNjQ5MDY0Mn0.AzANnn6D-5xGrIcWoqtHN2G2-6yu37WnFUVdtfmiG-4',
                'created_at' => '2025-06-17 16:17:21',
                'updated_at' => '2025-06-17 16:17:21'
            ],
            [
                'id_artikel' => 1,
                'path' => 'artikel-media/decbee0bf215da6e9b02a4f1f81fdc9d_1750176919_a68feb1e.png',
                'type' => 'image/png',
                'name' => 'decbee0bf215da6e9b02a4f1f81fdc9d.png',
                'url' => 'https://oeqrcmehjunaylaaiuuv.supabase.co/storage/v1/object/sign/media/artikel-media/decbee0bf215da6e9b02a4f1f81fdc9d_1750176919_a68feb1e.png?token=eyJraWQiOiJzdG9yYWdlLXVybC1zaWduaW5nLWtleV8wNDgyNWM4OC1lNWIxLTQxMGItODM5MC1jN2UwYzcyY2E4YmYiLCJhbGciOiJIUzI1NiJ9.eyJ1cmwiOiJtZWRpYS9hcnRpa2VsLW1lZGlhL2RlY2JlZTBiZjIxNWRhNmU5YjAyYTRmMWY4MWZkYzlkXzE3NTAxNzY5MTlfYTY4ZmViMWUucG5nIiwiaWF0IjoxNzUwMTc2OTIyLCJleHAiOjE4MzY0OTA1MjJ9.MoWyo7ejzY9Po4X0CJpC06AuFENcCk7cjWqaEfxCxz0',
                'created_at' => '2025-06-17 16:15:22',
                'updated_at' => '2025-06-17 16:15:22'
            ]
        ];

        foreach ($media as $item) {
            DB::table('artikel_media')->insert($item);
        }
    }
} 