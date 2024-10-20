<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas de Cursos</title>
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
        <h1>Estadísticas de Cursos</h1>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título del Curso</th>
                    <th>Total de Estudiantes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($statistics as $stat)
                <tr>
                    <td>{{ $stat->id }}</td>
                    <td>{{ $stat->title }}</td>
                    <td>{{ $stat->total_students }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="footer">
            <p>Reporte generado el {{ date('d/m/Y H:i:s') }}</p>
        </div>
    </div>
</body>
</html>