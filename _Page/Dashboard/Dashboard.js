// ======================================================
// CHART STATE
// ======================================================

const dashboardCharts = {};

// ======================================================
// FUNCTION
// ======================================================

function RenderChart(targetSelector, options) {
    const chartTarget = document.querySelector(targetSelector);

    if (!chartTarget || typeof ApexCharts === 'undefined') {
        return;
    }

    if (dashboardCharts[targetSelector]) {
        dashboardCharts[targetSelector].destroy();
        delete dashboardCharts[targetSelector];
    }

    chartTarget.innerHTML = '';

    dashboardCharts[targetSelector] = new ApexCharts(chartTarget, options);
    dashboardCharts[targetSelector].render();
}

function RenderMonthlyPartisipasiChart(labels, series) {
    RenderChart('#chart_partisipasi_responden', {
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
                formatter: function(value) {
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
                formatter: function(value) {
                    return value + ' responden';
                }
            }
        },
        noData: {
            text: 'Tidak ada data untuk ditampilkan'
        }
    });
}

function RenderGapPartisipasiChart(labels, series) {
    RenderChart('#chart_gap_partisipasi_responden', {
        series: series,
        chart: {
            height: 360,
            type: 'donut',
            toolbar: {
                show: false
            },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        labels: labels,
        colors: ['#6c757d', '#0d6efd'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' responden';
                }
            }
        },
        noData: {
            text: 'Tidak ada data untuk ditampilkan'
        }
    });
}

function RenderGenderChart(labels, series) {
    RenderChart('#chart_gender_responden', {
        series: series,
        chart: {
            height: 360,
            type: 'donut',
            toolbar: {
                show: false
            },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        labels: labels,
        colors: ['#198754', '#d63384'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' responden';
                }
            }
        },
        noData: {
            text: 'Tidak ada data untuk ditampilkan'
        }
    });
}

function RenderEncounterChart(labels, series) {
    RenderChart('#chart_encounter_responden', {
        series: series,
        chart: {
            height: 360,
            type: 'donut',
            toolbar: {
                show: false
            },
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        labels: labels,
        colors: ['#fd7e14', '#20c997'],
        legend: {
            position: 'bottom'
        },
        dataLabels: {
            enabled: true
        },
        plotOptions: {
            pie: {
                donut: {
                    size: '68%'
                }
            }
        },
        tooltip: {
            y: {
                formatter: function(value) {
                    return value + ' responden';
                }
            }
        },
        noData: {
            text: 'Tidak ada data untuk ditampilkan'
        }
    });
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
        type: 'POST',
        url: '_Page/Dashboard/Count.php',
        data: {},
        dataType: 'json',
        success: function(res) {
            const status = res.status;
            const message = res.message || 'Terjadi kesalahan.';

            if (status === 'success') {
                targetPertanyaan.html(res.jumlah_pertanyaan);
                targetResponden.html(res.jumlah_responden);
                targetUndangan.html(res.jumlah_undangan);
                targetJawaban.html(res.jumlah_jawaban);

                RenderMonthlyPartisipasiChart(
                    res.chart_labels || [],
                    res.chart_series || []
                );

                RenderGapPartisipasiChart(
                    res.chart_gap_labels || [],
                    res.chart_gap_series || []
                );

                RenderGenderChart(
                    res.chart_gender_labels || [],
                    res.chart_gender_series || []
                );

                RenderEncounterChart(
                    res.chart_encounter_labels || [],
                    res.chart_encounter_series || []
                );
            } else {
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
