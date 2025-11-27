@extends('layouts.admin')

@section('main-content')

    <!-- Page Heading -->
    <h1 class="h3 mb-4 text-gray-800">{{ __('Daftar Pemilih Tetap') }}</h1>

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
        <h6 class="m-0 font-weight-bold text-primary">Daftar Kecamatan Kabupaten Trenggalek</h6>
    </div>

    <div class="card-body">


    <div class="row">
        <div class="col-12 col-md-6 col-lg-4 mb-4">         
            <a href="{{ route('dapil') }}/1" class="btn btn-primary p-3 flex-fill mx-2 shadow-lg text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                <i class="fas fa-chart-bar fa-3x mb-2"></i>
                <span class="font-weight-bold text-lg">DAPIL 1</span>
                <span>{{ implode(', ', config('kecamatan.group_dapil.1')) }}</span>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">         
            <a href="{{ route('dapil') }}/2" class="btn btn-primary p-3 flex-fill mx-2 shadow-lg text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                <i class="fas fa-chart-bar fa-3x mb-2"></i>
                <span class="font-weight-bold text-lg">DAPIL 2</span>
                <span>{{ implode(', ', config('kecamatan.group_dapil.2')) }}</span>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">         
            <a href="{{ route('dapil') }}/3" class="btn btn-primary p-3 flex-fill mx-2 shadow-lg text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                <i class="fas fa-chart-bar fa-3x mb-2"></i>
                <span class="font-weight-bold text-lg">DAPIL 3</span>
                <span>{{ implode(', ', config('kecamatan.group_dapil.3')) }}</span>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">         
            <a href="{{ route('dapil') }}/4" class="btn btn-primary p-3 flex-fill mx-2 shadow-lg text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                <i class="fas fa-chart-bar fa-3x mb-2"></i>
                <span class="font-weight-bold text-lg">DAPIL 4</span>
                <span>{{ implode(', ', config('kecamatan.group_dapil.4')) }}</span>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">         
            <a href="{{ route('dapil') }}/5" class="btn btn-primary p-3 flex-fill mx-2 shadow-lg text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                <i class="fas fa-chart-bar fa-3x mb-2"></i>
                <span class="font-weight-bold text-lg">DAPIL 5</span>
                <span>{{ implode(', ', config('kecamatan.group_dapil.5')) }}</span>
            </a>
        </div>
        <div class="col-12 col-md-6 col-lg-4 mb-4">         
            <a href="{{ route('dapil') }}/6" class="btn btn-primary p-3 flex-fill mx-2 shadow-lg text-center d-flex flex-column align-items-center justify-content-center" style="min-height: 150px;">
                <i class="fas fa-chart-bar fa-3x mb-2"></i>
                <span class="font-weight-bold text-lg">DAPIL 6</span>
                <span>{{ implode(', ', config('kecamatan.group_dapil.6')) }}</span>
            </a>
        </div>
    </div>
    </div>

</div>

@endsection