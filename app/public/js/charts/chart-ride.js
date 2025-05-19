document.addEventListener("DOMContentLoaded", function () {
	const ctx = document.getElementById('ridesChart')
	if (!ctx) return
	const labels = JSON.parse(ctx.dataset.labels)
	const data = JSON.parse(ctx.dataset.data)

	const chart = new Chart(ctx, {
		type: 'bar',
		data: {
			labels: labels,
			datasets: [{
				label: 'Covoiturages par jour',
				data: data,
				backgroundColor: 'rgba(54, 162, 235, 0.6)',
				borderColor: 'rgba(54, 162, 235, 1)',
				borderWidth: 1
			}]
		},
		options: {
			scales: {
				y: {
					beginAtZero: true,
					title: {
						display: true,
						text: 'Nombre de trajets'
					}
				},
				x: {
					title: {
						display: true,
						text: 'Date'
					}
				}
			}
		}
	});

	window.addEventListener("resize", () => {
		chart.resize();
	});
})