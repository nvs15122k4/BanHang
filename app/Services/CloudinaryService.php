<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;

class CloudinaryService
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true,
            ],
        ]);
    }

    /**
     * Upload ảnh lên Cloudinary.
     * Trả về secure_url để lưu vào DB.
     */
    public function uploadImage(UploadedFile $file): string
    {
        $result = $this->cloudinary
            ->uploadApi()
            ->upload($file->getRealPath(), [
                'folder' => 'banhang/products',
                'resource_type' => 'image',
                'transformation' => [
                    'quality' => 'auto',
                    'fetch_format' => 'auto',
                ],
            ]);

        return $result['secure_url'];
    }

    /**
     * Xóa ảnh trên Cloudinary theo public_id.
     * public_id được trích xuất từ URL.
     */
    public function deleteImage(string $url): void
    {
        $publicId = $this->extractPublicId($url);

        if (! $publicId) {
            return;
        }

        try {
            $this->cloudinary->uploadApi()->destroy($publicId);
        } catch (\Exception) {
            // Bỏ qua nếu ảnh không tồn tại
        }
    }

    /**
     * Trích xuất public_id từ Cloudinary URL.
     *
     * Ví dụ URL:
     * https://res.cloudinary.com/dxvml3sji/image/upload/v1234567890/banhang/products/abc123.jpg
     * → public_id: banhang/products/abc123
     */
    public static function extractPublicId(string $url): ?string
    {
        // Khớp phần sau /upload/v{version}/ hoặc /upload/
        if (preg_match('/\/upload\/(?:v\d+\/)?(.+?)(?:\.[a-z]+)?$/', $url, $matches)) {
            return $matches[1];
        }

        return null;
    }

    /**
     * Kiểm tra URL có phải là Cloudinary không.
     */
    public static function isCloudinaryUrl(string $url): bool
    {
        return str_contains($url, 'cloudinary.com') || str_contains($url, 'res.cloudinary.com');
    }
}
