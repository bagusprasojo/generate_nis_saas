@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <div>
        <h1 class="h4 mb-0">{{ $mode === 'create' ? 'Tambah Sekolah' : 'Edit Sekolah' }}</h1>
        <div class="text-muted">Isi detail sekolah untuk pengelolaan NIS.</div>
    </div>
    <a class="btn btn-outline-secondary" href="{{ route('schools.index') }}">Kembali</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="POST" action="{{ $mode === 'create' ? route('schools.store') : route('schools.update', $school) }}">
            @csrf
            @if($mode === 'edit')
                @method('PUT')
            @endif
            <div class="mb-3">
                <label class="form-label" for="name">Nama Sekolah</label>
                <input class="form-control" type="text" id="name" name="name" value="{{ old('name', $school->name) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="code">Kode Sekolah</label>
                <input class="form-control" type="text" id="code" name="code" value="{{ old('code', $school->code) }}" required>
            </div>
            <div class="mb-3">
                <label class="form-label" for="address">Alamat</label>
                <textarea class="form-control" id="address" name="address" rows="3">{{ old('address', $school->address) }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Simpan</button>
        </form>
    </div>
</div>

@if($mode === 'edit')
    <div class="mt-3">
        <form method="POST" action="{{ route('schools.destroy', $school) }}" onsubmit="return confirm('Hapus sekolah ini?');">
            @csrf
            @method('DELETE')
            <button class="btn btn-outline-danger" type="submit">Hapus Sekolah</button>
        </form>
    </div>
@endif
@endsection
