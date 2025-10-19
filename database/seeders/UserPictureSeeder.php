<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserPicture;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

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
        $gcsBucket = env('GOOGLE_CLOUD_STORAGE_BUCKET');

        if (!$gcsBucket) {
            $this->command->error('GOOGLE_CLOUD_STORAGE_BUCKET environment variable is not set.');
            return;
        }

        foreach ($users as $user) {
            $this->command->info("Processing user: {$user->email}");

            // Setiap pengguna akan memiliki 1 foto profil dan 2 foto tambahan.
            for ($i = 1; $i <= 3; $i++) {
                try {
                    $this->command->info("  - Downloading picture {$i}/3...");
                                        $response = Http::get('https://picsum.photos/800/600?t=' . microtime(true));

                    if ($response->successful()) {
                        $imageContents = $response->body();
                        $userEmail = $user->email;
                        $randomFileName = Str::random(40) . '.jpg';

                        if ($i === 1) {
                            $imagePath = "profile_pictures/{$userEmail}/{$randomFileName}";
                        } else {
                            $imagePath = "additional_pictures/{$userEmail}/{$randomFileName}";
                        }

                        // Simpan ke Google Cloud Storage
                        Storage::disk('gcs')->put($imagePath, $imageContents, 'public');
                        $this->command->info("    - Uploaded to GCS: {$imagePath}");

                        // Bangun URL secara manual untuk konsistensi
                        $url = "https://storage.googleapis.com/{$gcsBucket}/{$imagePath}";

                        // Simpan URL ke database
                        UserPicture::create([
                            'user_id' => $user->id,
                            'picture_url' => $url,
                            'sequence' => $i,
                        ]);

                        // Jika ini adalah gambar pertama, tetapkan sebagai foto profil utama
                        if ($i === 1) {
                            $user->profile->update(['profile_picture_url' => $url]);
                            $this->command->info("    - Set as profile picture.");
                        }
                    } else {
                        $this->command->error("    - Failed to download image. Status: " . $response->status());
                    }
                } catch (Throwable $e) {
                    $this->command->error("    - An error occurred: " . $e->getMessage());
                    Log::error("UserPictureSeeder Error for user {$user->id}: " . $e->getMessage());
                    // Lanjutkan ke iterasi berikutnya jika terjadi kesalahan
                    continue;
                }

                // Tambahkan jeda untuk menghindari rate limiting
                sleep(1);
            }
        }
    }
}