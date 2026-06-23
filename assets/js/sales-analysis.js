// Weekly Bar Chart
const weeklyCtx = document.getElementById('weeklyBarChart').getContext('2d');
new Chart(weeklyCtx, {
    type: 'bar',
    data: {
        labels: weeklyLabels,
        datasets: [{
            label: 'Revenue (ETB)',
            data: weeklyData,
            backgroundColor: '#3498db',
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});

// Yearly Line Chart
const yearlyCtx = document.getElementById('yearlyLineChart').getContext('2d');
new Chart(yearlyCtx, {
    type: 'line',
    data: {
        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
        datasets: [{
            label: 'Monthly Growth',
            data: yearlyData,
            borderColor: '#2ecc71',
            fill: true,
            backgroundColor: 'rgba(46, 204, 113, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } }
    }
});