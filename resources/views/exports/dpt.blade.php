<table>
    <thead>
        <tr>
            @foreach ($headers as $header)
            <th style="border: 1px solid black; font-weight: bold; text-align: center;">{{ $header }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
        <tr>
            @foreach ($item as $value)
            <td style="border: 1px solid black;">{{ $value }}</td>
            @endforeach
        </tr>
        @endforeach
    </tbody>
</table>