<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AssessmentController extends Controller
{
    public function index()
    {
        $assessments = Assessment::orderBy('created_at', 'desc')->get();

        return view('assessments.index', [
            'assessments' => $assessments,
        ]);
    }

    public function create()
    {
        return view('assessments.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'assessment_name' => 'required|string',
            'assessment_type' => 'required|in:CIA,ETA',
        ]);

        $validatedData = $request->all();
        $validatedData['user_id'] = Auth::id(); // Associate the assessment with the current user

        // Check if the combination of assessment name and type already exists
        $exists = Assessment::where('assessment_name', $validatedData['assessment_name'])
                            ->where('assessment_type', $validatedData['assessment_type'])
                            ->exists();

        if ($exists) {
            return redirect()->back()->withInput()->withErrors([
                'assessment_name' => 'The combination of assessment name and type already exists.',
            ]);
        }

        Assessment::create($validatedData);

        return redirect()->route('assessments.index');
    }

    public function show(Assessment $assessment)
    {
        return view('assessments.show', [
            'assessment' => $assessment,
        ]);
    }

    public function edit(Assessment $assessment)
    {
        return view('assessments.edit', [
            'assessment' => $assessment,
        ]);
    }

    public function update(Request $request, Assessment $assessment)
    {
        $validated = $request->validate([
            'assessment_name' => 'required|string',
            'assessment_type' => 'required|in:CIA,ETA',
        ]);

        $assessment->update($validated);

        return redirect()->route('assessments.index');
    }

    public function destroy(Assessment $assessment)
    {
        $assessment->delete();

        return redirect()->route('assessments.index');
    }
}
