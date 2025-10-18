<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPicture;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserPictureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = User::all();
        foreach ($users as $user) {
            // Ambil 3 gambar acak dari Unsplash
            for ($i = 1; $i <= 3; $i++) {
                try {
                    $response = Http::get('https://source.unsplash.com/random/800x600');
                    if ($response->successful()) {
                        $imageContents = $response->body();
                        $imageName = 'user_pictures/' . $user->id . '/' . Str::random(40) . '.jpg';

                        // Simpan ke Google Cloud Storage
                        Storage::disk('gcs')->put($imageName, $imageContents);

                        // Simpan URL ke database
                        UserPicture::create([
                            'user_id' => $user->id,
                            'url' => Storage::disk('gcs')->url($imageName),
                            'sequence' => $i,
                        ]);
                    }
                } catch (\Exception $e) {
                    // Tangani jika ada error saat mengunduh atau menyimpan gambar
                    // Anda bisa menambahkan logging di sini
                    continue;
                }
            }
        }
    }
}