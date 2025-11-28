@extends('layouts.admin')

@section('main-content')

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
        <h6 class="m-0 font-weight-bold text-primary">Kecamatan Pada DAPIL {{$dapil}}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th rowspan="2">No.</th>
                        <th rowspan="2">Nama Dewan</th>
                        <th colspan="{{ count($kecamatans) }}">Kecamatan</th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        @foreach($kecamatans as $kecamatan => $dapil)
                            <th>
                                <a href="{{ route('desa', $kecamatan) }}" style="white-space: nowrap;">
                                    <span class="d-block">{{ $kecamatan }} <i class="fas fa-external-link-alt"></i></span>
                                </a>
                                <span class="badge badge-secondary">{{ number_format($dpt[$kecamatan], 0, ',', '.') }} DPT</span>
                                <div class="d-flex">
                                    {{-- <a class="btn btn-sm btn-primary" href="{{ route('tps', $desa->id) }}">TPS</a> --}}
                                    {{-- <a class="btn btn-sm btn-success ml-2" href="{{ route('rt', $desa->id) }}">RT</a> --}}
                                </div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calon_dewans as $dewan)
                        @php
                        $total_baris_ini = [
                            'suara' => 0,
                            'target' => 0,
                            'kurang' => 0,
                        ];
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dewan->name }}</td>
                            @foreach($kecamatans as $kecamatan => $dapil)
                                @php $poin = $data[$kecamatan][$dewan->id] ?? null;
                                $total_baris_ini['suara'] += $poin;
                                $total_baris_ini['target'] += $target[$kecamatan][$dewan->id];
                                $total_baris_ini['kurang'] += $target[$kecamatan][$dewan->id] - $poin;
                                @endphp
                                <td>
                                    <div class="d-flex">

                                        <div class="progress vertical my-auto" style="height: 40px">
                                            <div class="progress-bar bg-primary" 
                                                style="height: {{ $poin / $dpt[$kecamatan] * 100 }}%">
                                            </div>
                                            <div class="progress-bar bg-success" 
                                                style="height: {{ ($target[$kecamatan][$dewan->id] - $poin) / $dpt[$kecamatan] * 100 }}%">
                                            </div>
                                        </div>

                                        <div class="d-inline-flex flex-column mr-2">

                                            <div class="d-block text-nowrap">
                                                <span class="badge badge-success">{{ number_format($target[$kecamatan][$dewan->id], 0, ',', '.') }} Target</span>
                                            </div>
                                            <div class="d-block text-nowrap">
                                                <span class="badge badge-primary">{{ number_format($data[$kecamatan][$dewan->id], 0, ',', '.') }} Suara</span>
                                            </div>

                                            @if($target[$kecamatan][$dewan->id] > $poin)
                                            <div class="d-block text-nowrap">
                                                <span class="badge badge-danger">{{ number_format($target[$kecamatan][$dewan->id] - $poin, 0, ',', '.') }} Kurang</span>
                                            </div>
                                            @endif

                                        </div>
                                        
                                        <form class="input-group input-group-sm my-auto mr-2" style="width: 150px !important;" action="{{ route('sebar') }}" method="POST">
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
                                    </div>
                                </td>
                            @endforeach
                            <td>
                                <div class="d-block text-nowrap mb-1">
                                    <span class="badge badge-success" style="font-size: 100%">{{ number_format($total_baris_ini['target'], 0, ',', '.') }} Target</span>
                                </div>
                                <div class="d-block text-nowrap mb-1">
                                    <span class="badge badge-primary" style="font-size: 100%">{{ number_format($total_baris_ini['suara'], 0, ',', '.') }} Suara</span>
                                </div>

                                @if($total_baris_ini['target'] > $total_baris_ini['suara'])
                                <div class="d-block text-nowrap mb-1">
                                    <span class="badge badge-danger" style="font-size: 100%">{{ number_format($total_baris_ini['target'] - $total_baris_ini['suara'], 0, ',', '.') }} Kurang</span>
                                </div>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $('#dataTable').DataTable({
                fixedColumns: {
                    start: 2,
                    end: 1,
                },
                paging: false,
                scrollCollapse: true,
                scrollX: true,
                scrollY: 600,
                ordering: false,
                searching: false,
            });
        });
    </script>
@endpush
