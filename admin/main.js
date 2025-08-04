// C:\xampp\htdocs\bisma dashboard\admin\main.js
console.log("Main.js loaded");

$(document).ready(function() {
    // Sidebar Toggle
    $('#sidebarToggle, #sidebarToggleTop').on('click', function() {
        console.log("Sidebar toggle clicked");
        $('.sidebar').toggleClass('toggled');
        $('body').toggleClass('sidebar-toggled');
        if ($('.sidebar').hasClass('toggled')) {
            $('#sidebarToggle').html('<i class="fas fa-angle-right"></i>');
        } else {
            $('#sidebarToggle').html('<i class="fas fa-angle-left"></i>');
        }
    });

    // Adjust layout on resize
    $(window).on('resize', function() {
        console.log("Window resized, width:", window.innerWidth);
        if (window.innerWidth < 768) {
            $('.sidebar').addClass('toggled');
            $('body').addClass('sidebar-toggled');
            $('#sidebarToggle').html('<i class="fas fa-angle-right"></i>');
        } else {
            $('.sidebar').removeClass('toggled');
            $('body').removeClass('sidebar-toggled');
            $('#sidebarToggle').html('<i class="fas fa-angle-left"></i>');
        }
    }).trigger('resize');
});