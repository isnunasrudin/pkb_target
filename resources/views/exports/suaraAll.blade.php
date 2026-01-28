<table>
    <thead>
        <tr>
            <th rowspan="2" style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">No.</th>
            <th rowspan="2" style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">Nama Dewan</th>
            <th colspan="{{ $kecamatans->count() }}" style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">DAPIL {{ $dapil }}</th>
            <th rowspan="2" style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">Total</th>
        </tr>
        <tr>
            @foreach($kecamatans->keys() as $kecamatan)
            <th style="background-color: #f2f2f2; border: 1px solid #000000; font-weight: bold; text-align: center; vertical-align: center;">
                {{ strtoupper($kecamatan) }}
            </th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($calon_dewans as $dewan)
        @php
        $total_target = 0;
        $total_suara = 0;
        @endphp
        <tr>
            <td style="border: 1px solid #000000; text-align: center;">{{ $loop->iteration }}</td>
            <td style="border: 1px solid #000000;">{{ $dewan->name }}</td>

            @foreach($kecamatans->keys() as $kecamatan)
            @php
            $suara = $data[$kecamatan][$dewan->id] ?? 0;
            $target_desa = $target[$kecamatan][$dewan->id] ?? 0;
            $selisih = $target_desa - $suara;

            $total_target += $target_desa;
            $total_suara += $suara;
            @endphp
            <td style="border: 1px solid #000000; text-align: right;">{{ $suara }}</td>
            @endforeach

            <td style="border: 1px solid #000000; text-align: right; font-weight: bold;">{{ $total_suara }}</td>
        </tr>
        @endforeach
    </tbody>
</table>