<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleanupTestUsers extends Command
{
    /**
     * Tên lệnh dùng trong terminal (VD: php artisan test:cleanup-users)
     */
    protected $signature = 'test:cleanup-users';

    /**
     * Mô tả chức năng của lệnh
     */
    protected $description = 'Tự động dọn dẹp các tài khoản rác (email chứa từ khóa test) được sinh ra do Automation Test bị lỗi sập giữa chừng.';

    /**
     * Hàm thực thi chính
     */
    public function handle()
    {
        // Thời gian giới hạn: Quét các nick được tạo ra từ hơn 1 tiếng trước
        // (Tránh xóa nhầm nick test nào đó đang chạy dở)
        $thresholdTime = Carbon::now()->subHour();

        // Tìm các User có email chứa chữ "test_" (ví dụ: testuser_1728391_1@example.com)
        // và thời gian tạo cách đây hơn 1 tiếng.
        $deletedCount = User::where('email', 'LIKE', '%test_%')
                            ->where('created_at', '<', $thresholdTime)
                            ->delete();

        // In ra màn hình Terminal để dễ theo dõi
        $this->info("Đã dọn dẹp thành công {$deletedCount} tài khoản test rác.");
        
        // Ghi lại vào file log của server để đối soát sau này
        Log::info("Cron Job Cleanup: Đã xóa {$deletedCount} tài khoản test rác sinh ra do lỗi.");
    }
}
