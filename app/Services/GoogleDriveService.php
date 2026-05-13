<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Illuminate\Http\UploadedFile;

class GoogleDriveService
{
    protected ?Drive $driveUpload = null;
    protected ?Drive $driveRead = null;
    protected string $folderId;
    protected ?string $uploadError = null;

    public function __construct()
    {
        $this->folderId = env('GOOGLE_DRIVE_FOLDER_ID', '');

        try {
            $readClient = new Client();
            $readClient->setAuthConfig(base_path(env('GOOGLE_DRIVE_CREDENTIALS')));
            $readClient->addScope(Drive::DRIVE_READONLY);
            $this->driveRead = new Drive($readClient);
        } catch (\Exception $e) {}

        $clientId     = env('GOOGLE_OAUTH_CLIENT_ID');
        $clientSecret = env('GOOGLE_OAUTH_CLIENT_SECRET');
        $refreshToken = env('GOOGLE_OAUTH_REFRESH_TOKEN');

        if ($clientId && $clientSecret && $refreshToken) {
            try {
                $uploadClient = new Client();
                $uploadClient->setClientId($clientId);
                $uploadClient->setClientSecret($clientSecret);
                $uploadClient->setAccessType('offline');
                $uploadClient->addScope(Drive::DRIVE_FILE);
                $uploadClient->fetchAccessTokenWithRefreshToken($refreshToken);
                $this->driveUpload = new Drive($uploadClient);
            } catch (\Exception $e) {
                $this->uploadError = 'OAuth2 loi: ' . $e->getMessage();
            }
        } else {
            $this->uploadError = 'Chua cau hinh OAuth2. Can them GOOGLE_OAUTH_CLIENT_ID, GOOGLE_OAUTH_CLIENT_SECRET, GOOGLE_OAUTH_REFRESH_TOKEN vao .env';
        }
    }

    public function uploadImage(UploadedFile $file): string
    {
        if (!$this->driveUpload) {
            throw new \Exception($this->uploadError ?? 'Google Drive upload chua duoc cau hinh.');
        }
        $fileMetadata = new DriveFile([
            'name'    => time() . '_' . $file->getClientOriginalName(),
            'parents' => $this->folderId ? [$this->folderId] : [],
        ]);
        $uploaded = $this->driveUpload->files->create($fileMetadata, [
            'data'       => file_get_contents($file->getRealPath()),
            'mimeType'   => $file->getMimeType(),
            'uploadType' => 'multipart',
            'fields'     => 'id',
        ]);
        return $uploaded->getId();
    }

    public function deleteFile(string $fileId): void
    {
        if (!$this->driveUpload) return;
        try { $this->driveUpload->files->delete($fileId); } catch (\Exception) {}
    }

    public function getFileContent(string $fileId): array
    {
        if (!$this->driveRead) throw new \Exception('Service Account chua duoc cau hinh.');
        try {
            $file     = $this->driveRead->files->get($fileId, ['fields' => 'mimeType,name']);
            $response = $this->driveRead->files->get($fileId, ['alt' => 'media']);
            return [
                'mimeType' => $file->getMimeType(),
                'content'  => $response->getBody()->getContents(),
                'name'     => $file->getName(),
            ];
        } catch (\Exception $e) {
            throw new \Exception('Khong the lay anh tu Google Drive: ' . $e->getMessage());
        }
    }

    public function fileExists(string $fileId): bool
    {
        if (!$this->driveRead) return false;
        try { $this->driveRead->files->get($fileId, ['fields' => 'id']); return true; } catch (\Exception) { return false; }
    }

    public function isUploadReady(): bool { return $this->driveUpload !== null; }
    public function getUploadError(): ?string { return $this->uploadError; }

    public static function extractFileId(string $urlOrId): ?string
    {
        if (!str_contains($urlOrId, '/') && !str_contains($urlOrId, 'http')) return $urlOrId;
        if (preg_match('/\/anh\/([a-zA-Z0-9_-]+)/', $urlOrId, $m)) return $m[1];
        if (preg_match('/[?&]id=([a-zA-Z0-9_-]+)/', $urlOrId, $m)) return $m[1];
        if (preg_match('/\/file\/d\/([a-zA-Z0-9_-]+)/', $urlOrId, $m)) return $m[1];
        return null;
    }

    public static function isGoogleDriveFileId(string $value): bool
    {
        if (preg_match('/^[a-zA-Z0-9_-]+$/', $value) && strlen($value) > 20) return true;
        return str_contains($value, 'drive.google.com') || str_contains($value, 'googleusercontent.com');
    }

    public static function isGoogleDriveUrl(string $url): bool
    {
        return str_contains($url, 'drive.google.com') || str_contains($url, 'googleusercontent.com');
    }
}
