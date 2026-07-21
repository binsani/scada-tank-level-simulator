import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

Alpine.data('scadaDashboard', () => ({
    live: true,
    fullScreen: false,
    snapshot: window.__SCADA_STATE__ ?? {},
    alarms: window.__SCADA_STATE__?.alarms ?? [],
    charts: {},
    tick() {
        if (!this.live) {
            return;
        }

        fetch('/api/tank')
            .then((response) => response.json())
            .then((data) => {
                this.snapshot = data;
                this.alarms = data.alarms ?? [];
                this.refreshCharts(data);
            });
    },
    refreshCharts(data) {
        const labels = Array.from({ length: 12 }, (_, index) => `${index * 5}m`);
        const series = {
            level: Array.from({ length: 12 }, () => Number(data.tank?.level_percent ?? 0)),
            pressure: Array.from({ length: 12 }, () => Number(data.tank?.pressure_bar ?? 0)),
            temperature: Array.from({ length: 12 }, () => Number(data.tank?.temperature_c ?? 0)),
            flow: Array.from({ length: 12 }, () => Number(data.tank?.flow_rate_l_min ?? 0)),
            current: Array.from({ length: 12 }, () => Number(data.pump?.motor_current_a ?? 0)),
            energy: Array.from({ length: 12 }, () => Number(data.pump?.energy_used_kwh ?? 0)),
        };

        Object.entries(series).forEach(([key, values]) => {
            const chart = this.charts[key];
            if (!chart) {
                return;
            }
            chart.data.labels = labels;
            chart.data.datasets[0].data = values;
            chart.update('none');
        });
    },
    initCharts() {
        const configs = {
            levelChart: { label: 'Tank Level (%)', color: '#38bdf8' },
            pressureChart: { label: 'Pressure (bar)', color: '#22c55e' },
            temperatureChart: { label: 'Temperature (°C)', color: '#facc15' },
            flowChart: { label: 'Flow Rate (L/min)', color: '#60a5fa' },
            currentChart: { label: 'Motor Current (A)', color: '#f97316' },
            energyChart: { label: 'Energy (kWh)', color: '#a78bfa' },
        };

        Object.entries(configs).forEach(([elementId, config]) => {
            const canvas = document.getElementById(elementId);
            if (!canvas) {
                return;
            }

            this.charts[elementId.replace('Chart', '')] = new Chart(canvas, {
                type: 'line',
                data: {
                    labels: Array.from({ length: 12 }, (_, index) => `${index * 5}m`),
                    datasets: [{
                        label: config.label,
                        data: Array.from({ length: 12 }, () => 0),
                        borderColor: config.color,
                        backgroundColor: `${config.color}33`,
                        tension: 0.35,
                        fill: true,
                        pointRadius: 0,
                    }],
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                    },
                    scales: {
                        x: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94a3b8' } },
                        y: { grid: { color: 'rgba(148, 163, 184, 0.1)' }, ticks: { color: '#94a3b8' } },
                    },
                },
            });
        });
    },
}));

Alpine.start();
