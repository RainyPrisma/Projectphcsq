// paginationControl.js
//สำหรับตารางที่มีการแบ่งหน้า
class PaginationControl {
    constructor(itemsSelector, itemsPerPage = 10, containerId = 'pagination-container') {
        this.itemsSelector = itemsSelector; // ตัวเลือก CSS สำหรับรายการ (เช่น 'tbody tr')
        this.itemsPerPage = itemsPerPage;   // จำนวนรายการต่อหน้า (ปรับเป็น 10 เพื่อเหมาะกับตาราง)
        this.currentPage = 1;               // หน้าปัจจุบัน
        this.totalItems = 0;                // จำนวนรายการทั้งหมด
        this.totalPages = 0;                // จำนวนหน้าทั้งหมด
        this.containerId = containerId;     // ID ของคอนเทนเนอร์ pagination

        this.setupPaginationControls();
        this.filterItemsByPage();
    }

    setupPaginationControls() {
        let paginationContainer = document.getElementById(this.containerId);
        if (!paginationContainer) {
            paginationContainer = document.createElement('div');
            paginationContainer.id = this.containerId;
            paginationContainer.className = 'pagination-container text-center mt-3';
            const parentContainer = document.querySelector(this.itemsSelector)?.closest('table');
            if (parentContainer) {
                parentContainer.insertAdjacentElement('afterend', paginationContainer);
            }
        }

        this.renderPagination();
    }

    renderPagination() {
        const paginationContainer = document.getElementById(this.containerId);
        if (!paginationContainer) return;

        const items = document.querySelectorAll(this.itemsSelector);
        this.totalItems = items.length;
        this.totalPages = Math.ceil(this.totalItems / this.itemsPerPage);

        paginationContainer.innerHTML = '';

        const prevButton = document.createElement('button');
        prevButton.innerHTML = '«';
        prevButton.className = 'btn btn-outline-secondary mx-1';
        prevButton.disabled = this.currentPage === 1;
        prevButton.addEventListener('click', () => this.changePage(this.currentPage - 1));
        paginationContainer.appendChild(prevButton);

        for (let i = 1; i <= this.totalPages; i++) {
            const pageButton = document.createElement('button');
            pageButton.textContent = i;
            pageButton.className = `btn btn-${i === this.currentPage ? 'primary' : 'outline-secondary'} mx-1`;
            pageButton.addEventListener('click', () => this.changePage(i));
            paginationContainer.appendChild(pageButton);
        }

        const nextButton = document.createElement('button');
        nextButton.innerHTML = '»';
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
        const items = document.querySelectorAll(this.itemsSelector);
        const startIndex = (this.currentPage - 1) * this.itemsPerPage;
        const endIndex = startIndex + this.itemsPerPage;

        items.forEach((item, index) => {
            item.style.display = (index >= startIndex && index < endIndex) ? '' : 'none';
            // ใช้ '' แทน 'block' เพราะ <tr> ต้องการ display: table-row โดยธรรมชาติ
        });
    }

    update() {
        this.currentPage = 1;
        this.filterItemsByPage();
        this.renderPagination();
    }

    static initialize(itemsSelector, itemsPerPage, containerId) {
        return new PaginationControl(itemsSelector, itemsPerPage, containerId);
    }
}

// เรียกใช้งานเมื่อโหลดหน้า
document.addEventListener('DOMContentLoaded', function() {
    PaginationControl.initialize('tbody tr', 10, 'pagination-container');
});