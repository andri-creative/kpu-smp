<canvas id="pieChart"></canvas>
<script>
    $(document).ready(function() {
        // Data dari server (diambil dari PHP dan di-encode ke JSON)
        var kandidatData = @json($kandidatData);

        // Ekstrak label dan data dari kandidatData
        var labels = kandidatData.map(function(kandidat) {
            return kandidat.name;
        });

        var dataVotes = kandidatData.map(function(kandidat) {
        return kandidat.votes;
    });

    // Generate warna otomatis berdasarkan jumlah kandidat
    const colors = generateDistinctColors(kandidatData.length);

    // Konfigurasi data untuk Chart.js
    const data = {
        labels: labels,
        datasets: [{
            label: 'Distribusi Suara per Kandidat',
            data: dataVotes,
            backgroundColor: colors.backgroundColor,
            borderColor: colors.borderColor,
            borderWidth: 0
        }]
    };

    // Konfigurasi Chart.js
    const config = {
        type: 'pie',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Distribusi Suara per Kandidat'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return label + ': ' + value + ' (' + percentage + '%)';
                        }
                    }
                },
                datalabels: {
                    color: '#fff',
                    font: {
                        weight: 'bold',
                        size: 14
                    },
                    formatter: function(value, context) {
                        const total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        const label = context.chart.data.labels[context.dataIndex];
                        return label + '\n' + percentage + '%';
                    }
                }
            }
        },
    };

        // Register plugin datalabels agar label % tampil di dalam pie slice
        Chart.register(ChartDataLabels);

        // Render pie chart ke canvas
        var pieChart = new Chart(
            document.getElementById('pieChart'),
            config
        );
    });
</script>
