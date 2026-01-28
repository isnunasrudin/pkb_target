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

<div class="row">
    <div class="col-lg-6">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Hasil Kalkulasi Target 2029</h6>
            </div>

            <div class="card-body">
                <div class="alert alert-info mb-2">
                    Hasil dari rekapitulasi merupakan perhitungan dari data 2024 dan target 2029. Juga sudah disesuaikan dengan template yang diminta Pusat.
                </div>
                <button id="export" class="btn btn-success btn-lg">Unduh Rekap Target (.xlsx)</button>
            </div>

        </div>
    </div>
    <div class="col-lg-6">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-info">Daftar Pemilih Tetap (DPT) 2024</h6>
            </div>

            <div class="card-body">
                <div class="alert alert-info mb-2">
                    Rekapitulasi terdiri dari DPT Dapil, DPT Kecamatan, dan DPT Desa.
                </div>
                <a href="{{ route('recap.dpt') }}" class="btn btn-info btn-lg">Unduh Rekap DPT (.xlsx)</a>
            </div>

        </div>
    </div>
    <div class="col-lg-4">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Suara Partai 2024</h6>
            </div>

            <div class="card-body">
                <div class="alert alert-danger mb-2">
                    Rekapitulasi terdiri dari Partai DPT Dapil, DPT Kecamatan, dan DPT Desa.
                </div>
                <a href="{{ route('recap.partai') }}" class="btn btn-danger btn-lg">Unduh Rekap Partai (.xlsx)</a>
            </div>
        </div>
    </div>
    <div class="col-lg-4">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Suara Caleg 2024</h6>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('recap') }}">
                    @csrf
                    <button class="btn btn-danger btn-lg">Unduh Rekap Semua Dapil (.xlsx)</button>
                </form>
            </div>

        </div>
    </div>

    <div class="col-lg-4">

        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-danger">Suara Caleg Per Kecamatan 2024</h6>
            </div>

            <div class="card-body">
                <form method="POST" action="{{ route('recap.kecamatan') }}">
                    @csrf
                    <div class="form-group mb-2">
                        <label for="exampleFormControlSelect2">Pilih Kecamatan</label>
                        <select class="form-control" id="exampleFormControlSelect1" name="kecamatan">
                            @foreach (config('kecamatan.all') as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button class="btn btn-danger btn-lg">Unduh Rekap</button>
                </form>
            </div>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    $(document).ready(function() {
        $('#export').click(function(e) {
            e.preventDefault()
            Swal.fire({
                title: 'Export Diproses',
                text: "Memerlukan waktu beberapa menit",
                icon: 'info',
                allowOutsideClick: false,
                allowEscapeKey: false,
                didOpen: () => {
                    Swal.showLoading()
                    $.ajax({
                        url: "{{ route('export') }}",
                        type: 'GET',
                        success: function(response) {
                            Swal.close()
                            window.location.href = response.data.url
                        },
                        error: function(response) {
                            Swal.close()
                            Swal.fire({
                                title: 'Error',
                                text: response.responseJSON.message,
                                icon: 'error',
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            })
                        }
                    })
                }
            })
        });
    });
</script>
@endpush