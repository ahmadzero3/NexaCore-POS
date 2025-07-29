$(function () {
    "use strict";

    // chart 1

    var ctx = document.getElementById("chart1").getContext("2d");

    var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke1.addColorStop(0, "#6078ea");
    gradientStroke1.addColorStop(1, "#17c5ea");

    var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke2.addColorStop(0, "#ff8359");
    gradientStroke2.addColorStop(1, "#ffdf40");

    var myChart = new Chart(ctx, {
        type: "bar",
        data: {
            labels: chartMonths, //From Dashboard page
            datasets: [
                {
                    label: "Purchase",
                    data: chartPurchases,
                    borderColor: gradientStroke2,
                    backgroundColor: gradientStroke2,
                    hoverBackgroundColor: gradientStroke2,
                    pointRadius: 0,
                    fill: false,
                    borderRadius: 20,
                    borderWidth: 0,
                },
                {
                    label: "Sale",
                    data: chartSales, //From Dashboard page
                    borderColor: gradientStroke1,
                    backgroundColor: gradientStroke1,
                    hoverBackgroundColor: gradientStroke1,
                    pointRadius: 0,
                    fill: false,
                    borderRadius: 20,
                    borderWidth: 0,
                },
            ],
        },

        options: {
            maintainAspectRatio: false,
            barPercentage: 0.5,
            categoryPercentage: 0.8,
            plugins: {
                legend: {
                    display: false,
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                },
            },
        },
    });

    // chart 2

    var ctx = document.getElementById("chart2").getContext("2d");

    var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke1.addColorStop(0, "#fc4a1a");
    gradientStroke1.addColorStop(1, "#f7b733");

    var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke2.addColorStop(0, "#4776e6");
    gradientStroke2.addColorStop(1, "#8e54e9");

    var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke3.addColorStop(0, "#ee0979");
    gradientStroke3.addColorStop(1, "#ff6a00");

    var gradientStroke4 = ctx.createLinearGradient(0, 0, 0, 300);
    gradientStroke4.addColorStop(0, "#42e695");
    gradientStroke4.addColorStop(1, "#3bb2b8");

    var myChart = new Chart(ctx, {
        type: "doughnut",
        data: {
            labels: serviceNames,
            datasets: [
                {
                    backgroundColor: [
                        gradientStroke1,
                        gradientStroke2,
                        gradientStroke3,
                        gradientStroke4,
                    ],
                    hoverBackgroundColor: [
                        gradientStroke1,
                        gradientStroke2,
                        gradientStroke3,
                        gradientStroke4,
                    ],
                    data: serviceCounts,
                    borderWidth: [1, 1, 1, 1],
                },
            ],
        },
        options: {
            maintainAspectRatio: false,
            cutout: 82,
            plugins: {
                legend: {
                    display: false,
                },
            },
        },
    });
});
