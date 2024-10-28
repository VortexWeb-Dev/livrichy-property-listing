const leftChartData = {
  labels: ["Sales", "Rent", "Property Finder", "Bayut", "Website"],
  datasets: [
    {
      label: "Left Chart",
      data: [1, 0, 1, 1, 1],
      backgroundColor: [
        "rgba(54, 162, 235, 0.6)",
        "rgba(75, 192, 192, 0.6)",
        "rgba(153, 102, 255, 0.6)",
        "rgba(255, 159, 64, 0.6)",
        "rgba(255, 205, 86, 0.6)",
      ],
      borderColor: [
        "rgba(54, 162, 235, 1)",
        "rgba(75, 192, 192, 1)",
        "rgba(153, 102, 255, 1)",
        "rgba(255, 159, 64, 1)",
        "rgba(255, 205, 86, 1)",
      ],
      borderWidth: 1,
    },
  ],
};

// Right Chart Data
const rightChartData = {
  labels: ["Sales", "Rent", "Property Finder", "Bayut", "Website"],
  datasets: [
    {
      label: "Right Chart",
      data: [279, 33, 245, 282, 312],
      backgroundColor: [
        "rgba(255, 99, 132, 0.6)",
        "rgba(54, 162, 235, 0.6)",
        "rgba(255, 206, 86, 0.6)",
        "rgba(75, 192, 192, 0.6)",
        "rgba(153, 102, 255, 0.6)",
      ],
      borderColor: [
        "rgba(255, 99, 132, 1)",
        "rgba(54, 162, 235, 1)",
        "rgba(255, 206, 86, 1)",
        "rgba(75, 192, 192, 1)",
        "rgba(153, 102, 255, 1)",
      ],
      borderWidth: 1,
    },
  ],
};

// Configuring the Charts
const config = {
  type: "doughnut",
  options: {
    responsive: true,
    plugins: {
      legend: {
        display: false,
      },
    },
  },
};

// Render Left Chart
const leftChart = new Chart(document.getElementById("leftChart"), {
  ...config,
  data: leftChartData,
});

// Render Right Chart
const rightChart = new Chart(document.getElementById("rightChart"), {
  ...config,
  data: rightChartData,
});
