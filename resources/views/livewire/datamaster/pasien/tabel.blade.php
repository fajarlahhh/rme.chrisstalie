<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>RM</th>
            <th>IHS</th>
            <th>Nama</th>
            <th>No. KTP</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>Alamat</th>
            <th>No. Telp.</th>
            <th>Tanggal Daftar</th>
            @if ($cetak == false)
                <th class="w-10px"></th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $row)
            <tr>
                <td>
                    {{ $cetak == false ? ($data->currentPage() - 1) * $data->perPage() + $loop->iteration : $loop->iteration }}
                </td>
                <td>{{ $row->id }}</td>
                <td>{{ $row->ihs }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->nik }}</td>
                <td>{{ $row->tanggal_lahir }}</td>
                <td>{{ $row->jenis_kelamin }}</td>
                <td>{{ $row->alamat }}</td>
                <td>{{ $row->no_hp }}</td>
                <td>{{ $row->tanggal_daftar }}</td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor|operator')
                            @if ($row->pembayaran->count() > 0)
                                <x-action :row="$row" custom="" :detail="false" :edit="true"
                                    :print="false" :permanentdelete="false" :restore="false" :delete="false" />
                            @else
                                <x-action :row="$row" custom="" :detail="false" :edit="true"
                                    :print="false" :permanentdelete="false" :restore="false" :delete="true" />
                            @endif
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
