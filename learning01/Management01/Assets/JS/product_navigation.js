// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    initImageNavigation();
});

function initImageNavigation() {
    // Get all image sources from main image and thumbnails
    function getAllImageSources() {
        const sources = [];
        const mainImage = document.getElementById('main-product-image');
        if (mainImage) {
            sources.push(mainImage.src);
            
            document.querySelectorAll('.thumbnail').forEach(thumbnail => {
                if (thumbnail.getAttribute('data-image') !== sources[0]) {
                    sources.push(thumbnail.getAttribute('data-image'));
                }
            });
        }
        return sources;
    }
    
    // Set up navigation
    const prevButton = document.getElementById('prev-image');
    const nextButton = document.getElementById('next-image');
    const mainImage = document.getElementById('main-product-image');
    
    if (!prevButton || !nextButton || !mainImage) {
        console.error('Navigation elements not found');
        return;
    }
    
    let allImages = getAllImageSources();
    let currentIndex = 0;
    
    prevButton.addEventListener('click', function() {
        currentIndex = (currentIndex - 1 + allImages.length) % allImages.length;
        mainImage.src = allImages[currentIndex];
        updateActiveThumbnail(allImages[currentIndex]);
    });
    
    nextButton.addEventListener('click', function() {
        currentIndex = (currentIndex + 1) % allImages.length;
        mainImage.src = allImages[currentIndex];
        updateActiveThumbnail(allImages[currentIndex]);
    });
    
    function updateActiveThumbnail(src) {
        document.querySelectorAll('.thumbnail').forEach(thumbnail => {
            if (thumbnail.getAttribute('data-image') === src) {
                thumbnail.classList.add('active');
            } else {
                thumbnail.classList.remove('active');
            }
        });
    }
    
    // Initialize with current image
    document.querySelectorAll('.thumbnail').forEach((thumbnail, index) => {
        if (thumbnail.getAttribute('data-image') === mainImage.src) {
            currentIndex = index;
        }
        
        thumbnail.addEventListener('click', function() {
            const newSrc = this.getAttribute('data-image');
            mainImage.src = newSrc;
            currentIndex = allImages.indexOf(newSrc);
            updateActiveThumbnail(newSrc);
        });
    });
}