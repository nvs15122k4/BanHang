<?php

namespace App\Console\Commands;

use Google\Client;
use Google\Service\Drive;
use Illuminate\Console\Command;

class ListDriveImages extends Command
{
    protected $signature = 'drive:list-images {--folder= : Folder ID (mặc định dùng GOOGLE_DRIVE_FOLDER_ID trong .env)}';

    protected $description = 'Liệt kê tất cả ảnh trong thư mục Google Drive BanHangImage';

    public function handle(): int
    {
        $folderId = $this->option('folder') ?: env('GOOGLE_DRIVE_FOLDER_ID');

        if (! $folderId) {
            $this->error('Chưa cấu hình GOOGLE_DRIVE_FOLDER_ID trong .env');

            return self::FAILURE;
        }

        $client = new Client;
        $client->setAuthConfig(base_path(env('GOOGLE_DRIVE_CREDENTIALS')));
        $client->addScope(Drive::DRIVE);
        $drive = new Drive($client);

        $this->info("Đang lấy danh sách ảnh từ folder: {$folderId}");

        $results = $drive->files->listFiles([
            'q' => "'{$folderId}' in parents and trashed = false",
            'fields' => 'files(id,name,mimeType)',
        ]);

        $files = $results->getFiles();

        if (empty($files)) {
            $this->warn('Không có file nào trong thư mục này.');

            return self::SUCCESS;
        }

        $rows = [];
        foreach ($files as $file) {
            $url = "https://drive.google.com/uc?export=view&id={$file->getId()}";
            $rows[] = [
                $file->getName(),
                $file->getId(),
                $url,
            ];
        }

        $this->table(['Tên file', 'File ID', 'URL để lưu vào DB'], $rows);
        $this->info('Tổng: '.count($rows).' file(s)');
        $this->newLine();
        $this->comment('Lưu cột "URL để lưu vào DB" vào trường `anh` của bảng products.');

        return self::SUCCESS;
    }
}
