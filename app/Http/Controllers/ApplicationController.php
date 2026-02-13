<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:applications,email',
            'phone' => 'required|string|max:20',
            'college_name' => 'required|string',
            'degree' => 'required|string',
            'last_exam' => 'required|string',
            'cgpa' => 'required',
            'preferred_domain' => 'required|string',
            'skills' => 'required|string',
            'resume' => 'required|file|mimes:pdf,doc,docx|max:10240',
        ]);

        $fileName = null;

        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
             $fileName = Str::slug($request->full_name) . '.' . $file->getClientOriginalExtension();
            $file->storeAs('resumes', $fileName, 'public');
            // $name = Str::slug($request->full_name); // mansi-gajjar
            // $extension = $file->getClientOriginalExtension();

            // $fileName = $name . '.' . $extension;

            // $file->store('resumes', $fileName, 'public');
        }

        Application::create([
            'name' => $request->full_name,
            'email' => $request->email,
            'phone' => $request->phone,
            'college' => $request->college_name,
            'degree' => $request->degree,
            'last_exam_appeared' => $request->last_exam,
            'cgpa' => $request->cgpa,
            'domain' => $request->preferred_domain,
            'skills' => $request->skills,
            'resume_path' => 'resumes/' . $fileName,
        ]);

        return response()->json([
            'message' => 'Application submitted successfully!'
        ]);
    }
}
