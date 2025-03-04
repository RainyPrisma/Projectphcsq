document.addEventListener('DOMContentLoaded', function() {
    // Handle search functionality
    const searchInput = document.getElementById('searchInput');
    const searchButton = document.getElementById('searchButton');
    
    // Real-time search functionality
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            // Clear previous timeout
            clearTimeout(searchTimeout);
            
            // Set a small delay to avoid making requests on every keystroke
            searchTimeout = setTimeout(function() {
                performSearch();
            }, 500); // 500ms delay
        });
    }
    
    function performSearch() {
        const searchTerm = searchInput.value.trim();
        
        // Create URL with search parameters and existing filters
        const urlParams = new URLSearchParams(window.location.search);
        
        // Only set search parameter if it has a value
        if (searchTerm) {
            urlParams.set('search', searchTerm);
        } else {
            urlParams.delete('search');
        }
        
        // Get current filters from data attributes
        const categoryFilter = searchInput.getAttribute('data-category') || '';
        const priceMin = parseInt(searchInput.getAttribute('data-price-min') || 0);
        const priceMax = parseInt(searchInput.getAttribute('data-price-max') || 1000000);
        
        // Preserve existing filters
        if (categoryFilter) {
            urlParams.set('category', categoryFilter);
        }
        
        if (priceMin > 0) {
            urlParams.set('price_min', priceMin);
        }
        
        if (priceMax < 1000000) {
            urlParams.set('price_max', priceMax);
        }
        
        window.location.href = 'gallery.php?' + urlParams.toString();
    }
    
    // Search on Enter key - keep this for immediate search if Enter is pressed
    if (searchInput) {
        searchInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                clearTimeout(searchTimeout); // Clear any pending timeout
                performSearch();
            }
        });
    }
    
    // Search button is now optional, but keep it for accessibility
    if (searchButton) {
        searchButton.addEventListener('click', function() {
            clearTimeout(searchTimeout); // Clear any pending timeout
            performSearch();
        });
    }
    
    // Handle quantity buttons
    document.querySelectorAll('.quantity-btn.minus').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.quantity-input');
            const value = parseInt(input.value);
            if (value > 1) {
                input.value = value - 1;
            }
        });
    });
    
    document.querySelectorAll('.quantity-btn.plus').forEach(button => {
        button.addEventListener('click', function() {
            const input = this.parentNode.querySelector('.quantity-input');
            const value = parseInt(input.value);
            const max = parseInt(input.getAttribute('max'));
            if (value < max) {
                input.value = value + 1;
            }
        });
    });
    
    // Handle sorting
    const sortSelect = document.getElementById('sort');
    if (sortSelect) {
        sortSelect.addEventListener('change', function() {
            const sortValue = this.value;
            const items = Array.from(document.querySelectorAll('.gallery-item'));
            const container = document.querySelector('.gallery-container');
            
            items.sort((a, b) => {
                if (sortValue === 'price-low') {
                    const priceA = parseFloat(a.querySelector('.price').textContent.replace('฿', '').replace(',', ''));
                    const priceB = parseFloat(b.querySelector('.price').textContent.replace('฿', '').replace(',', ''));
                    return priceA - priceB;
                } else if (sortValue === 'price-high') {
                    const priceA = parseFloat(a.querySelector('.price').textContent.replace('฿', '').replace(',', ''));
                    const priceB = parseFloat(b.querySelector('.price').textContent.replace('฿', '').replace(',', ''));
                    return priceB - priceA;
                } else if (sortValue === 'name') {
                    const nameA = a.querySelector('h2').textContent;
                    const nameB = b.querySelector('h2').textContent;
                    return nameA.localeCompare(nameB);
                } else {
                    return 0;
                }
            });
            
            // Clear and re-append sorted items
            container.innerHTML = '';
            items.forEach(item => container.appendChild(item));
        });
    }
    
    // Initialize dropdown functionality
    const dropdownBtn = document.querySelector('.dropdown-btn');
    if (dropdownBtn) {
        dropdownBtn.addEventListener('click', function() {
            const dropdownContent = document.querySelector('.dropdown-content');
            dropdownContent.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        window.addEventListener('click', function(event) {
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
});