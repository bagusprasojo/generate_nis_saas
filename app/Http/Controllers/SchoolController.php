<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;

class SchoolController extends Controller
{
    public function index()
    {
        $this->authorizeSuperAdmin();

        $schools = School::query()
            ->with('pattern')
            ->latest()
            ->paginate(10);

        return view('schools.index', [
            'schools' => $schools,
        ]);
    }

    public function create()
    {
        $this->authorizeSuperAdmin();

        return view('schools.form', [
            'school' => new School(),
            'mode' => 'create',
        ]);
    }

    public function store(Request $request)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:schools,code'],
            'address' => ['nullable', 'string'],
        ]);

        $school = School::create($data);

        return redirect()
            ->route('schools.edit', $school)
            ->with('status', 'Sekolah berhasil dibuat.');
    }

    public function edit(School $school)
    {
        $this->authorizeSuperAdmin();

        return view('schools.form', [
            'school' => $school,
            'mode' => 'edit',
        ]);
    }

    public function update(Request $request, School $school)
    {
        $this->authorizeSuperAdmin();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:50', 'unique:schools,code,' . $school->id],
            'address' => ['nullable', 'string'],
        ]);

        $school->update($data);

        return redirect()
            ->route('schools.edit', $school)
            ->with('status', 'Sekolah berhasil diperbarui.');
    }

    public function destroy(School $school)
    {
        $this->authorizeSuperAdmin();

        $school->delete();

        return redirect()
            ->route('schools.index')
            ->with('status', 'Sekolah berhasil dihapus.');
    }

    private function authorizeSuperAdmin(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->isSuperAdmin()) {
            abort(403);
        }
    }
}
