// assets/admin.js
import Chart from "chart.js/auto";

document.addEventListener("DOMContentLoaded", () => {
  const monthlyChart = document.getElementById("monthlyChart");
  const clientChart = document.getElementById("clientChart");
  const courseChart = document.getElementById("mostSaleCourses");
  const lessonChart = document.getElementById("mostSaleLessons");

  if (monthlyChart && clientChart && courseChart && lessonChart) {
    const monthlyData = JSON.parse(monthlyChart.dataset.chart);
    const clientData = JSON.parse(clientChart.dataset.chart);
    const courseData = JSON.parse(courseChart.dataset.chart);
    const lessonData = JSON.parse(lessonChart.dataset.chart);
    new Chart(monthlyChart, {
      type: "line",
      data: monthlyData,
      options: {
        responsive: true,
        plugins: {
          legend: { position: "top" },
          title: { display: true, text: "Ventes par mois" },
        },
      },
    });

    new Chart(clientChart, {
      type: "doughnut",
      data: clientData,
      options: {
        responsive: true,
        plugins: {
          legend: { position: "bottom" },
          title: { display: true, text: "Ventes par client" },
        },
      },
    });

    new Chart(courseChart, {
      type: "bar",
      data: courseData,
      options: {
        responsive: true,
        plugins: {
          legend: { position: "bottom" },
          title: { display: true, text: "Most sale courses" },
        },
      },
    });

    new Chart(lessonChart, {
      type: "bar",
      data: lessonData,
      options: {
        responsive: true,
        plugins: {
          legend: { position: "bottom" },
          title: { display: true, text: "Most sale lessons" },
        },
      },
    });
  }
});
