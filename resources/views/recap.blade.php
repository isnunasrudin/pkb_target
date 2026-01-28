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
        <h6 class="m-0 font-weight-bold text-primary">Hasil Kalkulasi Target 2029</h6>
    </div>

    <div class="card-body">
        <button id="export" class="btn btn-success btn-lg">Unduh Rekap Target (.xlsx)</button>
        <div class="alert alert-info mt-2">
            Hasil dari rekapitulasi merupakan perhitungan dari data 2024 dan target 2029. Juga sudah disesuaikan dengan template yang diminta Pusat.
        </div>
    </div>

</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-info">Daftar Pemilih Tetap (DPT) 2024</h6>
    </div>

    <div class="card-body">
        <a href="{{ route('recap.dpt') }}" class="btn btn-info btn-lg">Unduh Rekap DPT (.xlsx)</a>
        <div class="alert alert-info mt-2">
            Rekapitulasi terdiri dari DPT Dapil, DPT Kecamatan, dan DPT Desa.
        </div>
    </div>

</div>

<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-danger">Suara Partai 2024</h6>
    </div>

    <div class="card-body">
        <a href="{{ route('recap.partai') }}" class="btn btn-danger btn-lg">Unduh Rekap Partai (.xlsx)</a>
        <div class="alert alert-danger mt-2">
            Rekapitulasi terdiri dari Partai DPT Dapil, DPT Kecamatan, dan DPT Desa.
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