// enable tooltips everywhere
$(function () {
    $('[data-toggle="tooltip"]').tooltip();
});

function cObj(id) {
    return document.getElementById(id);
}

window.onload = function () {
    let table = $('#transactions_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/Transactions/datatable',
            type: 'GET',
        },
        order: [[0, 'desc']],
        dom: '<"bottom"l>t<"bottom"ip>',
        pageLength: 20,
        lengthMenu: [10, 20, 50, 100],
        columns: [
            { data: 'rownum',   orderable: true,  searchable: false },
            { data: 'mpesa_id', orderable: true,  searchable: false },
            { data: 'account',  orderable: true,  searchable: false },
            { data: 'amount',   orderable: true,  searchable: false },
            { data: 'actions',  orderable: false, searchable: false },
        ]
    });

    $('#searchkey').on('keyup', function () {
        table.search(this.value).draw();
    });

    table.on('draw.dt', function () {
        $('[data-toggle="tooltip"]').tooltip();
        applyHoverEffect(document.getElementById('transactions_table'));
    });

    setTimeout(function () {
        plotGraph(collection_stats);
    }, 500);
};

var myChart;
function plotGraph(client_data) {
    if (myChart != null) {
        myChart.destroy();
    }
    var data = [client_data];
    var show_x_axis = true;
    var show_y_axis = true;

    var ctx = cObj('transaction_collection_stat');
    var type = 'line';
    var labels = [];
    var chart_data = [];
    for (var i = 0; i < data[0].length; i++) {
        labels.push(data[0][i].date);
        chart_data.push(data[0][i].amount);
    }

    myChart = new Chart(ctx, {
        type: type,
        data: {
            labels: labels,
            datasets: [{
                tension: 0.4,
                label: 'Payments in Kes',
                data: chart_data,
                borderWidth: 1,
                backgroundColor: 'rgba(55, 61, 125,0.3)',
                borderColor: 'rgb(55, 61, 125)',
                fill: true
            }],
            hoverOffset: 4
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true, display: show_y_axis },
                x: { display: show_x_axis }
            },
            plugins: {
                title: {
                    display: true,
                    text: 'Payments received in the last 7 days',
                    font: { family: 'Comfortaa, sans-serif', size: 14, weight: 'bold' }
                },
                legend: { display: true, position: 'bottom' },
                tooltip: { enabled: true }
            }
        }
    });
}

var closedWin = 0;
function applyHoverEffect(container) {
    if (!container) return;
    container.querySelectorAll('.btn').forEach(function (button) {
        if (button.dataset.hoverApplied) return;
        button.dataset.hoverApplied = '1';
        var span = button.querySelector('.d-inline-block');
        if (!span) return;
        var origBg       = getComputedStyle(button).backgroundColor;
        var origSpanColor = getComputedStyle(span).color;
        var origSpanBg   = getComputedStyle(span).backgroundColor;
        button.addEventListener('mouseenter', function () {
            var btnBg = getComputedStyle(button).backgroundColor;
            button.style.backgroundColor = getComputedStyle(span).color;
            button.style.transition = 'background-color 0.3s ease';
            span.style.backgroundColor = '#fff';
            span.style.color = btnBg;
            span.style.setProperty('border-color', btnBg, 'important');
            span.style.transition = 'color 0.3s ease, background-color 0.3s ease, border-color 0.3s ease';
        });
        button.addEventListener('mouseleave', function () {
            button.style.backgroundColor = origBg;
            span.style.backgroundColor = origSpanBg;
            span.style.color = origSpanColor;
            span.style.borderColor = '';
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var showTotalsBtn = cObj('show_totals');
    if (showTotalsBtn) {
        showTotalsBtn.onclick = function () {
            if (closedWin === 0) {
                cObj('totals_window').classList.remove('d-none');
                closedWin = 1;
                this.innerHTML = "<span class=\"d-inline-block border border-white w-100\" style=\"border-radius:2px;padding:5px;\"><i class='ft-eye-off'></i> Hide Totals</span>";
            } else {
                cObj('totals_window').classList.add('d-none');
                closedWin = 0;
                this.innerHTML = "<span class=\"d-inline-block border border-white w-100\" style=\"border-radius:2px;padding:5px;\"><i class='ft-eye'></i> Show Totals</span>";
            }
        };
    }

    var reportsBtn = cObj('transaction_reports_btn');
    if (reportsBtn) {
        reportsBtn.onclick = function () {
            cObj('show_generate_reports_window').classList.toggle('hide');
        };
    }

    var dateOption = cObj('transaction_date_option');
    if (dateOption) {
        dateOption.onchange = function () {
            var option = this.value;
            cObj('select_date_win').classList.add('hide');
            cObj('select_from_date_win').classList.add('hide');
            cObj('select_to_date_win').classList.add('hide');
            if (option === 'select date') {
                cObj('select_date_win').classList.remove('hide');
            } else if (option === 'between dates') {
                cObj('select_from_date_win').classList.remove('hide');
                cObj('select_to_date_win').classList.remove('hide');
            }
        };
    }

    var userOption = cObj('select_user_option');
    if (userOption) {
        userOption.onchange = function () {
            if (this.value === 'specific_user') {
                cObj('client_status_opt').classList.remove('hide');
            } else {
                cObj('client_status_opt').classList.add('hide');
            }
        };
    }
});
