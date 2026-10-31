import Chart from 'chart.js/auto';

const chartCanvas = document.querySelector('#finance-chart');

if (chartCanvas && window.financeChartData) {
    new Chart(chartCanvas, {
        type: 'bar',
        data: {
            labels: window.financeChartData.labels,
            datasets: [
                {
                    label: 'Pemasukan',
                    data: window.financeChartData.income,
                    backgroundColor: '#526b3a',
                    borderRadius: 6,
                },
                {
                    label: 'Pengeluaran',
                    data: window.financeChartData.expense,
                    backgroundColor: '#e8b44a',
                    borderRadius: 6,
                },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: (value) => `Rp${Number(value).toLocaleString('id-ID')}`,
                    },
                },
            },
        },
    });
}
