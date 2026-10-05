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
            borderWidth: 1
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
                    }
                }
            },
        };

        // Render pie chart ke canvas
        var pieChart = new Chart(
            document.getElementById('pieChart'),
            config
        );
    });
</script>
