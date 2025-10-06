// gallery.js - UPDATED for multi-image categories and recommended classes

document.addEventListener('DOMContentLoaded', () => {
    // Get the modal and its elements
    const modal = document.getElementById("galleryModal");
    const modalImage = document.getElementById("modalImage");
    const captionText = document.getElementById("caption");
    const closeBtn = document.querySelector(".close-btn");
    const prevBtn = document.querySelector(".prev");
    const nextBtn = document.querySelector(".next");

    // Target the main clickable images in the collage. If you use the recommended
    // classes, this selects only the one visible image per category.
    const clickableImages = document.querySelectorAll(".Gallery-Collage img"); 
    
    // Variables to hold the current set of images and the current index
    let currentImageSet = [];
    let currentIndex = 0;

    // Function to change the image with a smooth fade effect
    const changeImage = (index) => {
        // Handle looping navigation
        if (index >= currentImageSet.length) {
            currentIndex = 0; // Loop to the first image
        } else if (index < 0) {
            currentIndex = currentImageSet.length - 1; // Loop to the last image
        } else {
            currentIndex = index;
        }
        
        // 1. Start the smooth fade-out animation
        modalImage.classList.remove('fade-in');
        modalImage.classList.add('fade-out');

        // 2. Wait for the animation to complete (300ms) before changing the source
        setTimeout(() => {
            const newImage = currentImageSet[currentIndex];
            modalImage.src = newImage.src;
            captionText.innerHTML = newImage.alt;

            // 3. Remove fade-out and apply the fade-in animation to the new image
            modalImage.classList.remove('fade-out');
            modalImage.classList.add('fade-in');

        }, 300); // 300ms matches the CSS animation duration
    };

    // Function to open the modal
    const openModal = (clickedImage) => {
        // 1. Find the parent Image-Set (e.g., Image-Set2)
        const parentDiv = clickedImage.closest('.Gallery-Collage > div');
        
        // 2. Collect ALL images within that parent div for the current set
        currentImageSet = Array.from(parentDiv.querySelectorAll('img'));
        
        // 3. Find the index of the clicked image within its own set
        currentIndex = currentImageSet.indexOf(clickedImage);

        // 4. Display the modal
        modal.style.display = "block";
        modalImage.src = clickedImage.src;
        captionText.innerHTML = clickedImage.alt;
        
        // Ensure the initial image has the entry animation
        modalImage.classList.add('zoomIn');
    };

    // Add click listeners to all images in the collage
    clickableImages.forEach((image) => {
        image.addEventListener("click", () => {
            openModal(image);
        });
    });

    // Close Modal Logic
    const closeModal = () => {
        modal.style.display = "none";
        // Reset and clean up temporary animation classes when closing
        modalImage.classList.remove('zoomIn', 'fade-in', 'fade-out');
        currentImageSet = []; // Clear the image set
        currentIndex = 0;
    };

    closeBtn.addEventListener("click", closeModal);
    window.addEventListener("click", (event) => {
        if (event.target == modal) {
            closeModal();
        }
    });

    // Navigation functionality for previous and next buttons
    prevBtn.addEventListener("click", () => {
        changeImage(currentIndex - 1);
    });

    nextBtn.addEventListener("click", () => {
        changeImage(currentIndex + 1);
    });

    // Keyboard navigation (Escape, Left, Right arrows)
    document.addEventListener("keydown", (event) => {
        if (modal.style.display === "block") {
            if (event.key === "ArrowLeft") {
                changeImage(currentIndex - 1);
            } else if (event.key === "ArrowRight") {
                changeImage(currentIndex + 1);
            } else if (event.key === "Escape") {
                closeModal();
            }
        }
    });
});