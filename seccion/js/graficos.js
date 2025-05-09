 // Configuración del gráfico de torta
 function crearGraficoPedidos(totalPendientes, totalEntregados, totalCancelados) {
    var ctx = document.getElementById('graficoPedidos').getContext('2d');
    var graficoPedidos = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Pendientes', 'Entregados', 'Cancelados'],
            datasets: [{
                data: [totalPendientes, totalEntregados, totalCancelados],
                backgroundColor: ['#FFCE56', 'green', '#FF6384']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            }
        }
    });
}