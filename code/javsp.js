
// Toggles dark mode on the webpage
function myFunction() {
    var element = document.body;
    element.classList.toggle("dark-mode");
}


function showContent(tabId) {
    // Remove active class from all tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });

    // Hide all content
    document.querySelectorAll('.content').forEach(content => {
        content.style.display = 'none';
    });

    // Add active class to the clicked tab
    const activeTab = document.querySelector(`.tab[data-tab="${tabId}"]`);
    if (activeTab) activeTab.classList.add('active');

    // Show the selected content
    const activeContent = document.getElementById(tabId);
    if (activeContent) activeContent.style.display = 'block';
}

// Show the content of the "music" tab by default when the page loads
document.addEventListener('DOMContentLoaded', () => {
    showContent('music');
});

console.log(`Activated content: ${tabId}`);
