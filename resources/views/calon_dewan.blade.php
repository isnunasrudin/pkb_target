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
    <div class="card-header py-3 d-flex justify-content-between">
        <h6 class="m-0 font-weight-bold text-primary">Calon Dewan</h6>
        <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addCalonDewan"><i class="fas fa-plus"></i> Tambah Calon Dewan</button>

        <!-- Modal -->
        <div class="modal fade" id="addCalonDewan" tabindex="-1" aria-labelledby="addCalonDewan" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCalonDewan">Tambah Calon Dewan</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form action="{{ route('calon_dewan.store') }}" method="POST">
                            @csrf
                            <div class="form-group">
                                <label for="name">Nama</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="form-group">
                                <label for="order">No Urut</label>
                                <input type="number" class="form-control" id="order" name="order" required>
                            </div>
                            <div class="form-group">
                                <label for="dapil">Dapil</label>
                                <input type="text" class="form-control" id="dapil" name="dapil" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>



    </div>
    <div class="card-body">
        <table class="table table-bordered" id="dataTable">
            <thead>
                <tr>
                    <th style="width: 5%">#</th>
                    <th style="width: 10%">No Urut</th>
                    <th>Nama</th>
                    <th>Dapil</th>
                    <th>Total Suara</th>
                    <th style="width: 10%">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($calonDewan as $item)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $item->order }}</td>
                    <td>
                        {{ $item->name }}
                        @if($item->deleted_at)
                            <span class="badge badge-danger">Dihapus</span>
                        @endif
                    </td>
                    <td>{{ $item->dapil }}</td>
                    <td>{{ $item->suara_sum_total ?? 0 }}</td>
                    <td>
                        <button class="btn btn-warning btn-sm" data-toggle="modal" data-target="#edit{{ $item->id }}"><i class="fas fa-edit"></i></button>
                        @if($item->deleted_at)
                            <button class="btn btn-success btn-sm" onclick="restoreData({{ $item->id }})"><i class="fas fa-undo"></i></button>
                        @else
                            <button class="btn btn-danger btn-sm" onclick="hapusData({{ $item->id }})"><i class="fas fa-trash"></i></button>
                        @endif

                        <!-- Modal -->
                        <div class="modal fade" id="edit{{ $item->id }}" tabindex="-1" aria-labelledby="edit{{ $item->id }}" aria-hidden="true">
                        <form class="modal-dialog" action="{{ route('calon_dewan.update', $item->id) }}" method="POST">
                            <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="edit{{ $item->id }}">Edit Calon Dewan</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                    @csrf
                                    @method('PUT')
                                    <div class="form-group">
                                        <label for="name">Nama</label>
                                        <input type="text" class="form-control" id="name" name="name" value="{{ $item->name }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="order">No Urut</label>
                                        <input type="number" class="form-control" id="order" name="order" value="{{ $item->order }}">
                                    </div>
                                    <div class="form-group">
                                        <label for="dapil">Dapil</label>
                                        <input type="text" class="form-control" id="dapil" name="dapil" value="{{ $item->dapil }}">
                                    </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                                <button type="submit" class="btn btn-primary">Simpan</button>
                            </div>
                            </div>
                        </form>
                        </div>

                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>



@endsection

@push('scripts')



    <script>

        $('#dataTable').DataTable();

        function hapusData($id) {
            swal({
                title: "Yakin?",
                text: "Data akan dihapus!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        fetch('/calon_dewan/' + $id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                // swal("Terhapus!", "Data berhasil dihapus.", "success");
                                location.reload();
                            } else {
                                swal("Gagal!", "Terjadi kesalahan saat menghapus data.", "error");
                            }
                        })
                        .catch(error => {
                            swal("Gagal!", "Terjadi kesalahan saat menghapus data.", "error");
                        });
                    }
            });
        }

        function restoreData($id) {
            swal({
                title: "Yakin?",
                text: "Data akan dikembalikan!",
                icon: "warning",
                buttons: true,
                dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        fetch('/calon_dewan/' + $id, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                        })
                        .then(response => {
                            if (response.ok) {
                                // swal("Terhapus!", "Data berhasil dihapus.", "success");
                                location.reload();
                            } else {
                                swal("Gagal!", "Terjadi kesalahan saat mengembalikan data.", "error");
                            }
                        })
                        .catch(error => {
                            swal("Gagal!", "Terjadi kesalahan saat mengembalikan data.", "error");
                        });
                    }
            });
        }
    </script>
@endpush

{{-- @push('scripts')
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
                scrollY: 400,
                ordering: false,
                searching: false,
            });
        });
    </script>
@endpush --}}
