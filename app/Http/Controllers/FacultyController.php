<?php

namespace App\Http\Controllers;

use App\Models\Faculty;
use App\Models\Program;
use Illuminate\Http\Request;

class FacultyController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Faculty::class, 'faculty');
    }

       public function index()
    {
        $faculties = Faculty::paginate(10);

        return view('faculties.index', ['faculties' => $faculties]);
    }

    public function create()
    {
        return view('faculties.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);

        $faculty = new Faculty();
        $faculty->name = $validated['name'];
        $faculty->save();

        return redirect()->intended('faculties');
    }

    public function show(Faculty $faculty)
    {
        abort(404);
    }


    public function edit(Faculty $faculty)
    {
        return view('faculties.edit', ['faculty'=>$faculty]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Faculty  $faculty
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, Faculty $faculty)
    {
        $validated = $request->validate([
            'name' => 'required|string'
        ]);

        $faculty->update([
            'name' => $validated['name']
        ]);

        return redirect(route('faculties.index'));
    }


    public function destroy(Faculty $faculty)
    {
        $faculty->delete();

        return redirect('faculties');
    }
}
