// Lightweight header animation without Three.js
document.addEventListener('DOMContentLoaded', function() {
    initHeaderAnimation();
});

function initHeaderAnimation() {
    const headerElement = document.getElementById('header-animation');
    if (!headerElement) return;
    
    // Create gradient animation with CSS only
    headerElement.classList.add('gradient-animation');
    
    // Add subtle parallax effect on mouse move (very lightweight)
    document.addEventListener('mousemove', function(e) {
        if (window.innerWidth < 992) return; // Skip on mobile devices
        
        // Calculate mouse position percentage
        const mouseX = e.clientX / window.innerWidth;
        const mouseY = e.clientY / window.innerHeight;
        
        // Apply subtle transform to header background (minimal performance impact)
        headerElement.style.transform = `translate(${mouseX * -10}px, ${mouseY * -10}px)`;
    }, { passive: true });
}