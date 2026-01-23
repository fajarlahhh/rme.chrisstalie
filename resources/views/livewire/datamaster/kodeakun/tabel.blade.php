<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>Kode</th>
            <th>Nama</th>
            <th>Kategori</th>
            <th>Parent</th>
            <th>Detail</th>
            @if ($cetak == false)
                <th></th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr>
                <td>{{ $cetak == false ? ($data->currentPage() - 1) * $data->perPage() + $loop->iteration : $loop->iteration }}
                </td>
                <td>{{ $item->id }}</td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->kategori }}</td>
                <td>{{ $item->parent_id }}</td>
                <td>{{ $item->detail ? 'Ya' : 'Tidak' }}</td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor')
                            {{-- @if ($item->sistem != 1) --}}
                            <x-action :row="$item" custom="" :detail="false" :edit="true" :print="false"
                                :permanentDelete="false" :restore="false" :delete="true" />
                            {{-- @endif --}}
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
