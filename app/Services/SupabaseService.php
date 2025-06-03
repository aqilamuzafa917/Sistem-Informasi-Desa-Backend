<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

const SECONDS_IN_DAY = 86400;

class SupabaseService
{
    private $supabaseUrl;
    private $apiKey;
    private $bucketName;

    public function __construct()
    {
        $this->supabaseUrl = env('SUPABASE_URL');
        $this->apiKey = env('SUPABASE_APIKEY');
        $this->bucketName = env('SUPABASE_BUCKET');
    }

    public function uploadArtikelMedia($file)
    {
        try {
            Log::info("Preparing to upload artikel media to supabase", [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize()
            ]);
            
            // Generate unique filename from the start
            $originalName = $file->getClientOriginalName();
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $timestamp = time();
            $random = substr(md5(uniqid()), 0, 8);
            $uniqueName = "{$nameOnly}_{$timestamp}_{$random}.{$ext}";
            $filepath = "artikel-media/" . $uniqueName;

            Log::info("Generated unique filename", [
                'unique_name' => $uniqueName,
                'filepath' => $filepath
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->attach('file', $file->get(), $uniqueName)
                ->post(
                    "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filepath}"
                );

            if ($response->successful()) {
                Log::info("Successfully uploaded file to Supabase", [
                    'filepath' => $filepath,
                    'response' => $response->json()
                ]);
                return ['path' => $filepath] + $response->json();
            }

            Log::error("Upload failed", [
                'status' => $response->status(),
                'body' => $response->json(),
                'filepath' => $filepath
            ]);
            throw new \Exception('Failed to upload artikel media to Supabase: ' . $response->body());
        } catch (\Exception $e) {
            Log::error("Error in uploadArtikelMedia", [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw $e;
        }
    }

    public function getArtikelMediaUrl($file)
    {
        try {
            // Get the path from the upload result
            $filepath = $file['path'] ?? null;
            
            if (!$filepath) {
                throw new \Exception('File path not found in upload result');
            }

            Log::info("Getting signed URL for file: {$filepath}");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->post("{$this->supabaseUrl}/storage/v1/object/sign/{$this->bucketName}/{$filepath}", [
                    "expiresIn" => 999 * SECONDS_IN_DAY,
                ]);

            if ($response->successful()) {
                Log::info("Successfully got signed URL for: {$filepath}");
                $url = $this->supabaseUrl . "/storage/v1" . $response->json()['signedURL'];
                return str_replace(' ', '%20', $url);
            }

            Log::error("Failed to get signed URL", [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to retrieve signed URL for artikel media: ' . $response->body());
        } catch (\Exception $e) {
            Log::error("Error in getArtikelMediaUrl: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteArtikelMedia($filepath)
    {
        // If the filepath doesn't already include artikel-media, add it
        if (!str_starts_with($filepath, 'artikel-media/')) {
            $filepath = "artikel-media/" . $filepath;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])
            ->delete("{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filepath}");

        if (!$response->successful()) {
            throw new \Exception('Failed to delete artikel media from Supabase: ' . $response->body());
        }

        return true;
    }

    public function uploadSuratBuktiPendukung($file)
    {
        try {
            Log::info("Preparing to upload surat bukti pendukung to supabase");
            
            // Generate unique filename from the start
            $originalName = $file->getClientOriginalName();
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $timestamp = time();
            $random = substr(md5(uniqid()), 0, 8);
            $uniqueName = "{$nameOnly}_{$timestamp}_{$random}.{$ext}";
            $filepath = "sk-bukti-pendukung/" . $uniqueName;

            Log::info("Uploading file with unique name: {$uniqueName}");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->attach('file', $file->get(), $uniqueName)
                ->post(
                    "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filepath}"
                );

            if ($response->successful()) {
                Log::info("Successfully uploaded file to: {$filepath}");
                return ['path' => $filepath] + $response->json();
            }

            Log::error("Upload failed", [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to upload surat bukti pendukung to Supabase: ' . $response->body());
        } catch (\Exception $e) {
            Log::error("Error in uploadSuratBuktiPendukung: " . $e->getMessage());
            throw $e;
        }
    }

    public function getSuratBuktiPendukungUrl($file)
    {
        try {
            // Get the path from the upload result
            $filepath = $file['path'] ?? null;
            
            if (!$filepath) {
                throw new \Exception('File path not found in upload result');
            }

            Log::info("Getting signed URL for file: {$filepath}");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->post("{$this->supabaseUrl}/storage/v1/object/sign/{$this->bucketName}/{$filepath}", [
                    "expiresIn" => 999 * SECONDS_IN_DAY,
                ]);

            if ($response->successful()) {
                Log::info("Successfully got signed URL for: {$filepath}");
                $url = $this->supabaseUrl . "/storage/v1" . $response->json()['signedURL'];
                return str_replace(' ', '%', $url);
            }

            Log::error("Failed to get signed URL", [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to retrieve signed URL for surat bukti pendukung: ' . $response->body());
        } catch (\Exception $e) {
            Log::error("Error in getSuratBuktiPendukungUrl: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteSuratBuktiPendukung($filepath)
    {
        // If the filepath doesn't already include sk-bukti-pendukung, add it
        if (!str_starts_with($filepath, 'sk-bukti-pendukung/')) {
            $filepath = "sk-bukti-pendukung/" . $filepath;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])
            ->delete("{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filepath}");

        if (!$response->successful()) {
            throw new \Exception('Failed to delete surat bukti pendukung from Supabase: ' . $response->body());
        }

        return true;
    }

    public function uploadProfilMedia($file)
    {
        try {
            Log::info("Preparing to upload profil media to supabase", [
                'original_name' => $file->getClientOriginalName(),
                'mime_type' => $file->getClientMimeType(),
                'size' => $file->getSize()
            ]);
            
            // Generate unique filename from the start
            $originalName = $file->getClientOriginalName();
            $ext = pathinfo($originalName, PATHINFO_EXTENSION);
            $nameOnly = pathinfo($originalName, PATHINFO_FILENAME);
            $timestamp = time();
            $random = substr(md5(uniqid()), 0, 8);
            $uniqueName = "{$nameOnly}_{$timestamp}_{$random}.{$ext}";
            $filepath = "profil-media/" . $uniqueName;

            Log::info("Generated unique filename", [
                'unique_name' => $uniqueName,
                'filepath' => $filepath
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->attach('file', $file->get(), $uniqueName)
                ->post(
                    "{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filepath}"
                );

            if ($response->successful()) {
                Log::info("Successfully uploaded file to Supabase", [
                    'filepath' => $filepath,
                    'response' => $response->json()
                ]);
                return ['path' => $filepath] + $response->json();
            }

            Log::error("Upload failed", [
                'status' => $response->status(),
                'body' => $response->json(),
                'filepath' => $filepath
            ]);
            throw new \Exception('Failed to upload profil media to Supabase: ' . $response->body());
        } catch (\Exception $e) {
            Log::error("Error in uploadProfilMedia", [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName()
            ]);
            throw $e;
        }
    }

    public function getProfilMediaUrl($file)
    {
        try {
            // Get the path from the upload result
            $filepath = $file['path'] ?? null;
            
            if (!$filepath) {
                throw new \Exception('File path not found in upload result');
            }

            Log::info("Getting signed URL for file: {$filepath}");

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])
                ->post("{$this->supabaseUrl}/storage/v1/object/sign/{$this->bucketName}/{$filepath}", [
                    "expiresIn" => 999 * SECONDS_IN_DAY,
                ]);

            if ($response->successful()) {
                Log::info("Successfully got signed URL for: {$filepath}");
                $url = $this->supabaseUrl . "/storage/v1" . $response->json()['signedURL'];
                return str_replace(' ', '%20', $url);
            }

            Log::error("Failed to get signed URL", [
                'status' => $response->status(),
                'body' => $response->json()
            ]);
            throw new \Exception('Failed to retrieve signed URL for profil media: ' . $response->body());
        } catch (\Exception $e) {
            Log::error("Error in getProfilMediaUrl: " . $e->getMessage());
            throw $e;
        }
    }

    public function deleteProfilMedia($filepath)
    {
        // If the filepath doesn't already include profil-media, add it
        if (!str_starts_with($filepath, 'profil-media/')) {
            $filepath = "profil-media/" . $filepath;
        }

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
        ])
            ->delete("{$this->supabaseUrl}/storage/v1/object/{$this->bucketName}/{$filepath}");

        if (!$response->successful()) {
            throw new \Exception('Failed to delete profil media from Supabase: ' . $response->body());
        }

        return true;
    }
} 