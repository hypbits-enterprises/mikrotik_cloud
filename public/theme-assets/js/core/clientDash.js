const { endsWith } = require("lodash");

// get an object by id 
function cObj(id) {
    return document.getElementById(id);
}

function plotGraph(weekly_data, ctx_id, title, myChart = null) {
    if (myChart != null) {
        myChart.destroy();
    }
    var data = [weekly_data];
    console.log(data);
    var show_x_axis = true;
    var show_y_axis = true;
    
    var ctx = cObj(ctx_id);
    var type = "bar" //line, pie, bar, doughnut, polarArea, radar;
    var backgroundColor = ['rgb(201, 48, 215)'];
    if (type == "pie") {
        backgroundColor = [];
        for (let index = 0; index < data[0].length; index++) {
            const element = data[0][index];
            var rand_red = generateRandomNumber(100,255);
            var rand_green = generateRandomNumber(100,255);
            var rand_blue = generateRandomNumber(100,255);
            var rand_color = 'rgb('+rand_red+', '+rand_green+', '+rand_blue+')';
            backgroundColor.push(rand_color);
        }
    }

    // get the labels
    var labels = [];
    var chart_data = [];
    for (let index = 0; index < data[0].length; index++) {
        const element = data[0][index];
        labels.push(element.X);
        chart_data.push(element.amount);
    }

    myChart = new Chart(ctx, {
        type: type,
        data: {
            labels:labels,
            datasets: [{
                tension: 0.4,
                label: 'Commission in Kes',
                data: chart_data,
                borderWidth: 1,
                font: {
                    size: 14
                },
                backgroundColor: 'rgba(255, 189, 103, 0.3)',
                borderColor:'rgb(255, 131, 10)',
                fill: true
            }],
            hoverOffset: 4
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks:{
                        stepSize: 1
                    },
                    grid:{
                        display:true,
                        drawOnChartArea:true,
                        drawTicks:true
                    },
                    display:show_y_axis
                },
                x:{
                    grid:{
                        display:true,
                        drawOnChartArea:true,
                        drawTicks:true
                    },
                    display:show_x_axis
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: title,
                    font: {
                        family: 'Comfortaa, sans-serif',
                        size: 14,
                        weight: 'bold',
                        style: 'normal'
                    },
                },
                legend: {
                    display: true,
                    position: 'bottom',
                    font: {
                        family: 'Comfortaa, sans-serif',
                        size: 14,
                        weight: 'bold',
                        style: 'normal'
                    }
                },
                tooltip: {
                    enabled: true,
                    titleFont: { family: 'Comfortaa, sans-serif', size: 14, weight: 'bold' },
                    bodyFont: { family: 'Comfortaa, sans-serif', size: 12 }
                },
            }
        }
    });
}