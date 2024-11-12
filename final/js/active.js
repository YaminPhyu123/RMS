// Get all navigation links
const navLinks = document.querySelectorAll('.nav-link');

// Function to handle click on nav links
function handleNavClick(event) {
    event.preventDefault(); // Prevent default link behavior (e.g., navigating to another page)
    
    // Remove 'active' class from all links
    navLinks.forEach(link => {
        link.classList.remove('active');
    });
    
    // Add 'active' class to the clicked link
    this.classList.add('active');
    
    // Optionally, remove 'active' class after a delay (e.g., 3 seconds)
    setTimeout(() => {
        this.classList.remove('active');
    }, 3000); // Adjust delay time (in milliseconds) as needed
}

// Add click event listener to each link
navLinks.forEach(link => {
    link.addEventListener('click', handleNavClick);
});

// Check if current page is 'Home' page when the page loads
document.addEventListener('DOMContentLoaded', function() {
    // Get the current URL pathname
    const currentPath = window.location.pathname;
    
    // Check if it's the 'Home' page (adjust '/home' as per your actual home page URL)
    if (currentPath === '/home' || currentPath === '/') {
        // Find the 'Home' link and add 'active' class
        navLinks.forEach(link => {
            if (link.textContent === 'Home') {
                link.classList.add('active');
            }
        });
    }
});
