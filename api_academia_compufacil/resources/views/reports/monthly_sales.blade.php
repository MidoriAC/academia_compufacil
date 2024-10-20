<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas Mensuales</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
        }
        .container {
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background-color: #3498db;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Reporte de Ventas Mensuales</h1>
        <table>
            <thead>
                <tr>
                    <th>Mes</th>
                    <th>Total de Ventas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($monthlySales as $month => $total)
                <tr>
                    <td>{{ $month }}</td>
                    <td>Q{{ number_format($total, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="total-revenue">
            <h2>Ingresos Totales: Q{{ number_format($totalRevenue, 2) }}</h2>
        </div>
        <div class="footer">
            <p>Reporte generado el {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>