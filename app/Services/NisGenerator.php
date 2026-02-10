<?php

namespace App\Services;

use App\Models\NisPattern;
use App\Models\NisSequence;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class NisGenerator
{
    public function preview(School $school, int $intakeYear): string
    {
        $pattern = $this->getPatternOrFail($school);
        $resetKey = $this->resolveResetKey($pattern, $intakeYear);

        $sequence = NisSequence::query()
            ->where('school_id', $school->id)
            ->where('reset_key', $resetKey)
            ->first();

        $nextSequence = ($sequence?->last_sequence ?? 0) + 1;

        return $this->renderPattern($pattern->pattern, $school, $intakeYear, $nextSequence);
    }

    public function createStudent(School $school, array $data): Student
    {
        $intakeYear = (int) $data['intake_year'];

        return DB::transaction(function () use ($school, $data, $intakeYear) {
            $pattern = $this->getPatternOrFail($school);
            $resetKey = $this->resolveResetKey($pattern, $intakeYear);

            $sequence = $this->lockSequence($school->id, $resetKey);
            $nextSequence = $sequence->last_sequence + 1;

            $nis = $this->renderPattern($pattern->pattern, $school, $intakeYear, $nextSequence);

            $student = Student::create([
                'school_id' => $school->id,
                'name' => $data['name'],
                'intake_year' => $intakeYear,
                'nis' => $nis,
            ]);

            $sequence->last_sequence = $nextSequence;
            $sequence->save();

            return $student;
        });
    }

    private function getPatternOrFail(School $school): NisPattern
    {
        $pattern = $school->pattern()->first();

        if (! $pattern) {
            throw new InvalidArgumentException('Sekolah belum memiliki pola NIS.');
        }

        return $pattern;
    }

    private function resolveResetKey(NisPattern $pattern, int $intakeYear): string
    {
        return match ($pattern->reset_rule) {
            'yearly' => $this->now()->format('Y'),
            'intake' => (string) $intakeYear,
            default => 'global',
        };
    }

    private function lockSequence(int $schoolId, string $resetKey): NisSequence
    {
        $sequence = NisSequence::query()
            ->where('school_id', $schoolId)
            ->where('reset_key', $resetKey)
            ->lockForUpdate()
            ->first();

        if ($sequence) {
            return $sequence;
        }

        try {
            NisSequence::create([
                'school_id' => $schoolId,
                'reset_key' => $resetKey,
                'last_sequence' => 0,
            ]);
        } catch (QueryException $exception) {
            // Another transaction may have created the sequence row first.
        }

        return NisSequence::query()
            ->where('school_id', $schoolId)
            ->where('reset_key', $resetKey)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function renderPattern(string $pattern, School $school, int $intakeYear, int $sequence): string
    {
        $sequenceLength = $this->extractSequenceLength($pattern);

        $now = $this->now();

        $replacements = [
            '{YEAR_SHORT}' => $now->format('y'),
            '{YEAR}' => $now->format('Y'),
            '{SCHOOL_CODE}' => $school->code,
            '{INTAKE_YEAR}' => (string) $intakeYear,
        ];

        $value = str_replace(array_keys($replacements), array_values($replacements), $pattern);
        $value = preg_replace(
            '/\{SEQ:(\d+)\}/',
            str_pad((string) $sequence, $sequenceLength, '0', STR_PAD_LEFT),
            $value,
            1
        );

        return $value;
    }

    private function extractSequenceLength(string $pattern): int
    {
        preg_match_all('/\{SEQ:(\d+)\}/', $pattern, $matches);

        if (count($matches[1]) !== 1) {
            throw new InvalidArgumentException('Pattern harus memiliki tepat satu placeholder {SEQ:n}.');
        }

        $length = (int) $matches[1][0];

        if ($length < 1) {
            throw new InvalidArgumentException('Nilai n pada {SEQ:n} harus lebih besar dari 0.');
        }

        return $length;
    }

    private function now(): Carbon
    {
        return Carbon::now(config('app.timezone'));
    }
}
