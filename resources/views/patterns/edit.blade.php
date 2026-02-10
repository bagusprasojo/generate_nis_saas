@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Pola NIS - {{ $school->name }}</h1>
        <div class="text-muted">Atur template NIS untuk sekolah ini.</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ auth()->user()->isSuperAdmin() ? route('schools.index') : route('students.index', $school) }}">Kembali</a>
</div>

<div class="row g-3">
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body" x-data="{ showHelp: false }">
                <form method="POST" action="{{ route('patterns.update', $school) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label" for="pattern">Pattern</label>
                        <input class="form-control" type="text" id="pattern" name="pattern" value="{{ old('pattern', $pattern->pattern ?? '') }}" placeholder="{YEAR}{SEQ:4}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="reset_rule">Aturan Reset Sequence</label>
                        <select class="form-select" id="reset_rule" name="reset_rule" required>
                            @php
                                $current = old('reset_rule', $pattern->reset_rule ?? 'never');
                            @endphp
                            <option value="yearly" {{ $current === 'yearly' ? 'selected' : '' }}>Yearly (reset tiap tahun)</option>
                            <option value="intake" {{ $current === 'intake' ? 'selected' : '' }}>Intake (reset per angkatan)</option>
                            <option value="never" {{ $current === 'never' ? 'selected' : '' }}>Never (tanpa reset)</option>
                        </select>
                    </div>
                    <button class="btn btn-primary" type="submit">Simpan Pola</button>
                    <button class="btn btn-outline-secondary" type="button" x-on:click="showHelp = !showHelp">
                        Lihat Placeholder
                    </button>
                </form>

                <div class="mt-3" x-show="showHelp" x-transition>
                    <div class="border rounded p-3 bg-light">
                        <div class="fw-semibold mb-2">Placeholder yang didukung</div>
                        <ul class="mb-0">
                            <li><code>{YEAR}</code> Tahun sekarang (2026)</li>
                            <li><code>{YEAR_SHORT}</code> Tahun 2 digit (26)</li>
                            <li><code>{SCHOOL_CODE}</code> Kode sekolah</li>
                            <li><code>{INTAKE_YEAR}</code> Tahun masuk siswa</li>
                            <li><code>{SEQ:n}</code> Nomor urut dengan padding n digit</li>
                        </ul>
                        <div class="text-muted small mt-2">Pattern wajib memiliki tepat satu <code>{SEQ:n}</code>.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h6">Preview NIS (dry-run)</h2>
                <form method="POST" action="{{ route('patterns.preview', $school) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label" for="intake_year">Tahun Masuk</label>
                        <input class="form-control" type="text" id="intake_year" name="intake_year" value="{{ old('intake_year', $previewYear ?? '') }}" placeholder="2026" required>
                    </div>
                    <button class="btn btn-outline-primary" type="submit">Preview</button>
                </form>

                @isset($preview)
                    <div class="mt-3">
                        <div class="text-muted">Hasil Preview</div>
                        <div class="fs-5 fw-semibold">{{ $preview }}</div>
                    </div>
                @endisset

                @if(!empty($previewError))
                    <div class="alert alert-warning mt-3">{{ $previewError }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
