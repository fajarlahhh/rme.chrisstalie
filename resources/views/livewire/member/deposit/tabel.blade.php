<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>Tanggal</th>
            <th>ID Member</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Metode Bayar</th>
            <th>Jumlah</th>
            <th class="w-10px"></th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $row)
            <tr>
                <td>
                    {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                </td>
                <td>{{ $row->created_at }}</td>
                <td>{{ $row->member_id }}</td>
                <td>{{ $row->member->pasien->nama }}</td>
                <td>{{ $row->member->email }}</td>
                <td>{{ $row->metode_bayar }}</td>
                <td>{{ $cetak ? $row->masuk : number_format($row->masuk) }}</td>
                <td class="with-btn-group text-end" nowrap>
                    @role('administrator|supervisor')
                        <x-action :row="$row" custom="" :detail="false" :edit="false" :print="false"
                            :permanentdelete="false" :restore="false" :delete="true" />
                    @endrole
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
