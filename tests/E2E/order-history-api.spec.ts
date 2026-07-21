import { test, expect } from '@playwright/test';

test.describe('API Security - Lịch sử đơn hàng', () => {

  // Định nghĩa URL của API lịch sử đơn hàng
  const ORDER_HISTORY_API = '/api/order-history';

  test('Lỗ hổng 1: Chặn truy cập khi không có JWT Token ở Header', async ({ request }) => {
    // Gửi GET request trực tiếp mà không truyền token
    const response = await request.get(ORDER_HISTORY_API);

    // Kì vọng hệ thống trả về mã lỗi 401 (Unauthorized)
    // Nếu API đang bị lỗi (không chặn), đoạn check này sẽ bị fail
    expect(response.status(), 'API lịch sử đơn hàng phải trả về lỗi 401 khi không truyền Token').toBe(401);
  });

  test('Luồng thành công: Lấy dữ liệu hợp lệ khi có JWT Token', async ({ request }) => {
    // Giả lập một token hợp lệ (Trong thực tế, bạn sẽ phải call API login trước để lấy token thật)
    const validToken = 'mock_valid_jwt_token_here';
    const expectedOrderId = 1; // ID đơn hàng mong đợi

    // Gửi GET request kèm theo Authorization Header
    const response = await request.get(ORDER_HISTORY_API, {
      headers: {
        'Authorization': `Bearer ${validToken}`,
        'Accept': 'application/json'
      }
    });

    // Kì vọng request thành công (200 OK)
    expect(response.status(), 'API lịch sử đơn hàng phải trả về thành công 200 khi có Token hợp lệ').toBe(200);

    // Trích xuất dữ liệu JSON
    const responseData = await response.json();

    // Đối chiếu tính chính xác của dữ liệu (chuẩn mẫu đã nộp)
    expect(responseData, 'Dữ liệu phản hồi phải chứa thuộc tính orders').toHaveProperty('orders');
    if (responseData.orders && responseData.orders.length > 0) {
      expect(responseData.orders[0].id, 'Mã đơn hàng đầu tiên trả về không khớp với mã mong đợi').toBe(expectedOrderId);
    }
  });

});
