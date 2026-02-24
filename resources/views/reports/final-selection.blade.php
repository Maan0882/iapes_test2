<!DOCTYPE html>
<html>
<head>
    <title>Final Selection Report</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f2f2f2; }
        h2 { text-align: center; }
    </style>
</head>
<body>

<h2>Final Selected Interns</h2>

<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Total Score</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($interns as $intern)
        <tr>
            <td>{{ $intern->id }}</td>
            <td>{{ $intern->application->name ?? '-' }}</td>
            <td>{{ $intern->application->email ?? '-' }}</td>
            <td>{{ $intern->total }}</td>
            <td>
                @if($intern->total >= 35)
                    Selected
                @else
                    Not Selected
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>
