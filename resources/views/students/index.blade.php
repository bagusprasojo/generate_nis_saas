@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Daftar Siswa - {{ $school->name }}</h1>
        <div class="text-muted">Data siswa hanya untuk sekolah ini.</div>
    </div>
    <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="{{ route('patterns.edit', $school) }}">Pola NIS</a>
        <a class="btn btn-primary" href="{{ route('students.create', $school) }}">Tambah Siswa</a>
    </div>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>NIS</th>
                    <th>Tahun Masuk</th>
                    <th>Dibuat</th>
                </tr>
            </thead>
            <tbody>
                @forelse($students as $student)
                    <tr>
                        <td class="fw-semibold">{{ $student->name }}</td>
                        <td>{{ $student->nis }}</td>
                        <td>{{ $student->intake_year }}</td>
                        <td>{{ $student->created_at?->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada siswa.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $students->links() }}
</div>
@endsection
