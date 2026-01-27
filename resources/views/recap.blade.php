@extends('layouts.admin')

@section('main-content')

<!-- Page Heading -->
<h1 class="h3 mb-4 text-gray-800">{{ __('Rekapitulasi') }}</h1>

@if (session('success'))
<div class="alert alert-success border-left-success alert-dismissible fade show" role="alert">
    {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

@if (session('status'))
<div class="alert alert-success border-left-success" role="alert">
    {{ session('status') }}
</div>
@endif

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Daftar Pemilih Tetap (DPT)</h6>
    </div>

    <div class="card-body">
        <a href="{{ route('recap.dpt') }}" class="btn btn-success btn-lg">Unduh Rekap DPT (.xlsx)</a>
        <div class="alert alert-info mt-2">
            Rekapitulasi terdiri dari DPT Dapil, DPT Kecamatan, dan DPT Desa.
        </div>
    </div>

</div>

@endsection