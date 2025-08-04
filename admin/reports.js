$(document).ready(function() {
    // Check dependencies
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not loaded');
        showToast('Error: jQuery not loaded! Charts cannot load.', true);
        renderFallbackCharts();
        return;
    }
    if (typeof Chart === 'undefined') {
        console.error('Chart.js not loaded');
        showToast('Error: Chart.js not loaded! Charts cannot load.', true);
        renderFallbackCharts();
        return;
    }
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr not loaded');
        showToast('Warning: Flatpickr not loaded! Date picker may not work.', true);
    }

    // Initialize Flatpickr
    flatpickr("#dateRangePicker", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: ["2025-01-01", "2025-12-31"],
        onChange: function(selectedDates) {
            console.log("Date range selected:", selectedDates);
        }
    });

    // Chart instances
    let monthlySalesChart, ordersByStatusChart, topProductsChart;

    // Toast function
    function showToast(message, isError = false) {
        const toast = $('#toastNotification');
        if (!toast.length) {
            console.error('Toast element not found');
            alert(message);
            return;
        }
        toast.find('.toast-body').text(message);
        toast.addClass('show ' + (isError ? 'toast-error' : 'toast-success'));
        setTimeout(() => {
            toast.removeClass('show');
        }, 3000);
    }

    // Render fallback charts
    function renderFallbackCharts() {
        const fallbackData = {
            sales: [1000, 2000, 3000, 4000, 0, 0, 0, 0, 0, 0, 0, 0],
            status: { pending: 2, processing: 1, shipped: 3, delivered: 4 },
            products: { labels: ['Sample Product'], quantities: [5] }
        };
        updateCharts(fallbackData);
        showToast('Using sample data due to error.', true);
    }

    // Fetch chart data
    function fetchChartData(startDate, endDate, categoryId) {
        console.log("Fetching data:", { startDate, endDate, categoryId });
        $.ajax({
            url: 'reports.php',
            type: 'POST',
            data: {
                fetch_data: true,
                start_date: startDate,
                end_date: endDate,
                category_id: categoryId
            },
            dataType: 'json',
            success: function(data) {
                console.log("Data received:", data);
                if (data.error) {
                    console.error("Server error:", data.error);
                    showToast('Error: ' + data.error, true);
                    renderFallbackCharts();
                    return;
                }
                updateCharts(data);
                showToast("Charts updated successfully!");
                // Update category dropdown
                if (data.categories && data.categories.length) {
                    const $categoryFilter = $('#categoryFilter');
                    $categoryFilter.html('<option value="0">All Categories</option>');
                    data.categories.forEach(cat => {
                        $categoryFilter.append(`<option value="${cat.id}">${cat.name}</option>`);
                    });
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX error:", textStatus, errorThrown, jqXHR.responseText);
                showToast('Error: Failed to fetch data! Using sample data.', true);
                renderFallbackCharts();
            }
        });
    }

    // Update charts
    function updateCharts(data) {
        // Monthly Sales Chart
        if (monthlySalesChart) monthlySalesChart.destroy();
        const monthlySalesCanvas = document.getElementById('monthlySalesChart');
        if (!monthlySalesCanvas) {
            console.error('Canvas #monthlySalesChart not found');
            showToast('Error: Monthly Sales chart canvas not found!', true);
            return;
        }
        monthlySalesChart = new Chart(monthlySalesCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                datasets: [{
                    label: 'Sales (PKR)',
                    data: data.sales,
                    backgroundColor: '#FFCCD5',
                    borderColor: '#D4AF37',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top', labels: { font: { family: 'Inter', size: 14 } } },
                    title: { display: true, text: 'Monthly Sales', font: { family: 'Inter', size: 16 } },
                    tooltip: { backgroundColor: '#FFE4E1', titleFont: { family: 'Inter' }, bodyFont: { family: 'Inter' } }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Sales Amount (PKR)', font: { family: 'Inter' } } },
                    x: { title: { display: true, text: 'Month', font: { family: 'Inter' } } }
                }
            }
        });

        // Orders by Status Chart
        if (ordersByStatusChart) ordersByStatusChart.destroy();
        const ordersByStatusCanvas = document.getElementById('ordersByStatusChart');
        if (!ordersByStatusCanvas) {
            console.error('Canvas #ordersByStatusChart not found');
            showToast('Error: Orders by Status chart canvas not found!', true);
            return;
        }
        ordersByStatusChart = new Chart(ordersByStatusCanvas.getContext('2d'), {
            type: 'pie',
            data: {
                labels: Object.keys(data.status),
                datasets: [{
                    data: Object.values(data.status),
                    backgroundColor: ['#FFCCD5', '#FFE4E1', '#D4AF37', '#FF6F61'],
                    borderColor: '#2C2C2C',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'top', labels: { font: { family: 'Inter', size: 14 } } },
                    title: { display: true, text: 'Orders by Status', font: { family: 'Inter', size: 16 } },
                    tooltip: { backgroundColor: '#FFE4E1', titleFont: { family: 'Inter' }, bodyFont: { family: 'Inter' } }
                }
            }
        });

        // Top Selling Products Chart
        if (topProductsChart) topProductsChart.destroy();
        const topProductsCanvas = document.getElementById('topProductsChart');
        if (!topProductsCanvas) {
            console.error('Canvas #topProductsChart not found');
            showToast('Error: Top Products chart canvas not found!', true);
            return;
        }
        topProductsChart = new Chart(topProductsCanvas.getContext('2d'), {
            type: 'bar',
            data: {
                labels: data.products.labels,
                datasets: [{
                    label: 'Quantity Sold',
                    data: data.products.quantities,
                    backgroundColor: '#FFE4E1',
                    borderColor: '#D4AF37',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: true, position: 'top', labels: { font: { family: 'Inter', size: 14 } } },
                    title: { display: true, text: 'Top Selling Products', font: { family: 'Inter', size: 16 } },
                    tooltip: { backgroundColor: '#FFCCD5', titleFont: { family: 'Inter' }, bodyFont: { family: 'Inter' } }
                },
                scales: {
                    y: { beginAtZero: true, title: { display: true, text: 'Quantity Sold', font: { family: 'Inter' } } },
                    x: { title: { display: true, text: 'Product', font: { family: 'Inter' } } }
                }
            }
        });
    }

    // Apply filter
    $('#applyFilter').on('click', function() {
        const dates = $('#dateRangePicker').val().split(' to ');
        const startDate = dates[0] || '2025-01-01';
        const endDate = dates[1] || '2025-12-31';
        const categoryId = $('#categoryFilter').val();
        console.log("Applying filter:", { startDate, endDate, categoryId });
        fetchChartData(startDate, endDate, categoryId);
    });

    // Reset filter
    $('#resetFilter').on('click', function() {
        $('#dateRangePicker').val('2025-01-01 to 2025-12-31');
        $('#categoryFilter').val('0');
        console.log("Resetting filters");
        fetchChartData('2025-01-01', '2025-12-31', 0);
    });

    // Initial data load
    console.log("Initial data load");
    fetchChartData('2025-01-01', '2025-12-31', 0);

    // Log dependencies
    console.log('jQuery loaded:', typeof jQuery);
    console.log('Chart.js loaded:', typeof Chart);
    console.log('Flatpickr loaded:', typeof flatpickr);
});