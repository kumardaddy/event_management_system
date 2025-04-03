document.addEventListener('DOMContentLoaded', function() {
    // Hero Carousel
    initHeroCarousel();
    
    // Events Carousel
    initEventsCarousel();
    
    // Testimonials Carousel
    initTestimonialsCarousel();
    
    // Form Tabs
    initFormTabs();
    
    // Dashboard Tabs
    initDashboardTabs();
    
    // File Upload Preview
    initFileUploadPreview();
    
    // Mobile Menu
    initMobileMenu();
    
    // Event Bookmarks
    initEventBookmarks();
    
    // Fix form submission issues
    fixFormSubmissionBugs();
});

// Hero Carousel
function initHeroCarousel() {
    const carouselTrack = document.querySelector('.carousel-track');
    if (!carouselTrack) return;
    
    const slides = document.querySelectorAll('.carousel-slide');
    const prevBtn = document.querySelector('.carousel-prev');
    const nextBtn = document.querySelector('.carousel-next');
    const dotsContainer = document.querySelector('.carousel-dots');
    
    let currentIndex = 0;
    const slideWidth = 100; // 100%
    
    // Create dots
    slides.forEach((_, index) => {
        const dot = document.createElement('div');
        dot.classList.add('carousel-dot');
        if (index === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(index));
        dotsContainer.appendChild(dot);
    });
    
    const dots = document.querySelectorAll('.carousel-dot');
    
    // Set initial position
    carouselTrack.style.transform = `translateX(0%)`;
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    // Auto slide
    let interval = setInterval(nextSlide, 5000);
    
    // Reset interval on manual navigation
    function resetInterval() {
        clearInterval(interval);
        interval = setInterval(nextSlide, 5000);
    }
    
    function prevSlide() {
        currentIndex = (currentIndex === 0) ? slides.length - 1 : currentIndex - 1;
        updateCarousel();
        resetInterval();
    }
    
    function nextSlide() {
        currentIndex = (currentIndex === slides.length - 1) ? 0 : currentIndex + 1;
        updateCarousel();
        resetInterval();
    }
    
    function goToSlide(index) {
        currentIndex = index;
        updateCarousel();
        resetInterval();
    }
    
    function updateCarousel() {
        carouselTrack.style.transform = `translateX(-${currentIndex * slideWidth}%)`;
        
        // Update dots
        dots.forEach((dot, index) => {
            dot.classList.toggle('active', index === currentIndex);
        });
        
        // Add animation to current slide content
        slides.forEach((slide, index) => {
            const content = slide.querySelector('.slide-content');
            if (content) {
                if (index === currentIndex) {
                    content.style.opacity = '0';
                    setTimeout(() => {
                        content.style.opacity = '1';
                    }, 50);
                }
            }
        });
    }
    
    // Pause carousel on hover
    if (carouselTrack) {
        carouselTrack.addEventListener('mouseenter', () => {
            clearInterval(interval);
        });
        
        carouselTrack.addEventListener('mouseleave', () => {
            interval = setInterval(nextSlide, 5000);
        });
    }
    
    // Touch events for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    carouselTrack.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    carouselTrack.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            nextSlide();
        } else if (touchEndX > touchStartX + swipeThreshold) {
            prevSlide();
        }
    }
}

// Events Carousel
function initEventsCarousel() {
    const eventsTrack = document.querySelector('.events-track');
    if (!eventsTrack) return;
    
    const prevBtn = document.querySelector('.events-prev');
    const nextBtn = document.querySelector('.events-next');
    const eventCards = document.querySelectorAll('.event-card');
    
    if (eventCards.length === 0) return;
    
    let currentPosition = 0;
    let cardsPerView = getCardsPerView();
    let cardWidth = 100 / cardsPerView; // Percentage width
    
    // Set initial widths
    eventCards.forEach(card => {
        card.style.flex = `0 0 ${cardWidth}%`;
    });
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    // Update cards per view on window resize
    window.addEventListener('resize', () => {
        cardsPerView = getCardsPerView();
        cardWidth = 100 / cardsPerView;
        
        eventCards.forEach(card => {
            card.style.flex = `0 0 ${cardWidth}%`;
        });
        
        // Reset position if needed
        if (currentPosition > eventCards.length - cardsPerView) {
            currentPosition = Math.max(0, eventCards.length - cardsPerView);
            updateEventsCarousel();
        }
    });
    
    function prevSlide() {
        if (currentPosition > 0) {
            currentPosition--;
            updateEventsCarousel();
        }
    }
    
    function nextSlide() {
        if (currentPosition < eventCards.length - cardsPerView) {
            currentPosition++;
            updateEventsCarousel();
        }
    }
    
    function updateEventsCarousel() {
        eventsTrack.style.transform = `translateX(-${currentPosition * cardWidth}%)`;
        
        // Update button states
        if (prevBtn) {
            prevBtn.disabled = currentPosition === 0;
            prevBtn.classList.toggle('disabled', currentPosition === 0);
        }
        
        if (nextBtn) {
            nextBtn.disabled = currentPosition >= eventCards.length - cardsPerView;
            nextBtn.classList.toggle('disabled', currentPosition >= eventCards.length - cardsPerView);
        }
    }
    
    function getCardsPerView() {
        if (window.innerWidth < 576) return 1;
        if (window.innerWidth < 992) return 2;
        return 3;
    }
    
    // Initialize button states
    updateEventsCarousel();
    
    // Touch events for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    eventsTrack.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    eventsTrack.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            nextSlide();
        } else if (touchEndX > touchStartX + swipeThreshold) {
            prevSlide();
        }
    }
}

// Testimonials Carousel
function initTestimonialsCarousel() {
    const testimonialsTrack = document.querySelector('.testimonials-track');
    if (!testimonialsTrack) return;
    
    const prevBtn = document.querySelector('.testimonials-prev');
    const nextBtn = document.querySelector('.testimonials-next');
    const testimonialCards = document.querySelectorAll('.testimonial-card');
    
    if (testimonialCards.length === 0) return;
    
    let currentPosition = 0;
    let cardsPerView = getCardsPerView();
    let cardWidth = 100 / cardsPerView; // Percentage width
    
    // Set initial widths
    testimonialCards.forEach(card => {
        card.style.flex = `0 0 ${cardWidth}%`;
    });
    
    // Event listeners
    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
    
    // Update cards per view on window resize
    window.addEventListener('resize', () => {
        cardsPerView = getCardsPerView();
        cardWidth = 100 / cardsPerView;
        
        testimonialCards.forEach(card => {
            card.style.flex = `0 0 ${cardWidth}%`;
        });
        
        // Reset position if needed
        if (currentPosition > testimonialCards.length - cardsPerView) {
            currentPosition = Math.max(0, testimonialCards.length - cardsPerView);
            updateTestimonialsCarousel();
        }
    });
    
    function prevSlide() {
        if (currentPosition > 0) {
            currentPosition--;
            updateTestimonialsCarousel();
        }
    }
    
    function nextSlide() {
        if (currentPosition < testimonialCards.length - cardsPerView) {
            currentPosition++;
            updateTestimonialsCarousel();
        }
    }
    
    function updateTestimonialsCarousel() {
        testimonialsTrack.style.transform = `translateX(-${currentPosition * cardWidth}%)`;
        
        // Update button states
        if (prevBtn) {
            prevBtn.disabled = currentPosition === 0;
            prevBtn.classList.toggle('disabled', currentPosition === 0);
        }
        
        if (nextBtn) {
            nextBtn.disabled = currentPosition >= testimonialCards.length - cardsPerView;
            nextBtn.classList.toggle('disabled', currentPosition >= testimonialCards.length - cardsPerView);
        }
    }
    
    function getCardsPerView() {
        if (window.innerWidth < 576) return 1;
        if (window.innerWidth < 992) return 2;
        return 3;
    }
    
    // Initialize button states
    updateTestimonialsCarousel();
    
    // Touch events for mobile
    let touchStartX = 0;
    let touchEndX = 0;
    
    testimonialsTrack.addEventListener('touchstart', (e) => {
        touchStartX = e.changedTouches[0].screenX;
    }, { passive: true });
    
    testimonialsTrack.addEventListener('touchend', (e) => {
        touchEndX = e.changedTouches[0].screenX;
        handleSwipe();
    }, { passive: true });
    
    function handleSwipe() {
        const swipeThreshold = 50;
        if (touchEndX < touchStartX - swipeThreshold) {
            nextSlide();
        } else if (touchEndX > touchStartX + swipeThreshold) {
            prevSlide();
        }
    }
}

// Form Tabs
function initFormTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    if (!tabBtns.length) return;
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            
            // Update active tab button
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show selected tab content
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                if (content.id === tabId) {
                    content.style.display = 'block';
                    
                    // Add fade-in animation
                    content.style.opacity = '0';
                    setTimeout(() => {
                        content.style.opacity = '1';
                    }, 50);
                } else {
                    content.style.display = 'none';
                }
            });
        });
    });
}

// Dashboard Tabs
function initDashboardTabs() {
    const tabBtns = document.querySelectorAll('.tab-header .tab-btn');
    if (!tabBtns.length) return;
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            const tabId = btn.getAttribute('data-tab');
            
            // Update active tab button
            tabBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            // Show selected tab content
            const tabContents = document.querySelectorAll('.tab-content');
            tabContents.forEach(content => {
                if (content.id === tabId) {
                    content.classList.add('active');
                } else {
                    content.classList.remove('active');
                }
            });
        });
    });
}

// File Upload Preview
function initFileUploadPreview() {
    const fileInput = document.getElementById('profile_picture');
    if (!fileInput) return;
    
    fileInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                const profilePicture = document.querySelector('.profile-picture');
                if (!profilePicture) return;
                
                // Remove existing content
                profilePicture.innerHTML = '';
                
                // Create image element
                const img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Profile Picture';
                
                // Create overlay
                const overlay = document.createElement('div');
                overlay.classList.add('profile-picture-overlay');
                overlay.innerHTML = '<i class="fas fa-upload"></i>';
                
                // Append elements
                profilePicture.appendChild(img);
                profilePicture.appendChild(overlay);
            }
            
            reader.readAsDataURL(this.files[0]);
        }
    });
}

// Mobile Menu
function initMobileMenu() {
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu');
    const mobileMenuClose = document.querySelector('.mobile-menu-close');
    const body = document.body;
    
    if (!mobileMenuToggle || !mobileMenu) return;
    
    mobileMenuToggle.addEventListener('click', () => {
        mobileMenuToggle.classList.toggle('active');
        mobileMenu.classList.toggle('active');
        body.classList.toggle('menu-open');
    });
    
    if (mobileMenuClose) {
        mobileMenuClose.addEventListener('click', () => {
            mobileMenuToggle.classList.remove('active');
            mobileMenu.classList.remove('active');
            body.classList.remove('menu-open');
        });
    }
    
    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (mobileMenu.classList.contains('active') && 
            !mobileMenu.contains(e.target) && 
            !mobileMenuToggle.contains(e.target)) {
            mobileMenuToggle.classList.remove('active');
            mobileMenu.classList.remove('active');
            body.classList.remove('menu-open');
        }
    });
    
    // Handle sidebar toggle on dashboard
    const sidebarToggle = document.querySelector('.sidebar-toggle');
    const sidebar = document.querySelector('.sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });
    }
}

// Event Bookmarks
function initEventBookmarks() {
    const bookmarkBtns = document.querySelectorAll('.event-bookmark');
    
    bookmarkBtns.forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            e.stopPropagation();
            
            const icon = btn.querySelector('i');
            if (icon.classList.contains('far')) {
                icon.classList.remove('far');
                icon.classList.add('fas');
                btn.setAttribute('title', 'Remove from bookmarks');
                
                // Add animation
                btn.classList.add('pulse');
                setTimeout(() => {
                    btn.classList.remove('pulse');
                }, 500);
            } else {
                icon.classList.remove('fas');
                icon.classList.add('far');
                btn.setAttribute('title', 'Add to bookmarks');
            }
        });
    });
}

// Fix form submission bugs
function fixFormSubmissionBugs() {
    // Fix for register form
    const registerForm = document.querySelector('.register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', function(e) {
            // Validate form before submission
            const requiredFields = registerForm.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    
                    // Add error class if not already present
                    const formGroup = field.closest('.form-group');
                    if (formGroup && !formGroup.querySelector('.error')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error';
                        errorMsg.textContent = 'This field is required';
                        formGroup.appendChild(errorMsg);
                    }
                }
            });
            
            // Password match validation
            const password = registerForm.querySelector('[name="password"]');
            const confirmPassword = registerForm.querySelector('[name="confirm_password"]');
            
            if (password && confirmPassword && password.value !== confirmPassword.value) {
                isValid = false;
                
                const formGroup = confirmPassword.closest('.form-group');
                if (formGroup) {
                    let errorMsg = formGroup.querySelector('.error');
                    
                    if (!errorMsg) {
                        errorMsg = document.createElement('span');
                        errorMsg.className = 'error';
                        formGroup.appendChild(errorMsg);
                        errorMsg.textContent = 'Passwords do not match';
                    } else {
                        errorMsg.textContent = 'Passwords do not match';
                    }
                }
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Fix for login form
    const loginForm = document.querySelector('.login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            // Validate form before submission
            const requiredFields = loginForm.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    
                    // Add error class if not already present
                    const formGroup = field.closest('.form-group');
                    if (formGroup && !formGroup.querySelector('.error')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error';
                        errorMsg.textContent = 'This field is required';
                        formGroup.appendChild(errorMsg);
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    }
    
    // Fix for file upload
    const fileUploadForm = document.querySelector('form[enctype="multipart/form-data"]');
    if (fileUploadForm) {
        fileUploadForm.addEventListener('submit', function(e) {
            const fileInput = fileUploadForm.querySelector('input[type="file"]');
            if (fileInput && fileInput.files.length === 0) {
                // If submitting without selecting a file, prevent default only if required
                if (fileInput.hasAttribute('required')) {
                    e.preventDefault();
                    
                    // Show error message
                    const formGroup = fileInput.closest('.form-group');
                    if (formGroup && !formGroup.querySelector('.error')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error';
                        errorMsg.textContent = 'Please select a file';
                        formGroup.appendChild(errorMsg);
                    }
                }
            }
        });
    }
}