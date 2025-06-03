<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class StrukturOrganisasiException extends Exception
{
    protected $errors;
    protected $statusCode;

    public function __construct(string $message = "", array $errors = [], int $statusCode = 422)
    {
        parent::__construct($message);
        $this->errors = $errors;
        $this->statusCode = $statusCode;
    }

    public function render(): JsonResponse
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
            'errors' => $this->errors
        ], $this->statusCode);
    }

    public static function invalidFileType(): self
    {
        return new self(
            'Format file tidak valid',
            ['struktur_organisasi' => ['File harus berformat JPEG, PNG, atau JPG']]
        );
    }

    public static function fileTooLarge(): self
    {
        return new self(
            'Ukuran file terlalu besar',
            ['struktur_organisasi' => ['Ukuran file maksimal 2MB']]
        );
    }

    public static function uploadFailed(string $error): self
    {
        return new self(
            'Gagal mengupload file',
            ['struktur_organisasi' => [$error]]
        );
    }

    public static function fileNotFound(): self
    {
        return new self(
            'File tidak ditemukan',
            ['struktur_organisasi' => ['File struktur organisasi harus diupload']]
        );
    }

    public static function deleteFailed(string $error): self
    {
        return new self(
            'Gagal menghapus file lama',
            ['struktur_organisasi' => [$error]]
        );
    }
} 