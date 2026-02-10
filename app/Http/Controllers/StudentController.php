<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Student;
use App\Services\NisGenerator;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use InvalidArgumentException;

class StudentController extends Controller
{
    public function index(School $school)
    {
        $this->authorizeSchoolAccess($school);

        $students = Student::query()
            ->where('school_id', $school->id)
            ->latest()
            ->paginate(15);

        return view('students.index', [
            'school' => $school,
            'students' => $students,
        ]);
    }

    public function create(School $school)
    {
        $this->authorizeSchoolAccess($school);

        return view('students.create', [
            'school' => $school,
            'pattern' => $school->pattern,
        ]);
    }

    public function store(Request $request, School $school, NisGenerator $generator)
    {
        $this->authorizeSchoolAccess($school);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'intake_year' => ['required', 'digits:4'],
        ]);

        try {
            $student = $generator->createStudent($school, $data);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withErrors(['pattern' => $exception->getMessage()])
                ->withInput();
        } catch (QueryException $exception) {
            return back()
                ->withErrors(['nis' => 'NIS tidak bisa dibuat, silakan coba lagi.'])
                ->withInput();
        }

        return redirect()
            ->route('students.index', $school)
            ->with('status', 'Siswa berhasil ditambahkan dengan NIS: ' . $student->nis);
    }

    private function authorizeSchoolAccess(School $school): void
    {
        $user = auth()->user();

        if (! $user) {
            abort(403);
        }

        if ($user->isSuperAdmin()) {
            return;
        }

        if ($user->isSchoolAdmin() && (int) $user->school_id === (int) $school->id) {
            return;
        }

        abort(403);
    }
}
