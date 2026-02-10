<?php

namespace App\Http\Controllers;

use App\Models\NisPattern;
use App\Models\School;
use App\Services\NisGenerator;
use Illuminate\Http\Request;
use InvalidArgumentException;

class PatternController extends Controller
{
    public function edit(School $school)
    {
        $this->authorizeSchoolAccess($school);

        return view('patterns.edit', [
            'school' => $school,
            'pattern' => $school->pattern ?? new NisPattern(),
            'preview' => null,
            'previewError' => null,
            'previewYear' => null,
        ]);
    }

    public function update(Request $request, School $school)
    {
        $this->authorizeSchoolAccess($school);

        $data = $request->validate([
            'pattern' => ['required', 'string', 'max:255'],
            'reset_rule' => ['required', 'in:yearly,intake,never'],
        ]);

        try {
            $this->validatePattern($data['pattern']);
        } catch (InvalidArgumentException $exception) {
            return back()
                ->withErrors(['pattern' => $exception->getMessage()])
                ->withInput();
        }

        NisPattern::updateOrCreate(
            ['school_id' => $school->id],
            [
                'pattern' => $data['pattern'],
                'reset_rule' => $data['reset_rule'],
            ]
        );

        return redirect()
            ->route('patterns.edit', $school)
            ->with('status', 'Pola NIS berhasil disimpan.');
    }

    public function preview(Request $request, School $school, NisGenerator $generator)
    {
        $this->authorizeSchoolAccess($school);

        $data = $request->validate([
            'intake_year' => ['required', 'digits:4'],
        ]);

        $preview = null;
        $error = null;

        try {
            $preview = $generator->preview($school, (int) $data['intake_year']);
        } catch (InvalidArgumentException $exception) {
            $error = $exception->getMessage();
        }

        return view('patterns.edit', [
            'school' => $school,
            'pattern' => $school->pattern ?? new NisPattern(),
            'preview' => $preview,
            'previewError' => $error,
            'previewYear' => $data['intake_year'],
        ]);
    }

    private function validatePattern(string $pattern): void
    {
        preg_match_all('/\{SEQ:(\d+)\}/', $pattern, $seqMatches);

        if (count($seqMatches[1]) !== 1) {
            throw new InvalidArgumentException('Pattern harus memiliki tepat satu placeholder {SEQ:n}.');
        }

        if ((int) $seqMatches[1][0] < 1) {
            throw new InvalidArgumentException('Nilai n pada {SEQ:n} harus lebih besar dari 0.');
        }

        preg_match_all('/\{([A-Z_]+)(?::\d+)?\}/', $pattern, $placeholders);
        $allowed = ['YEAR', 'YEAR_SHORT', 'SCHOOL_CODE', 'INTAKE_YEAR', 'SEQ'];

        foreach ($placeholders[1] as $name) {
            if (! in_array($name, $allowed, true)) {
                throw new InvalidArgumentException('Placeholder tidak dikenal: ' . $name);
            }
        }
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
