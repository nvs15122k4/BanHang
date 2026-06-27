# BÁO CÁO THỰC TẬP TỐT NGHIỆP - PHẦN 1
## (Trang 1-10: Bìa, Mục lục, Lời nói đầu)

---

## THÔNG TIN CƠ BẢN

- **Sinh viên**: Nguyễn Văn Sang
- **Mã số sinh viên**: 1150080072
- **Lớp**: 11_ĐH_THMT
- **Khóa**: K11
- **Giảng viên hướng dẫn**: TS. Dương Thị Thúy Nga
- **Công ty thực tập**: Công ty Hoàng Khang Incotech
- **Người hướng dẫn tại công ty**: Trần Minh Hoàn
- **Thời gian thực tập**: 13/04/2026 - 25/06/2026
- **Đại học**: Đại học Tài nguyên và Môi trường TP.HCM
- **Khoa**: Công nghệ Thông tin
- **Điểm**: (Chờ đánh giá)

---

## MỤC LỤC

1. LỜI NÓI ĐẦU
2. CHƯƠNG 1: GIỚI THIỆU CÔNG TY HOÀNG KHANG INCOTECH
3. CHƯƠNG 2: GIỚI THIỆU DỰ ÁN SÀN TÍM VI EN
4. CHƯƠNG 3: TỔNG QUAN VỀ PLAYWRIGHT VÀ KIỂM THỬ TỰ ĐỘNG
5. CHƯƠNG 4: PHÂN TÍCH YÊU CẦU VÀ USE CASES
6. CHƯƠNG 5: THIẾT KẾ KIẾN TRÚC TEST
7. CHƯƠNG 6: CÀI ĐẶT VÀ CẤU HÌNH PLAYWRIGHT
8. CHƯƠNG 7: VIẾT TEST CASES - PHƯƠNG PHÁP VÀ BEST PRACTICES
9. CHƯƠNG 8: DATA CRAWLING VÀ CHUẨN BỊ DỮ LIỆU
10. CHƯƠNG 9: CHI TIẾT IMPLEMENTATION
11. CHƯƠNG 10: KẾT LUẬN VÀ HƯỚNG PHÁT TRIỂN
12. PHỤ LỤC A: CONFIGURATION FILES
13. PHỤ LỤC B: CODE REFERENCES
14. TÀI LIỆU THAM KHẢO

---

## LỜI NÓI ĐẦU

Kiểm thử phần mềm là một giai đoạn quan trọng và không thể thiếu trong vòng đời phát triển phần mềm hiện đại. Mục tiêu chính của kiểm thử là phát hiện các lỗi tiềm ẩn, đảm bảo chất lượng sản phẩm, xác miễn rằng hệ thống hoạt động đúng theo các yêu cầu đã được định rõ, và cải thiện độ tin cậy của ứng dụng trước khi đưa vào sử dụng thực tế.

Trong bối cảnh công nghệ phát triển nhanh chóng và các yêu cầu về chất lượng phần mềm ngày càng cao, kiểm thử tự động trở thành một công cụ không thể thiếu để giảm thời gian kiểm thử, tăng độ chính xác, và cải thiện hiệu suất của quá trình phát triển. Thay vì thực hiện các test case một cách thủ công - một quá trình vừa tốn thời gian vừa dễ xảy ra sai sót - kiểm thử tự động cho phép các nhà kiểm thử viết các script tự động để thực hiện các test một cách lặp đi lặp lại một cách chính xác và hiệu quả.

Playwright là một framework kiểm thử tự động mạnh mẽ được phát triển bởi Microsoft. Nó cho phép các nhà kiểm thử viết các test case tự động để kiểm thử các ứng dụng web trên nhiều trình duyệt khác nhau (Chromium, Firefox, WebKit) và cung cấp các API mạnh mẽ để tương tác với các ứng dụng web. Với Playwright, các nhà kiểm thử có thể viết các test case một lần và chạy trên nhiều trình duyệt mà không cần phải thay đổi code, tiết kiệm đáng kể thời gian và công sức.

Kỳ thực tập tốt nghiệp này cung cấp cơ hội quý báu để áp dụng các kiến thức lý thuyết vào thực tiễn, đặc biệt trong lĩnh vực kiểm thử phần mềm tự động sử dụng Playwright. Báo cáo này trình bày các kiến thức, kỹ năng, và kinh nghiệm quý báu thu được trong quá trình thực tập tại Công ty Hoàng Khang Incotech, một công ty công nghệ thông tin hàng đầu với nhiều dự án quy mô lớn và những tiêu chuẩn chất lượng cao.

Nội dung chính của báo cáo tập trung vào xây dựng và kiểm thử website bán hàng "Sàn Tím Vi En" - một nền tảng thương mại điện tử hoàn chỉnh. Báo cáo sẽ trình bày chi tiết về công ty, dự án, các công nghệ sử dụng, phương pháp kiểm thử, cách thiết kế test framework theo best practices, và các bài học kinh nghiệm quý báu thu được trong quá trình thực tập.

[Hình: Logo công ty Hoàng Khang Incotech, Logo Sàn Tím Vi En, Giao diện website Sàn Tím Vi En]

---

## KỲ VỌNG VÀ MỤC TIÊU CỦA THỰC TẬP

Trước khi bắt đầu thực tập, tôi đã đặt ra các mục tiêu và kỳ vọng cụ thể:

**Mục tiêu chính:**
1. Hiểu rõ quy trình kiểm thử phần mềm chuyên nghiệp trong một công ty công nghệ thực tế
2. Nắm vững Playwright framework và các tính năng chính
3. Viết được các test case hoàn chỉnh cho website bán hàng
4. Học cách quản lý dữ liệu kiểm thử hiệu quả
5. Tích hợp test vào pipeline CI/CD

**Kỳ vọng từ công ty:**
- Được hướng dẫn bởi các kỹ sư kinh nghiệm
- Làm việc trên các dự án thực tế
- Học hỏi các best practices trong ngành
- Cải thiện kỹ năng lập trình và kiểm thử

**Kỳ vọng từ bản thân:**
- Hoàn thành tất cả các nhiệm vụ được giao
- Chủ động học hỏi và cải thiện
- Đóng góp ý tưởng và sáng kiến
- Xây dựng mối quan hệ tốt với đồng nghiệp

---

## QUY TRÌNH THỰC TẬP

Quá trình thực tập được chia thành các giai đoạn sau:

**Giai đoạn 1: Làm quen (Tuần 1-2, từ ngày 13/04 đến 24/04)**
- Làm quen với công ty, môi trường làm việc
- Tìm hiểu cấu trúc tổ chức
- Học hỏi về các dự án đang thực hiện
- Thiết lập môi trường phát triển

**Giai đoạn 2: Tìm hiểu công nghệ (Tuần 3-4, từ ngày 27/04 đến 08/05)**
- Tìm hiểu Laravel framework
- Tìm hiểu Playwright framework
- Tìm hiểu Postman
- Chuẩn bị các công cụ cần thiết

**Giai đoạn 3: Viết test case (Tuần 5-8, từ ngày 11/05 đến 31/05)**
- Viết các test case cho authentication
- Viết các test case cho products management
- Viết các test case cho shopping cart
- Viết các test case cho checkout
- Viết các test case cho admin dashboard

**Giai đoạn 4: Hoàn thiện và kiểm thử (Tuần 9-10, từ ngày 03/06 đến 14/06)**
- Kiểm tra lại các test case
- Cải tiến và tối ưu hóa
- Tích hợp vào CI/CD
- Viết tài liệu

**Giai đoạn 5: Báo cáo (Tuần 11-14, từ ngày 17/06 đến 25/06)**
- Viết báo cáo
- Chuẩn bị bài thuyết trình
- Nhận xét từ các thầy cô

---

**Tiếp tục ở Phần 2...**

[Hình: Lịch trình thực tập, Quy trình kiểm thử]
