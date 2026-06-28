// ======================================================
// FUNCTION
// ======================================================

let dashboardChart = null;

function RenderPartisipasiChart(labels, series) {
    const chartTarget = document.querySelector('#chart_partisipasi_responden');

    if (!chartTarget || typeof ApexCharts === 'undefined') {
        return;
    }

    if (dashboardChart !== null) {
        dashboardChart.destroy();
        dashboardChart = null;
    }

    chartTarget.innerHTML = '';

    const options = {
        series: [{
            name: 'Responden',
            data: series
        }],
        chart: {
            height: 360,
            type: 'area',
            toolbar: {
                show: false
            },
            zoom: {
                enabled: false
            },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        dataLabels: {
            enabled: false
        },
        stroke: {
            curve: 'smooth',
            width: 3
        },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 0.8,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        colors: ['#0d6efd'],
        xaxis: {
            categories: labels,
            labels: {
                rotate: -45,
                trim: true
            }
        },
        yaxis: {
            min: 0,
            forceNiceScale: true,
            labels: {
                formatter: function (value) {
                    return Math.round(value);
                }
            }
        },
        grid: {
            borderColor: '#e9ecef',
            strokeDashArray: 4
        },
        tooltip: {
            y: {
                formatter: function (value) {
                    return value + ' responden';
                }
            }
        },
        noData: {
            text: 'Tidak ada data untuk ditampilkan'
        }
    };

    dashboardChart = new ApexCharts(chartTarget, options);
    dashboardChart.render();
}

// Menampilkan Count Dashboard
function ShowCount() {

    const targetPertanyaan = $('#jumlah_pertanyaan');
    const targetResponden = $('#jumlah_responden');
    const targetUndangan = $('#jumlah_undangan');
    const targetJawaban = $('#jumlah_jawaban');
    const targetNotifikasi = $('#notifikasi_count');

    targetPertanyaan.html('Loading..');
    targetResponden.html('Loading..');
    targetUndangan.html('Loading..');
    targetJawaban.html('Loading..');
    targetNotifikasi.html('');

    $.ajax({
        type    : 'POST',
        url     : '_Page/Dashboard/Count.php',
        data    : {},
        dataType: 'json',
        success : function(res) {
            const status = res.status;
            const message = res.message || 'Terjadi kesalahan.';

            if (status === 'success') {
                targetPertanyaan.html(res.jumlah_pertanyaan);
                targetResponden.html(res.jumlah_responden);
                targetUndangan.html(res.jumlah_undangan);
                targetJawaban.html(res.jumlah_jawaban);
                RenderPartisipasiChart(res.chart_labels || [], res.chart_series || []);
            }else{
                targetNotifikasi.html(
                    '<section class="section dashboard"><div class="alert alert-danger mb-0">' +
                    message +
                    '</div></section>'
                );
            }
        },
        error: function() {
            targetNotifikasi.html(
                '<section class="section dashboard"><div class="alert alert-danger mb-0">' +
                'Terjadi kesalahan saat mengambil data dashboard.' +
                '</div></section>'
            );
        }
    });
}

$(document).ready(function() {
    ShowCount();
});
