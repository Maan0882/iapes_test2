<h2>Interview Report</h2>

<table border="1" width="100%" cellpadding="5">
    <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Problem Solving</th>
        <th>Aptitude</th>
        <th>Total</th>
    </tr>

    @foreach($evaluations as $eval)
    <tr>
        <td>{{ $eval->application->name }}</td>
        <td>{{ $eval->application->email }}</td>
        <td>{{ $eval->problem_solving }}</td>
        <td>{{ $eval->aptitude }}</td>
        <td>{{ $eval->total }}</td>
    </tr>
    @endforeach
</table>
