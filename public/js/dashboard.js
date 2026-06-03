/**
 * Sistem Informasi Monitoring Guru (SIMGURU)
 * dashboard.js - Chart.js Initializations & Custom Plugins
 */

// Global chart instances
let attendanceBarChart = null;
let distributionDonutChart = null;

// Custom plugin to draw text inside the center of the Donut chart
const doughnutCenterTextPlugin = {
    id: 'doughnutCenterText',
    afterDraw(chart) {
        const { ctx, chartArea: { top, left, width, height } } = chart;
        
        // Retrieve plugin configuration
        const pluginConfig = chart.config.options.plugins.doughnutCenterText;
        if (!pluginConfig?.display) return;
        
        ctx.save();
        
        const mainText = pluginConfig.text || '0%';
        const subText = pluginConfig.subtext || 'Hadir';
        
        const centerX = left + width / 2;
        const centerY = top + height / 2;
        
        // Draw main percentage text
        ctx.font = '700 24px "Plus Jakarta Sans", sans-serif';
        ctx.textAlign = 'center';
        ctx.textBaseline = 'middle';
        ctx.fillStyle = '#0F1E32'; // var(--color-primary-900)
        
        if (subText) {
            ctx.fillText(mainText, centerX, centerY - 6);
            
            // Draw subtext below
            ctx.font = '600 11px "Plus Jakarta Sans", sans-serif';
            ctx.fillStyle = '#6B7280'; // var(--color-neutral-500)
            ctx.fillText(subText.toUpperCase(), centerX, centerY + 14);
        } else {
            ctx.fillText(mainText, centerX, centerY);
        }
        
        ctx.restore();
    }
};

/**
 * Initialize all dashboard charts with data.
 * 
 * @param {Object} barData Data for the 7-day attendance bar chart
 * @param {Object} donutData Data for the status distribution doughnut chart
 * @param {String} attendanceRateText Indonesian formatted attendance percentage (e.g. "87,5%")
 */
function initializeDashboardCharts(barData, donutData, attendanceRateText) {
    // 1. Destroy existing instances to prevent memory leaks or overlay issues on re-render
    if (attendanceBarChart) {
        attendanceBarChart.destroy();
    }
    if (distributionDonutChart) {
        distributionDonutChart.destroy();
    }

    // 2. Initialize Bar Chart
    const barCtx = document.getElementById('attendanceBarChart').getContext('2d');
    attendanceBarChart = new Chart(barCtx, {
        type: 'bar',
        data: {
            labels: barData.labels,
            datasets: [
                {
                    label: 'Hadir',
                    data: barData.hadir,
                    backgroundColor: '#16A34A', // var(--color-success-600)
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                },
                {
                    label: 'Tidak Hadir',
                    data: barData.tidak_hadir,
                    backgroundColor: '#F87171', // var(--color-danger-400)
                    borderRadius: 4,
                    barPercentage: 0.6,
                    categoryPercentage: 0.8
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        boxWidth: 12,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 12,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#0F1E32',
                    titleFont: {
                        family: 'Plus Jakarta Sans',
                        weight: '700'
                    },
                    bodyFont: {
                        family: 'Plus Jakarta Sans'
                    },
                    callbacks: {
                        label: function (context) {
                            return ` ${context.dataset.label}: ${context.raw} guru`;
                        }
                    }
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11
                        }
                    }
                },
                y: {
                    border: {
                        dash: [4, 4]
                    },
                    grid: {
                        color: '#E5E7EB' // var(--color-neutral-200)
                    },
                    ticks: {
                        stepSize: 10,
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11
                        }
                    },
                    min: 0
                }
            }
        }
    });

    // 3. Initialize Donut Chart
    const donutCtx = document.getElementById('distributionDonutChart').getContext('2d');
    distributionDonutChart = new Chart(donutCtx, {
        type: 'doughnut',
        data: {
            labels: ['Hadir', 'Sakit', 'Izin', 'Alpha'],
            datasets: [{
                data: [
                    donutData.hadir,
                    donutData.sakit,
                    donutData.izin,
                    donutData.alpha
                ],
                backgroundColor: [
                    '#16A34A', // Hadir (Hijau)
                    '#F59E0B', // Sakit (Kuning)
                    '#0EA5E9', // Izin (Biru)
                    '#DC2626'  // Alpha (Merah)
                ],
                borderWidth: 2,
                borderColor: '#ffffff',
                hoverOffset: 4
            }]
        },
        plugins: [doughnutCenterTextPlugin],
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '72%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        boxWidth: 10,
                        padding: 15,
                        usePointStyle: true,
                        pointStyle: 'circle',
                        font: {
                            family: 'Plus Jakarta Sans',
                            size: 11,
                            weight: '500'
                        }
                    }
                },
                tooltip: {
                    backgroundColor: '#0F1E32',
                    titleFont: {
                        family: 'Plus Jakarta Sans',
                        weight: '700'
                    },
                    bodyFont: {
                        family: 'Plus Jakarta Sans'
                    },
                    callbacks: {
                        label: function (context) {
                            return ` ${context.label}: ${context.raw} orang`;
                        }
                    }
                },
                doughnutCenterText: {
                    display: true,
                    text: attendanceRateText,
                    subtext: 'Hadir'
                }
            }
        }
    });
}
