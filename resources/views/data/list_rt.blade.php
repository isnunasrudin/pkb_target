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
        <div class="table-responsive" style="">
            <table class="table table-bordered" cellspacing="0" class="w-100">
                <thead>
                    <tr>
                        <th rowspan="2" width="5%">No.</th>
                        <th rowspan="2" width="100%">Nama Dewan</th>
                        <th colspan="{{ $daftar_rt->count() }}">RT/RW</th>
                    </tr>
                    <tr>
                        @foreach($daftar_rt as $rt)
                            <th>
                                {{ $rt->rt }}/{{ $rt->rw }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($calon_dewans as $dewan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $dewan->name }}</td>
                            @foreach($daftar_rt as $rt)
                                <td>
                                    {{-- <div class="progress mb-2" style="width: 100%">
                                        <div class="progress-bar bg-primary" 
                                            style="width: 20%" 
                                            aria-valuenow="20" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                        <div class="progress-bar bg-success" 
                                            style="width: 40%" 
                                            aria-valuenow="40" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div> --}}
                                    {{-- </div> --}}
                                    <div class="d-block text-nowrap">
                                        <span class="badge badge-primary">{{ $data[$rt->id][$dewan->id] }} Suara</span>
                                    </div>
                                    <div class="d-block text-nowrap">
                                        <span class="badge badge-success">{{ $target[$rt->id][$dewan->id] }} Target</span>
                                    </div>
                                    <div class="d-block text-nowrap">
                                        <span class="badge badge-secondary">{{ $dpt[$rt->id] }} DPT</span>
                                    </div>
                                {{-- <td class="{{ $poin !== null ? 'text-success' : 'text-danger' }}"> --}}
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

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" ></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.5/js/dataTables.fixedColumns.js" ></script>
<script src="https://cdn.datatables.net/fixedcolumns/5.0.5/js/fixedColumns.bootstrap4.js" ></script>
<script>
    console.log($('table'))
</script>
@endpush