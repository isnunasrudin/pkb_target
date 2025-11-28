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
        <h6 class="m-0 font-weight-bold text-primary">Data Desa {{ $address->desa }} | Kecamatan {{ $address->kecamatan }}</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive" style="">
            <table class="table table-bordered table-striped " id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th rowspan="2" width="5%">No.</th>
                        <th rowspan="2" width="100%">Nama Dewan</th>
                        <th colspan="{{ $daftar_rt->count() }}">
                            RT/RW
                        </th>
                        <th rowspan="2">Total</th>
                    </tr>
                    <tr>
                        @foreach($daftar_rt as $rt)
                            <th>
                                <span style="white-space: nowrap" class="{{ in_array($rt->rt, $daftar_rt_yang_duplikat) ? 'text-danger' : '' }}">RT {{ $rt->rt }} <sup>RW {{ $rt->rw }}</sup></span>
                                <span class="badge {{ in_array($rt->id, $rt_dpt_invalid) ? 'badge-danger' : 'badge-secondary' }}">{{ number_format($dpt[$rt->id], 0, ',', '.') }} DPT</span>
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
                            <td><span style="width: 200px" class="d-inline-block">{{ $dewan->name }}</span></td>
                            @foreach($daftar_rt as $rt)
                                @php $poin = $data[$rt->id][$dewan->id] ?? null;
                                    $total_baris_ini['suara'] += $poin;
                                    $total_baris_ini['target'] += $target[$rt->id][$dewan->id];
                                    $total_baris_ini['kurang'] += $target[$rt->id][$dewan->id] - $poin;
                                @endphp
                                <td>
                                    <div class="d-flex">

                                    <div class="progress vertical my-auto mr-2" style="height: 40px">
                                        <div class="progress-bar bg-primary" 
                                            style="height: {{ $poin / $dpt[$rt->id] * 100 }}%">
                                        </div>
                                        <div class="progress-bar bg-success" 
                                            style="height: {{ ($target[$rt->id][$dewan->id] - $poin) / $dpt[$rt->id] * 100 }}%">
                                        </div>
                                    </div>

                                    <div style="width: 50px" class="d-inline-flex flex-column">

                                        <div class="d-block text-nowrap">
                                            <span class="badge badge-success">{{ number_format($target[$rt->id][$dewan->id], 0, ',', '.') }} Target</span>
                                        </div>
                                        <div class="d-block text-nowrap">
                                            <span class="badge badge-primary">{{ number_format($data[$rt->id][$dewan->id], 0, ',', '.') }} Suara</span>
                                        </div>

                                        @if($target[$rt->id][$dewan->id] > $poin)
                                        <div class="d-block text-nowrap">
                                            <span class="badge badge-danger">{{ number_format($target[$rt->id][$dewan->id] - $poin, 0, ',', '.') }} Kurang</span>
                                        </div>
                                        @endif

                                    </div>
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
                                <div class="d-block text-nowrap">
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
