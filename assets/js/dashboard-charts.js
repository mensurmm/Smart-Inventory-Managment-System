const ctx = document.getElementById("salesChart").getContext("2d");

// Check if data exists to prevent errors
if (typeof chartLabels !== 'undefined' && typeof chartData !== 'undefined') {
    new Chart(ctx, {
        type: "line",
        data: {
            labels: chartLabels, 
            datasets: [{
                label: "Sales (ETB)",
                data: chartData, 
                borderColor: "#3498db",
                backgroundColor: "rgba(52, 152, 219, 0.1)",
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointBackgroundColor: "#3498db",
            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return "ETB " + context.parsed.y.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: { 
                    beginAtZero: true, 
                    grid: { color: "#eee" },
                    ticks: {
                        callback: function(value) { return "ETB " + value; }
                    }
                },
                x: { grid: { display: false } },
            },
        },
    });
} else {
    console.error("Chart data or labels are missing!");
}