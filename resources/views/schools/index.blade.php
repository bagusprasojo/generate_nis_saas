@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">Daftar Sekolah</h1>
        <div class="text-muted">Kelola data sekolah dan pola NIS.</div>
    </div>
    <a class="btn btn-primary" href="{{ route('schools.create') }}">Tambah Sekolah</a>
</div>

<div class="card shadow-sm">
    <div class="table-responsive">
        <table class="table table-striped mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama</th>
                    <th>Kode</th>
                    <th>Pola NIS</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schools as $school)
                    <tr>
                        <td class="fw-semibold">{{ $school->name }}</td>
                        <td>{{ $school->code }}</td>
                        <td>
                            @if($school->pattern)
                                <span class="badge text-bg-success">Tersedia</span>
                            @else
                                <span class="badge text-bg-warning">Belum ada</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="btn-group">
                                <a class="btn btn-outline-primary btn-sm" href="{{ route('schools.edit', $school) }}">Edit</a>
                                <a class="btn btn-outline-secondary btn-sm" href="{{ route('patterns.edit', $school) }}">Pola NIS</a>
                                <a class="btn btn-outline-info btn-sm" href="{{ route('students.index', $school) }}">Siswa</a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">Belum ada sekolah.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="mt-3">
    {{ $schools->links() }}
</div>
@endsection
