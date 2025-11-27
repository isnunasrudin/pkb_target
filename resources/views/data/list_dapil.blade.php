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
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th rowspan="2" width="5%">No.</th>
                        <th rowspan="2">Nama Dewan</th>
                        <th colspan="{{ count($kecamatans) }}">Kecamatan</th>
                    </tr>
                    <tr>
                        @foreach($kecamatans as $kecamatan => $dapil)
                            <th>
                                <span class="d-block">{{ $kecamatan }}</span>
                                <a class="btn btn-sm btn-primary" href="{{ route('desa', $kecamatan) }}">Cek Desa</a>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calon_dewans as $dewan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dewan->name }}</td>
                            @foreach($kecamatans as $kecamatan => $dapil)
                                @php $poin = $data[$kecamatan][$dewan->id] ?? null; @endphp
                                <td>
                                    <div class="d-inline text-nowrap">
                                        <span class="badge badge-primary">{{ $poin }} Suara</span>
                                    </div>
                                    <div class="d-inline text-nowrap">
                                        <span class="badge badge-success">{{ $target[$kecamatan][$dewan->id] }} Target</span>
                                    </div>
                                    <div class="d-inline text-nowrap">
                                        <span class="badge badge-secondary">{{ $dpt[$kecamatan] }} DPT</span>
                                    </div>

                                    <form class="input-group mt-2 input-group-sm" style="width: 150px !important;" action="{{ route('sebar') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="kecamatan" value="{{ $kecamatan }}">
                                        <input type="hidden" name="calon" value="{{ $dewan->id }}">
                                        <input type="text" class="form-control" name="target" value="">
                                        <div class="input-group-append">
                                            <button class="btn btn-success" type="submit">Set Target</button>
                                        </div>
                                    </form>

                                    <form action="{{ route('reset_sebaran') }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="kecamatan" value="{{ $kecamatan }}">
                                        <input type="hidden" name="calon" value="{{ $dewan->id }}">
                                        <button type="submit" class="btn btn-danger btn-sm mt-2">Reset Target</button>
                                    </form>
                                    
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection