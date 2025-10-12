// Image Slider Functionality
document.addEventListener('DOMContentLoaded', function() {
    // Image Slider
    const slides = document.querySelectorAll('.slides img');
    let currentSlide = 0;
    const slideInterval = 3000; // 3 seconds

    function showSlide(n) {
        // Remove active class from all slides
        slides.forEach(slide => slide.classList.remove('active'));
        
        // Calculate the actual slide index
        currentSlide = (n + slides.length) % slides.length;
        
        // Add active class to current slide
        slides[currentSlide].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    // Auto slide functionality
    let slideTimer = setInterval(nextSlide, slideInterval);

    // Pause slider on hover
    const slider = document.querySelector('.slider');
    slider.addEventListener('mouseenter', () => {
        clearInterval(slideTimer);
    });

    slider.addEventListener('mouseleave', () => {
        slideTimer = setInterval(nextSlide, slideInterval);
    });

    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // Header scroll effect
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        if (window.scrollY > 100) {
            header.style.background = 'rgba(0, 64, 128, 0.95)';
            header.style.boxShadow = '0 2px 10px rgba(0,0,0,0.1)';
        } else {
            header.style.background = '#004080';
            header.style.boxShadow = 'none';
        }
    });

    // Feature box hover effects
    const featureBoxes = document.querySelectorAll('.feature-box');
    featureBoxes.forEach(box => {
        box.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.2)';
        });
        
        box.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
    });

    // Blog card hover effects
    const blogCards = document.querySelectorAll('.blog-card');
    blogCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-8px)';
            this.style.boxShadow = '0 15px 30px rgba(0,0,0,0.15)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '0 5px 15px rgba(0,0,0,0.1)';
        });
    });

    // Social media icon hover effects
    const socialIcons = document.querySelectorAll('.socialLink a');
    socialIcons.forEach(icon => {
        icon.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.2)';
        });
        
        icon.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
        });
    });

    // Navigation menu active state
    const navLinks = document.querySelectorAll('nav ul li a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            navLinks.forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Current year in footer
    const currentYear = new Date().getFullYear();
    const copyrightElement = document.querySelector('.copyRight p');
    if (copyrightElement) {
        copyrightElement.innerHTML = `&copy; ${currentYear} Dhaka Centralized Hospital. All rights reserved.`;
    }

    // Loading animation
    window.addEventListener('load', function() {
        document.body.style.opacity = '0';
        document.body.style.transition = 'opacity 0.5s ease-in';
        
        setTimeout(() => {
            document.body.style.opacity = '1';
        }, 100);
    });

    // Emergency appointment quick access
    const emergencyBtn = document.querySelector('a[href="emergency.html"]');
    if (emergencyBtn) {
        emergencyBtn.addEventListener('click', function(e) {
            // Add emergency animation
            this.style.animation = 'pulse 0.5s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    }

    // Ambulance hire quick access
    const ambulanceBtn = document.querySelector('a[href="ambulance.html"]');
    if (ambulanceBtn) {
        ambulanceBtn.addEventListener('click', function(e) {
            // Add ambulance animation
            this.style.animation = 'shake 0.5s ease-in-out';
            setTimeout(() => {
                this.style.animation = '';
            }, 500);
        });
    }
});

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .feature-box, .blog-card {
        transition: all 0.3s ease;
    }
    
    nav ul li a {
        transition: color 0.3s ease;
    }
    
    .socialLink a {
        transition: transform 0.3s ease;
    }
    
    header {
        transition: all 0.3s ease;
    }
    
    body {
        transition: opacity 0.5s ease-in;
    }
`;
document.head.appendChild(style);

// Console welcome message
console.log('Welcome to Dhaka Centralized Hospital Management System');
console.log('System initialized successfully');

// Error handling for images
document.addEventListener('error', function(e) {
    if (e.target.tagName === 'IMG') {
        console.warn('Image failed to load:', e.target.src);
        e.target.style.display = 'none';
    }
}, true);