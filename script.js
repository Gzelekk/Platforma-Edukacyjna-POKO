    var adminPanel = document.querySelector('.admin-panel');
    var adminOptions = document.querySelector('.admin-options');
    adminPanel.addEventListener('mouseenter', function() {
        adminOptions.style.display = 'block';
    });
    adminPanel.addEventListener('mouseleave', function() {
        adminOptions.style.display = 'none';
    });