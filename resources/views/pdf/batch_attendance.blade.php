<h2>Interview Attendance Sheet</h2>

<p><strong>Batch:</strong> {{ $batch->batch_name }}</p>
<p><strong>Date:</strong> {{ $batch->interview_date }}</p>
<p><strong>Time:</strong> {{ $batch->start_time }} - {{ $batch->end_time }}</p>
<p><strong>Location:</strong> {{ $batch->location }}</p>

<hr>

<table width="100%" border="1" cellpadding="5" cellspacing="0">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Attendance</th>
        </tr>
    </thead>
    <tbody>
        @foreach($applications as $app)
            <tr>
                <td>{{ $app->name }}</td>
                <td>{{ $app->email }}</td>
                <td>{{ $app->status }}</td>
                <td>{{ $app->attendance }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
