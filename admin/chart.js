$(document).ready(function() {
    // Chart Colors
    const chartColors = {
        primary: '#FF6F61',
        secondary: '#D4A017',
        accent: '#FF8C94',
        highlight: '#6B7280',
        background: '#FFF5F5'
    };

    // Sales Overview Chart (Line)
    new Chart(document.getElementById('salesOverviewChart'), {
        type: 'line',
        data: {
            labels: chartData.salesDates,
            datasets: [{
                label: 'Sales ($)',
                data: chartData.salesValues,
                borderColor: chartColors.primary,
                backgroundColor: 'rgba(255, 111, 97, 0.2)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: chartColors.primary,
                pointBorderColor: '#FFF5F5',
                pointHoverBackgroundColor: chartColors.secondary,
                pointHoverBorderColor: '#FFF5F5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                datalabels: { display: false }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Sales ($)' } },
                x: { title: { display: true, text: 'Date' } }
            }
        }
    });

    // Top Products Chart (Bar)
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: chartData.productLabels,
            datasets: [{
                label: 'Sales ($)',
                data: chartData.productData,
                backgroundColor: chartColors.accent,
                borderColor: chartColors.primary,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                datalabels: { color: '#2C2C2C', anchor: 'end', align: 'top' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Sales ($)' } },
                x: { title: { display: true, text: 'Products' } }
            }
        }
    });

    // Sales by Category Chart (Pie)
    new Chart(document.getElementById('salesByCategoryChart'), {
        type: 'pie',
        data: {
            labels: chartData.categoryLabels,
            datasets: [{
                label: 'Sales ($)',
                data: chartData.categoryData,
                backgroundColor: [chartColors.primary, chartColors.secondary, chartColors.accent, '#9CA3AF'],
                borderColor: '#FFF5F5',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' },
                datalabels: { color: '#FFF5F5', formatter: (value) => value }
            }
        }
    });

    // User Growth Chart (Line)
    new Chart(document.getElementById('userGrowthChart'), {
        type: 'line',
        data: {
            labels: chartData.userMonths,
            datasets: [{
                label: 'Users',
                data: chartData.userCounts,
                borderColor: chartColors.secondary,
                backgroundColor: 'rgba(212, 160, 23, 0.2)',
                fill: true,
                tension: 0.4,
                pointBackgroundColor: chartColors.secondary,
                pointBorderColor: '#FFF5F5',
                pointHoverBackgroundColor: chartColors.primary,
                pointHoverBorderColor: '#FFF5F5'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                datalabels: { display: false }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Users' } },
                x: { title: { display: true, text: 'Month' } }
            }
        }
    });

    // Top Clients Chart (Bar)
    new Chart(document.getElementById('topClientsChart'), {
        type: 'bar',
        data: {
            labels: chartData.clientNames,
            datasets: [{
                label: 'Spending ($)',
                data: chartData.clientSpending,
                backgroundColor: chartColors.primary,
                borderColor: chartColors.accent,
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: true, position: 'top' },
                datalabels: { color: '#2C2C2C', anchor: 'end', align: 'top' }
            },
            scales: {
                y: { beginAtZero: true, title: { display: true, text: 'Spending ($)' } },
                x: { title: { display: true, text: 'Clients' } }
            }
        }
    });
});