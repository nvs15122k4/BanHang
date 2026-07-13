import re

with open("/home/nvs1512/Project IT/San-tim-vien/BanHang/docs/BaoCao_ThucTap_Restructured.md", "r", encoding="utf-8") as f:
    lines = f.readlines()

out = []
ch = 0
sec = 0
subsec = 0

for line in lines:
    if line.startswith("## CHƯƠNG"):
        ch += 1
        sec = 0
        subsec = 0
        out.append(line)
        continue
    
    # Check if it's one of the main injected sections
    m = re.match(r'^### \d+\.\d+ (Giới thiệu về cơ quan thực tập|Giới thiệu về nội dung công việc được giao thực tập|Phạm vi của đề tài|Lý thuyết|Kỹ thuật|Xây dựng và đề xuất mô hình ứng dụng|Phương pháp nghiên cứu/ hướng giải quyết vấn đề|Mô tả chi tiết phương pháp thực hiện|Mô tả các kết quả đạt được)', line)
    if m:
        sec += 1
        subsec = 0
        out.append(f"### {ch}.{sec} {m.group(1)}\n")
        continue
        
    # Check if it's an old section
    m_old = re.match(r'^### \d+\.\d+ (.*)', line)
    if m_old:
        if ch == 4:
            sec += 1
            out.append(f"### {ch}.{sec} {m_old.group(1)}\n")
        else:
            subsec += 1
            out.append(f"#### {ch}.{sec}.{subsec} {m_old.group(1)}\n")
        continue
        
    # Also fix lists that got numbered continuously like "15. ", "16. " to "- " or standard numbers.
    m_list = re.match(r'^(\s*)\d+\.\s+(.*)', line)
    if m_list and not line.startswith("1.") and not line.startswith("2.") and not line.startswith("3.") and not line.startswith("4.") and not line.startswith("5.") and not line.startswith("6.") and not line.startswith("7.") and not line.startswith("8.") and not line.startswith("9.") and not line.startswith("10."):
        # Actually it's safer to not touch lists unless we are sure. The user asked "chỉnh sửa các sô thứ tự trên các chương và các đề mục", so I will focus on headers.
        pass

    out.append(line)

with open("/home/nvs1512/Project IT/San-tim-vien/BanHang/docs/BaoCao_ThucTap_Restructured.md", "w", encoding="utf-8") as f:
    f.writelines(out)
