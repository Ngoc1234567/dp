# Template trang DP

Trong WordPress Admin → Trang → Thêm/Sửa trang → Thuộc tính trang → Mẫu, chọn template tương ứng. Nhập nội dung trong trình biên tập; ảnh đại diện hiển thị phía trên nội dung. Các trang dùng chung font, màu và header/footer DP.

| Template | Công dụng |
| --- | --- |
| DP — Giới thiệu | Nội dung doanh nghiệp, tầm nhìn và giá trị |
| DP — Phụ tùng | Danh mục và hướng dẫn yêu cầu phụ tùng |
| DP — Dịch vụ | Phạm vi dịch vụ kỹ thuật |
| DP — Giải pháp | Giải pháp theo ngành và ứng dụng |
| DP — Liên hệ | Nội dung liên hệ, thông tin từ Tùy biến, hỗ trợ shortcode form |
| DP — Tin tức | Danh sách bài viết, trạng thái trống và phân trang |
| DP — Catalogue sản phẩm | Các nhóm sản phẩm, danh sách thiết bị và phân trang |

`page.php` là template mặc định; `single.php` cho chi tiết bài viết; `archive.php` cho chuyên mục, thẻ và kho bài viết; `home.php` cho trang bài viết được cấu hình trong Cài đặt → Đọc; `404.php` cho đường dẫn không tồn tại. Template sản phẩm cũ `archive-dp_product.php`, `taxonomy-dp_category.php`, `single-dp_product.php` vẫn hoạt động.

Khởi tạo 7 trang từ nội dung hồ sơ: chạy `D:\DP\.runtime\php84\php.exe tools/setup-pages.php` từ thư mục WordPress. Script giữ nguyên các trang đã tồn tại, không ghi đè nội dung hoặc template đã chọn. Trang chủ và cài đặt trang bài viết hiện tại không bị thay đổi.

Trang Tin tức dùng template riêng, không cần chọn làm Posts page trong Cài đặt → Đọc. Xuất bản bài thật trong mục Bài viết để hiển thị; bài hello-world mặc định được loại khỏi template Tin tức.

Để dùng Contact Form 7, tạo form trong mục Contact, cấu hình người nhận và chèn shortcode của form vào nội dung trang Liên hệ. Template đã có CSS cho các trường; không tự tạo form, gửi email thử hoặc thay đổi cấu hình email. Cần kiểm tra gửi nhận riêng sau khi cấu hình mail/SMTP.

Menu giữ 5 mục đã yêu cầu; Phụ tùng, Dịch vụ, Giải pháp dẫn đến trang riêng. Các trang Giới thiệu, Tin tức, Liên hệ có liên kết trong sidebar và footer. Không stage file trong quá trình tạo template.
