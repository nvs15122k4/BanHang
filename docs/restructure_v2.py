import re

with open("/home/nvs1512/Project IT/San-tim-vien/BanHang/docs/BaoCao_ThucTap.md", "r", encoding="utf-8") as f:
    text = f.read()

# Split by chapters
parts = re.split(r'(## CHƯƠNG \d+:.*?)\n', text)

intro = parts[0]
chapters = {}

for i in range(1, len(parts), 2):
    header = parts[i]
    content = parts[i+1]
    ch_num = int(re.search(r'CHƯƠNG (\d+)', header).group(1))
    chapters[ch_num] = header + "\n" + content

new_doc = []

# Keep intro
new_doc.append(intro)

# NEW Chương 1: Tổng quan
new_doc.append("## CHƯƠNG 1: TỔNG QUAN\n\n### 1.1 Giới thiệu về cơ quan thực tập\n")
ch1_content = re.sub(r'^## CHƯƠNG 1:.*?\n', '', chapters[1], flags=re.MULTILINE)
new_doc.append(ch1_content)

new_doc.append("\n### 1.2 Giới thiệu về nội dung công việc được giao thực tập\n")
ch2_content = re.sub(r'^## CHƯƠNG 2:.*?\n', '', chapters[2], flags=re.MULTILINE)
new_doc.append(ch2_content)

new_doc.append("\n### 1.3 Phạm vi của đề tài\n")
new_doc.append("Phạm vi của đề tài tập trung vào việc xây dựng và triển khai kiểm thử tự động (Automation Testing) cho nền tảng thương mại điện tử Sàn Tím Vi En. Các module chính bao gồm Xác thực người dùng, Quản lý sản phẩm, Giỏ hàng, Thanh toán và Quản lý Admin bằng việc sử dụng Playwright framework.\n")

# NEW Chương 2: Cơ sở lý thuyết
new_doc.append("\n## CHƯƠNG 2: CƠ SỞ LÝ THUYẾT\n\n### 2.1 Lý thuyết\n")
ch3_content = re.sub(r'^## CHƯƠNG 3:.*?\n', '', chapters[3], flags=re.MULTILINE)
new_doc.append(ch3_content)

new_doc.append("\n### 2.2 Kỹ thuật\n")
new_doc.append("Các kỹ thuật được sử dụng bao gồm: Playwright, Node.js, TypeScript, Laravel, Vue.js.\n")

new_doc.append("\n### 2.3 Xây dựng và đề xuất mô hình ứng dụng\n")
ch5_content = re.sub(r'^## CHƯƠNG 5:.*?\n', '', chapters[5], flags=re.MULTILINE)
new_doc.append(ch5_content)

# NEW Chương 3: Cài đặt thử nghiệm
new_doc.append("\n## CHƯƠNG 3: CÀI ĐẶT THỬ NGHIỆM\n\n### 3.1 Phương pháp nghiên cứu/ hướng giải quyết vấn đề\n")
ch4_content = re.sub(r'^## CHƯƠNG 4:.*?\n', '', chapters[4], flags=re.MULTILINE)
ch7_content = re.sub(r'^## CHƯƠNG 7:.*?\n', '', chapters[7], flags=re.MULTILINE)
new_doc.append(ch4_content + "\n" + ch7_content)

new_doc.append("\n### 3.2 Mô tả chi tiết phương pháp thực hiện\n")
ch6_content = re.sub(r'^## CHƯƠNG 6:.*?\n', '', chapters[6], flags=re.MULTILINE)
ch8_content = re.sub(r'^## CHƯƠNG 8:.*?\n', '', chapters[8], flags=re.MULTILINE)
new_doc.append(ch6_content + "\n" + ch8_content)

new_doc.append("\n### 3.3 Mô tả các kết quả đạt được\n")
ch9_content = re.sub(r'^## CHƯƠNG 9:.*?\n', '', chapters[9], flags=re.MULTILINE)
new_doc.append(ch9_content)

# NEW Chương 4: Kết luận
new_doc.append("\n## CHƯƠNG 4: KẾT LUẬN\n")
ch10_content = re.sub(r'^## CHƯƠNG 10:.*?\n', '', chapters[10], flags=re.MULTILINE)
new_doc.append(ch10_content)

raw_text = "".join(new_doc)

out = []
ch = 0
sec = 0
subsec = 0

for line in raw_text.splitlines(True):
    if line.startswith("## CHƯƠNG"):
        ch += 1
        sec = 0
        subsec = 0
        out.append(line)
        continue
    
    # EXACT matching for main sections
    m = re.match(r'^### (1\.1 Giới thiệu về cơ quan thực tập|1\.2 Giới thiệu về nội dung công việc được giao thực tập|1\.3 Phạm vi của đề tài|2\.1 Lý thuyết|2\.2 Kỹ thuật|2\.3 Xây dựng và đề xuất mô hình ứng dụng|3\.1 Phương pháp nghiên cứu/ hướng giải quyết vấn đề|3\.2 Mô tả chi tiết phương pháp thực hiện|3\.3 Mô tả các kết quả đạt được)', line)
    if m:
        sec += 1
        subsec = 0
        out.append(f"### {ch}.{sec} {m.group(1).split(' ', 1)[1]}\n")
        continue
        
    # Old sections
    m_old = re.match(r'^### \d+\.\d+ (.*)', line)
    if m_old:
        if ch == 4:
            sec += 1
            out.append(f"### {ch}.{sec} {m_old.group(1)}\n")
        else:
            subsec += 1
            out.append(f"#### {ch}.{sec}.{subsec} {m_old.group(1)}\n")
        continue

    out.append(line)

with open("/home/nvs1512/Project IT/San-tim-vien/BanHang/docs/BaoCao_ThucTap_Restructured.md", "w", encoding="utf-8") as f:
    f.writelines(out)
