<!DOCTYPE html>
<html>
<head>
    <title>Workflow Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>

<h2>Workflow Report</h2>

<table>
    <thead>
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Status</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>{{ $workflow->id }}</td>
        <td>{{ $workflow->name }}</td>
        <td>{{ $workflow->status }}</td>
    </tr>
    </tbody>
</table>

</body>
</html>
