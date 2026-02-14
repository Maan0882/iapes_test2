<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\InterviewEvaluation;

class ReportController extends Controller
{
    public function generate()
    {
        $evaluations = InterviewEvaluation::with('application')->get();

        $pdf = Pdf::loadView('reports.interview', compact('evaluations'));

        return $pdf->download('Interview_Report.pdf');
    }
}
