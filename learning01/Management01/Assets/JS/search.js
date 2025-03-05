document.addEventListener('DOMContentLoaded', function() {
    class SeafoodGalleryManager {
        constructor() {
            this.searchInput = document.getElementById('searchInput');
            this.searchButton = document.getElementById('searchButton');
            this.sortSelect = document.getElementById('sort');
            this.searchTimeout = null;
            this.dropdownBtn = document.querySelector('.dropdown-btn');
            
            // การตั้งค่าการแบ่งหน้า
            this.currentPage = 1;
            this.itemsPerPage = 20; // สามารถปรับได้
            this.totalItems = 0;
            this.totalPages = 0;
            
            this.initializeEventListeners();
            this.setupPaginationControls();

            this.filterItemsByPage();
        }

        initializeEventListeners() {
            this.setupSearchFunctionality();
            this.setupQuantityControls();
            this.setupSorting();
            this.setupDropdownMenu();
        }

        setupSearchFunctionality() {
            if (this.searchInput) {
                // Real-time search with debounce
                this.searchInput.addEventListener('input', () => {
                    clearTimeout(this.searchTimeout);
                    this.searchTimeout = setTimeout(() => this.performSearch(), 500);
                });

                // Search on Enter key
                this.searchInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        clearTimeout(this.searchTimeout);
                        this.performSearch();
                    }
                });
            }

            // Search button
            if (this.searchButton) {
                this.searchButton.addEventListener('click', () => {
                    clearTimeout(this.searchTimeout);
                    this.performSearch();
                });
            }
        }

        performSearch() {
            const searchTerm = this.searchInput.value.trim();
            const urlParams = new URLSearchParams(window.location.search);

            // จัดการพารามิเตอร์การค้นหา
            if (searchTerm) {
                urlParams.set('search', searchTerm);
            } else {
                urlParams.delete('search');
            }

            // ดึงค่าฟิลเตอร์เดิม
            const filters = {
                category: this.searchInput.getAttribute('data-category') || '',
                priceMin: parseInt(this.searchInput.getAttribute('data-price-min') || 0),
                priceMax: parseInt(this.searchInput.getAttribute('data-price-max') || 1000000)
            };

            // เพิ่มฟิลเตอร์เดิม
            if (filters.category) urlParams.set('category', filters.category);
            if (filters.priceMin > 0) urlParams.set('price_min', filters.priceMin);
            if (filters.priceMax < 1000000) urlParams.set('price_max', filters.priceMax);

            // เปลี่ยนหน้าพร้อมพารามิเตอร์
            window.location.href = 'gallery.php?' + urlParams.toString();
        }

        setupQuantityControls() {
            // ปุ่มลด quantity
            document.querySelectorAll('.quantity-btn.minus').forEach(button => {
                button.addEventListener('click', () => {
                    const input = button.parentNode.querySelector('.quantity-input');
                    const value = parseInt(input.value);
                    if (value > 1) {
                        input.value = value - 1;
                    }
                });
            });

            // ปุ่มเพิ่ม quantity
            document.querySelectorAll('.quantity-btn.plus').forEach(button => {
                button.addEventListener('click', () => {
                    const input = button.parentNode.querySelector('.quantity-input');
                    const value = parseInt(input.value);
                    const max = parseInt(input.getAttribute('max'));
                    if (value < max) {
                        input.value = value + 1;
                    }
                });
            });
        }

        setupSorting() {
            if (this.sortSelect) {
                this.sortSelect.addEventListener('change', () => {
                    const sortValue = this.sortSelect.value;
                    const container = document.querySelector('.gallery-container');
                    const items = Array.from(document.querySelectorAll('.gallery-item'));
        
                    items.sort((a, b) => {
                        switch(sortValue) {
                            case 'price-low':
                                return this.extractPrice(a) - this.extractPrice(b);
                            case 'price-high':
                                return this.extractPrice(b) - this.extractPrice(a);
                            case 'name':
                                return a.querySelector('h2').textContent.localeCompare(b.querySelector('h2').textContent);
                            default:
                                return 0;
                        }
                    });
        
                    // ล้างและเติมรายการใหม่
                    container.innerHTML = '';
                    items.forEach(item => container.appendChild(item));
        
                    // รีเซ็ตการแบ่งหน้าหลังการเรียงลำดับ
                    this.setupPaginationControls();
                    
                    // เพิ่มบรรทัดนี้เพื่อแสดงสินค้าตามหน้าปัจจุบัน
                    this.filterItemsByPage();
                });
            }
        }

        extractPrice(element) {
            const priceText = element.querySelector('.price').textContent;
            return parseFloat(priceText.replace('฿', '').replace(',', ''));
        }

        setupDropdownMenu() {
            if (this.dropdownBtn) {
                // เปิด/ปิด dropdown
                this.dropdownBtn.addEventListener('click', () => {
                    const dropdownContent = document.querySelector('.dropdown-content');
                    dropdownContent.classList.toggle('show');
                });

                // ปิด dropdown เมื่อคลิกนอก
                window.addEventListener('click', (event) => {
                    if (!event.target.matches('.dropdown-btn') && !event.target.closest('.dropdown-btn')) {
                        const dropdowns = document.querySelectorAll('.dropdown-content');
                        dropdowns.forEach(dropdown => {
                            if (dropdown.classList.contains('show')) {
                                dropdown.classList.remove('show');
                            }
                        });
                    }
                });
            }
        }

        setupPaginationControls() {
            // สร้าง pagination container หากยังไม่มี
            let paginationContainer = document.getElementById('pagination-container');
            if (!paginationContainer) {
                paginationContainer = document.createElement('div');
                paginationContainer.id = 'pagination-container';
                paginationContainer.className = 'pagination-container text-center mt-3';
                
                // วางตำแหน่งหลัง gallery container
                const galleryContainer = document.querySelector('.gallery-container');
                if (galleryContainer) {
                    galleryContainer.insertAdjacentElement('afterend', paginationContainer);
                }
            }

            this.renderPagination();
        }

        renderPagination() {
            const paginationContainer = document.getElementById('pagination-container');
            if (!paginationContainer) return;

            // นับจำนวนสินค้าทั้งหมด
            const items = document.querySelectorAll('.gallery-item');
            this.totalItems = items.length;
            this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

            // สร้าง pagination
            paginationContainer.innerHTML = '';
            
            // ปุ่มก่อนหน้า
            const prevButton = document.createElement('button');
            prevButton.innerHTML = '&laquo;';
            prevButton.className = 'btn btn-outline-secondary mx-1';
            prevButton.disabled = this.currentPage === 1;
            prevButton.addEventListener('click', () => this.changePage(this.currentPage - 1));
            paginationContainer.appendChild(prevButton);

            // ปุ่มหมายเลขหน้า
            for (let i = 1; i <= this.totalPages; i++) {
                const pageButton = document.createElement('button');
                pageButton.textContent = i;
                pageButton.className = `btn btn-${i === this.currentPage ? 'primary' : 'outline-secondary'} mx-1`;
                pageButton.addEventListener('click', () => this.changePage(i));
                paginationContainer.appendChild(pageButton);
            }

            // ปุ่มถัดไป
            const nextButton = document.createElement('button');
            nextButton.innerHTML = '&raquo;';
            nextButton.className = 'btn btn-outline-secondary mx-1';
            nextButton.disabled = this.currentPage === this.totalPages;
            nextButton.addEventListener('click', () => this.changePage(this.currentPage + 1));
            paginationContainer.appendChild(nextButton);
        }

        changePage(pageNumber) {
            if (pageNumber < 1 || pageNumber > this.totalPages) return;

            this.currentPage = pageNumber;
            this.filterItemsByPage();
            this.renderPagination();
        }

        filterItemsByPage() {
            const items = document.querySelectorAll('.gallery-item');
            const startIndex = (this.currentPage - 1) * this.itemsPerPage;
            const endIndex = startIndex + this.itemsPerPage;

            items.forEach((item, index) => {
                item.style.display = (index >= startIndex && index < endIndex) 
                    ? 'block' 
                    : 'none';
            });

            // อัปเดตจำนวนสินค้าที่แสดง
            this.updateProductCount();
        }

        updateProductCount() {
            const productCountElement = document.querySelector('.product-count p');
            if (productCountElement) {
                const visibleItems = document.querySelectorAll('.gallery-item:not([style*="display: none"])');
                productCountElement.innerHTML = `<i class="fas fa-box"></i> Showing ${visibleItems.length} products`;
            }
        }

        static initialize() {
            return new SeafoodGalleryManager();
        }
    }

    // เรียกใช้เมื่อโหลดหน้า
    SeafoodGalleryManager.initialize();
});