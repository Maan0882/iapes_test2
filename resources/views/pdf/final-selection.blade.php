<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Final Selection Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: bold;
        }

        .sub-title {
            font-size: 14px;
            margin-top: 5px;
        }

        .info {
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }

        .selected {
            color: green;
            font-weight: bold;
        }

        .not-selected {
            color: red;
        }

        .footer {
            margin-top: 60px;
            width: 100%;
        }

        .signature {
            width: 45%;
            display: inline-block;
            text-align: center;
        }

        .confidential {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: gray;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="title">INTERNSHIP FINAL SELECTION REPORT</div>
    <div class="sub-title">Placement Evaluation Summary</div>
</div>

<div class="info">
    <strong>Batch:</strong> {{ $batch->batch_name }} <br>
    <strong>Date:</strong> {{ now()->format('d M Y') }} <br>
    <strong>Total Candidates:</strong> {{ count($interns) }}
</div>

<table>
    <thead>
        <tr>
            <th>Rank</th>
            <th>Name</th>
            <th>Problem Solving</th>
            <th>Aptitude</th>
            <th>Total</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($interns as $intern)
        <tr>
            <td>{{ $intern->rank }}</td>
            <td>{{ $intern->intern_name }}</td>
            <td>{{ $intern->problem_solving }}</td>
            <td>{{ $intern->aptitude }}</td>
            <td>{{ $intern->total_score }}</td>
            <td class="{{ $intern->total_score >= 60 ? 'selected' : 'not-selected' }}">
                {{ $intern->ai_suggestion }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

<div class="footer">
    <div class="signature">
        _______________________<br>
        HR Manager
    </div>

    <div class="signature" style="float:right;">
        _______________________<br>
        Technical Panel
    </div>
</div>

<div class="confidential">
    This document is confidential and intended for internal recruitment use only.
</div>

</body>
</html>
