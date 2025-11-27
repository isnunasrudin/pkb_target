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
                        <th colspan="{{ $daftar_desa->count() }}">Desa</th>
                    </tr>
                    <tr>
                        @foreach($daftar_desa as $desa)
                            <th>
                                <span class="d-block">{{ $desa->desa }}</span>
                                <div class="d-flex">
                                    <a class="btn btn-sm btn-primary" href="{{ route('tps', $desa->id) }}">TPS</a>
                                    <a class="btn btn-sm btn-success ml-2" href="{{ route('rt', $desa->id) }}">RT</a>
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calon_dewans as $dewan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dewan->name }}</td>
                            @foreach($daftar_desa as $desa)
                                @php $poin = $data[$desa->id][$dewan->id] ?? null @endphp
                                <td>
                                    <div class="d-block text-nowrap">
                                        <span class="badge badge-primary">{{ $data[$desa->id][$dewan->id] }} Suara</span>
                                    </div>
                                    <div class="d-block text-nowrap">
                                        <span class="badge badge-success">{{ $target[$desa->id][$dewan->id] }} Target</span>
                                    </div>
                                    <div class="d-block text-nowrap">
                                        <span class="badge badge-secondary">{{ $dpt[$desa->id] }} DPT</span>
                                    </div>
                                {{-- <td class="{{ $poin !== null ? 'text-success' : 'text-danger' }}"> --}}
                                    {{-- {{ $poin ?? '-' }} --}}
                                    {{-- @if($poin !== null)
                                    <a href="{{ route('desa', $desa->kecamatan) }}">{{ $poin }}</a>
                                    @else
                                    -
                                    @endif --}}
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