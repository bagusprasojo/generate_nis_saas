@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Tambah Siswa - {{ $school->name }}</h1>
        <div class="text-muted">NIS akan dibuat otomatis sesuai pola.</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('students.index', $school) }}">Kembali</a>
</div>

@if(!$pattern)
    <div class="alert alert-warning">
        Sekolah ini belum memiliki pola NIS. Silakan atur pola terlebih dahulu.
        <a href="{{ route('patterns.edit', $school) }}" class="alert-link">Atur Pola NIS</a>.
    </div>
@else
    <div class="card shadow-sm">
        <div class="card-body">
            <form method="POST" action="{{ route('students.store', $school) }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label" for="name">Nama Siswa</label>
                    <input class="form-control" type="text" id="name" name="name" value="{{ old('name') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="intake_year">Tahun Masuk</label>
                    <input class="form-control" type="text" id="intake_year" name="intake_year" value="{{ old('intake_year') }}" placeholder="2026" required>
                </div>
                <button class="btn btn-primary" type="submit">Simpan</button>
            </form>
        </div>
    </div>
@endif
@endsection
