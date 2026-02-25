<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>RM</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Tanggal Lahir</th>
            <th>Jenis Kelamin</th>
            <th>Alamat</th>
            <th>No. Telp.</th>
            <th>Saldo</th>
            <th>Poin</th>
            <th>Level</th>
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
                <td>{{ $row->pasien->nama }}</td>
                <td>{{ $row->email }} 
                    @if ($row->verified_at)
                        <span class="badge bg-success"><i class="fa fa-check"></i></span>
                    @else
                        <span class="badge bg-warning"><i class="fa fa-times"></i></span>
                    @endif
                </td>
                <td>{{ $row->pasien->tanggal_lahir }}</td>
                <td>{{ $row->pasien->jenis_kelamin }}</td>
                <td>{{ $row->pasien->alamat }}</td>
                <td>{{ $row->pasien->no_hp }}</td>
                <td class="text-end">{{ number_format($row->saldo) }}</td>
                <td class="text-end">{{ number_format($row->poin ?? 0) }}</td>
                <td><x-level :level="$row->level" /></td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor|operator')
                            <x-action :row="$row" custom="" :detail="false" :edit="true" :print="false"
                                :permanentdelete="false" :restore="false" :delete="false" />
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
