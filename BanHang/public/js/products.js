/**
 * Products Management JavaScript
 * Handles CRUD operations via AJAX for products
 */

class ProductManager {
    constructor() {
        this.currentProductId = null;
        this.isEditMode = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        this.initializeEventListeners();
    }

    initializeEventListeners() {
        // Form submission
        const productForm = document.getElementById('productForm');
        if (productForm) {
            productForm.addEventListener('submit', (e) => this.handleFormSubmit(e));
        }

        // Delete confirmation
        const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', () => this.confirmDelete());
        }

        // Image preview
        const imageInput = document.getElementById('hinh_anh');
        if (imageInput) {
            imageInput.addEventListener('change', (e) => this.previewImage(e));
        }
    }

    // Change items per page
    changePerPage(perPage) {
        const url = new URL(window.location);
        url.searchParams.set('per_page', perPage);
        url.searchParams.delete('page'); // Reset to first page
        window.location.href = url.toString();
    }

    // Export functions
    exportProducts() {
        this.showLoadingToast('Đang xuất file Excel...');
        window.location.href = '/export/products';
    }

    exportStatistics() {
        this.showLoadingToast('Đang xuất file thống kê...');
        window.location.href = '/export/statistics';
    }

    // Open create modal
    openCreateModal() {
        this.isEditMode = false;
        this.currentProductId = null;
        
        document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-plus me-2"></i>Thêm sản phẩm mới';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Thêm';
        
        // Reset form
        document.getElementById('productForm').reset();
        this.clearValidationErrors();
        document.getElementById('currentImagePreview').style.display = 'none';
        
        // Show modal
        new bootstrap.Modal(document.getElementById('productModal')).show();
    }

    // View product details
    async viewProduct(productId) {
        const modal = new bootstrap.Modal(document.getElementById('viewProductModal'));
        modal.show();
        
        // Show loading
        document.getElementById('viewProductContent').innerHTML = this.getLoadingHTML();
        
        try {
            const response = await this.fetchWithAuth(`/products/${productId}`);
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const product = await response.json();
            
            document.getElementById('viewProductContent').innerHTML = this.generateProductDetailHTML(product);
        } catch (error) {
            document.getElementById('viewProductContent').innerHTML = this.getErrorHTML('Không thể tải thông tin sản phẩm. Vui lòng thử lại.');
        }
    }

    // Edit product
    async editProduct(productId) {
        this.isEditMode = true;
        this.currentProductId = productId;
        
        document.getElementById('productModalTitle').innerHTML = '<i class="fas fa-edit me-2"></i>Chỉnh sửa sản phẩm';
        document.getElementById('submitBtn').innerHTML = '<i class="fas fa-save me-1"></i>Cập nhật';
        
        try {
            const response = await this.fetchWithAuth(`/products/${productId}/edit`);
            
            if (!response.ok) throw new Error('Network response was not ok');
            
            const product = await response.json();
            
            // Fill form with product data
            this.populateForm(product);
            this.clearValidationErrors();
            
            // Show modal
            new bootstrap.Modal(document.getElementById('productModal')).show();
            
        } catch (error) {
            this.showErrorToast('Không thể tải thông tin sản phẩm. Vui lòng thử lại.');
        }
    }

    // Delete product
    deleteProduct(productId, productName) {
        this.currentProductId = productId;
        document.getElementById('deleteProductName').textContent = productName;
        
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Confirm delete
    async confirmDelete() {
        if (!this.currentProductId) return;
        
        const btn = document.getElementById('confirmDeleteBtn');
        const originalText = btn.innerHTML;
        
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang xóa...';
        
        try {
            const response = await this.fetchWithAuth(`/products/${this.currentProductId}`, {
                method: 'DELETE'
            });
            
            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('deleteModal')).hide();
                this.showSuccessToast('Sản phẩm đã được xóa thành công!');
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorData = await response.json();
                this.showErrorToast('Lỗi: ' + (errorData.message || 'Không thể xóa sản phẩm'));
            }
        } catch (error) {
            this.showErrorToast('Có lỗi xảy ra khi xóa sản phẩm');
        } finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    // Handle form submission
    async handleFormSubmit(e) {
        e.preventDefault();
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Đang lưu...';
        
        this.clearValidationErrors();
        
        try {
            const formData = new FormData(e.target);
            
            let url, method;
            if (this.isEditMode) {
                url = `/products/${this.currentProductId}`;
                method = 'POST';
                formData.append('_method', 'PUT');
            } else {
                url = '/products';
                method = 'POST';
            }
            
            const response = await this.fetchWithAuth(url, {
                method: method,
                body: formData
            });
            
            if (response.ok) {
                bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
                this.showSuccessToast(this.isEditMode ? 'Sản phẩm đã được cập nhật!' : 'Sản phẩm đã được thêm!');
                setTimeout(() => location.reload(), 1000);
            } else {
                const errorData = await response.json();
                
                if (errorData.errors) {
                    this.displayValidationErrors(errorData.errors);
                } else {
                    this.showErrorToast('Lỗi: ' + (errorData.message || 'Không thể lưu sản phẩm'));
                }
            }
        } catch (error) {
            this.showErrorToast('Có lỗi xảy ra khi lưu sản phẩm');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    }

    // Utility methods
    async fetchWithAuth(url, options = {}) {
        return fetch(url, {
            ...options,
            headers: {
                'X-CSRF-TOKEN': this.csrfToken,
                'Accept': 'application/json',
                ...options.headers
            }
        });
    }

    populateForm(product) {
        document.getElementById('ten_sp').value = product.ten_sp;
        document.getElementById('gia').value = product.gia;
        document.getElementById('so_luong').value = product.so_luong;
        document.getElementById('trang_thai').value = product.trang_thai;
        document.getElementById('mo_ta').value = product.mo_ta || '';
        
        // Show current image if exists
        if (product.image_path && !product.image_path.includes('default-product')) {
            document.getElementById('currentImage').src = product.image_path;
            document.getElementById('currentImagePreview').style.display = 'block';
        } else {
            document.getElementById('currentImagePreview').style.display = 'none';
        }
    }

    clearValidationErrors() {
        document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    }

    displayValidationErrors(errors) {
        for (const [field, messages] of Object.entries(errors)) {
            const input = document.getElementById(field);
            if (input) {
                input.classList.add('is-invalid');
                const feedback = input.parentNode.querySelector('.invalid-feedback');
                if (feedback) {
                    feedback.textContent = messages[0];
                }
            }
        }
    }

    previewImage(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let preview = document.getElementById('imagePreview');
                if (!preview) {
                    preview = document.createElement('div');
                    preview.id = 'imagePreview';
                    preview.className = 'mt-2';
                    preview.innerHTML = '<label class="form-label">Xem trước:</label><br><img id="previewImg" class="img-thumbnail" style="max-height: 150px;">';
                    document.getElementById('hinh_anh').parentNode.appendChild(preview);
                }
                document.getElementById('previewImg').src = e.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    generateProductDetailHTML(product) {
        return `
            <div class="row">
                <div class="col-md-4">
                    <img src="${product.image_path}" alt="${product.ten_sp}" 
                         class="img-fluid rounded" 
                         onerror="this.src='/images/default-product.svg'">
                </div>
                <div class="col-md-8">
                    <h4 class="fw-bold text-primary">${product.ten_sp}</h4>
                    <div class="mb-3">
                        <span class="badge ${product.trang_thai === 'con' ? 'bg-success' : 'bg-warning text-dark'}">
                            ${product.trang_thai === 'con' ? 'Còn hàng' : 'Hết hàng'}
                        </span>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-6">
                            <strong>Giá:</strong><br>
                            <span class="text-primary fs-5 fw-bold">${new Intl.NumberFormat('vi-VN').format(product.gia)}đ</span>
                        </div>
                        <div class="col-6">
                            <strong>Số lượng:</strong><br>
                            <span class="fs-5">${product.so_luong}</span>
                        </div>
                        <div class="col-12">
                            <strong>Mô tả:</strong><br>
                            <p class="text-muted">${product.mo_ta || 'Không có mô tả'}</p>
                        </div>
                        <div class="col-6">
                            <strong>Ngày tạo:</strong><br>
                            <small class="text-muted">${new Date(product.created_at).toLocaleString('vi-VN')}</small>
                        </div>
                        <div class="col-6">
                            <strong>Cập nhật:</strong><br>
                            <small class="text-muted">${new Date(product.updated_at).toLocaleString('vi-VN')}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    getLoadingHTML() {
        return `
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Đang tải...</span>
                </div>
            </div>
        `;
    }

    getErrorHTML(message) {
        return `
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-triangle me-2"></i>
                ${message}
            </div>
        `;
    }

    showSuccessToast(message) {
        this.showToast(message, 'success');
    }

    showErrorToast(message) {
        this.showToast(message, 'danger');
    }

    showLoadingToast(message) {
        this.showToast(message, 'info');
    }

    showToast(message, type = 'info') {
        // Create toast container if not exists
        let container = document.getElementById('toast-container');
        if (!container) {
            container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'position-fixed top-0 end-0 p-3';
            container.style.zIndex = '9999';
            document.body.appendChild(container);
        }

        // Create toast
        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast align-items-center text-white bg-${type} border-0`;
        toast.setAttribute('role', 'alert');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        `;

        container.appendChild(toast);

        // Show toast
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();

        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', () => {
            toast.remove();
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    window.productManager = new ProductManager();
});

// Global functions for backward compatibility
function changePerPage(perPage) {
    window.productManager.changePerPage(perPage);
}

function exportProducts() {
    window.productManager.exportProducts();
}

function exportStatistics() {
    window.productManager.exportStatistics();
}

function openCreateModal() {
    window.productManager.openCreateModal();
}

function viewProduct(productId) {
    window.productManager.viewProduct(productId);
}

function editProduct(productId) {
    window.productManager.editProduct(productId);
}

function deleteProduct(productId, productName) {
    window.productManager.deleteProduct(productId, productName);
}