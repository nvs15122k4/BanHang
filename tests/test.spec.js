import { test, expect } from '@playwright/test';

async function dienThongTinDangNhap(page, user) {
    await page.getByLabel('Email').fill(user.email);
    await page.getByLabel('Mật Khẩu').fill(user.password);
}

async function dienThongTinSanPham(page, sanPham) {
    await page.fill('input[name="ten_sp"]', sanPham.ten);
    await page.fill('input[name="gia"]', sanPham.gia);
    await page.fill('input[name="so_luong"]', sanPham.soLuong);
    await page.selectOption('select[name="trang_thai"]', { label: sanPham.trangThai });
    await page.fill('textarea[name="mo_ta"]', sanPham.moTa);
};

//Dang nhap thanh cong
test ('dangNhapThanhCong', async({page})=>{
    await page.goto('localhost/login');
    await dienThongTinDangNhap(page,{
        email:'admin@gmail.com',
        password:'12345678'
    });
    await page.click('button[type="submit"]')
});

//Sai mat khau
test ('saiMatKhau', async({page})=>{
    await page.goto('localhost/login');
    await dienThongTinDangNhap(page,{
        email:'admin@gmail.com',
        password:'12345678abc'
    });
    await page.click('button[type="submit"]')
});

//Email trong
test('emailTrong',async({page})=>{
    await page.goto('localhost/login');
    const emailInput = page.locator('#email');
    
    await dienThongTinDangNhap(page,{
        email:'',
        password:'12345678abc'
    });
    await page.click('button[type="submit"]')

    const validationMessage = await emailInput.evaluate((element) => element.validationMessage);
    expect(validationMessage).not.toBe('');
});

//Email ko hop le
test('emaiKhongHopLe' ,async({page})=>{
    await page.goto('localhost/login');
    const emailInput = page.locator('#email');

    await dienThongTinDangNhap(page,{
        email:'abc',
        password:'12345678abc'
    });
    await page.click('button[type="submit"]')
    await emailInput.evaluate(el => el.validationMessage)
});

//Dang nhap va them san pham
test('dangNhapThemSP', async ({ page }) => {
    await page.goto('localhost');
    await page.goto('localhost/login');

    await dienThongTinDangNhap (page, {
        email: 'admin2@example.com',
        password: '123'
    });
    await page.click('button[type="submit"]');

    await page.goto('localhost/products');
    await page.getByRole('button',{name:"Thêm sản phẩm"}).click();
    
    await dienThongTinSanPham(page, {
        ten: 'Quan',
        gia: '10000000',
        soLuong: '100',
        trangThai: 'Hết hàng',
        moTa: 'quan ao test'
    });
    await page.getByRole('button', { name: "Lưu" }).click();

    await page.fill('input[name="search"]', 'Quan');
    await page.getByRole('button',{name:"Lọc"}).click();

    await expect(page.locator('body')).toContainText('Quan');
});

//Dang nhap va tao sp thieu ten
test('dangNhapTaoSPThieuTen', async ({ page }) => {
    await page.goto('localhost');
    await page.goto('localhost/login');

    const emailInput = page.locator('#ten_sp');

    await dienThongTinDangNhap (page, {
        email: 'admin2@example.com',
        password: '123'
    });
    await page.click('button[type="submit"]');

    await page.goto('localhost/products');
    await page.getByRole('button',{name:"Thêm sản phẩm"}).click();
    
    await dienThongTinSanPham(page, {
        ten: '',
        gia: '10000000',
        soLuong: '100',
        trangThai: 'Hết hàng',
        moTa: 'quan ao test'
    });
    await page.getByRole('button', { name: "Lưu" }).click();
    const validationMessage = await emailInput.evaluate((element) => element.validationMessage);
    expect(validationMessage).not.toBe('');
});

//Dang nhap va tao sp gia am
test('dangNhapTaoSPGiaAm', async ({ page }) => {
    await page.goto('localhost');
    await page.goto('localhost/login');

    const emailInput = page.locator('#gia');

    await dienThongTinDangNhap (page, {
        email: 'admin2@example.com',
        password: '123'
    });
    await page.click('button[type="submit"]');

    await page.goto('localhost/products');
    await page.getByRole('button',{name:"Thêm sản phẩm"}).click();
    
    await dienThongTinSanPham(page, {
        ten: 'Quan',
        gia: '-10000000',
        soLuong: '100',
        trangThai: 'Hết hàng',
        moTa: 'quan ao test'
    });
    await page.getByRole('button', { name: "Lưu" }).click();

    const validationMessage = await emailInput.evaluate((element) => element.validationMessage);
    expect(validationMessage).not.toBe('');
});

//Dang nhap va tao sp so luong am
test('dangNhapTaoSPSoLuongAm', async ({ page }) => {
    await page.goto('localhost');
    await page.goto('localhost/login');

    const emailInput = page.locator('#so_luong');

    await dienThongTinDangNhap (page, {
        email: 'admin2@example.com',
        password: '123'
    });
    await page.click('button[type="submit"]');

    await page.goto('localhost/products');
    await page.getByRole('button',{name:"Thêm sản phẩm"}).click();
    
    await dienThongTinSanPham(page, {
        ten: 'Quan',
        gia: '10000000',
        soLuong: '-100',
        trangThai: 'Hết hàng',
        moTa: 'quan ao test'
    });
    await page.getByRole('button', { name: "Lưu" }).click();

    const validationMessage = await emailInput.evaluate((element) => element.validationMessage);
    expect(validationMessage).not.toBe('');
});