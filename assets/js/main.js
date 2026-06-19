$(function () {
    const chartEl = document.querySelector('#salesChart');

    if (chartEl && window.ApexCharts) {
        const options = {
            chart: {
                height: 340,
                type: 'line',
                toolbar: {
                    show: false
                },
                fontFamily: 'inherit',
                animations: {
                    enabled: false
                }
            },
            series: [
                {
                    name: 'Penjualan',
                    type: 'column',
                    data: [32, 45, 38, 54, 49, 68, 73, 62, 81, 77, 89, 95]
                },
                {
                    name: 'Target',
                    type: 'line',
                    data: [28, 40, 42, 50, 55, 60, 69, 71, 76, 80, 84, 90]
                }
            ],
            colors: ['#98CD00', '#2E7D32'],
            stroke: {
                width: [0, 4],
                curve: 'smooth'
            },
            dataLabels: {
                enabled: false
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '50%'
                }
            },
            grid: {
                borderColor: 'rgba(23, 48, 24, 0.08)'
            },
            xaxis: {
                categories: ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des']
            },
            yaxis: [
                {
                    title: {
                        text: 'Penjualan'
                    }
                },
                {
                    opposite: true,
                    title: {
                        text: 'Target'
                    }
                }
            ],
            legend: {
                position: 'top',
                horizontalAlign: 'left'
            },
            tooltip: {
                shared: true,
                intersect: false
            }
        };

        window.requestAnimationFrame(function () {
            new ApexCharts(chartEl, options).render();
        });
    }

    const offcanvasEl = document.getElementById('adminNavbar');
    if (offcanvasEl) {
        offcanvasEl.addEventListener('show.bs.offcanvas', function () {
            document.body.classList.add('nav-open');
        });

        offcanvasEl.addEventListener('hidden.bs.offcanvas', function () {
            document.body.classList.remove('nav-open');
        });
    }

    $('.admin-offcanvas .nav-link').on('click', function (event) {
        const target = $(this).attr('data-bs-toggle');
        if (target === 'dropdown') {
            return;
        }

        if ($(window).width() < 992 && offcanvasEl) {
            const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(offcanvasEl);
            offcanvas.hide();
        }
    });
});
