<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>Kode</th>
            <th>Uraian</th>
            @if ($cetak == false)
                <th></th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr>
                <td>{{ $cetak == false ? ($data->currentPage() - 1) * $data->perPage() + $loop->iteration : $loop->iteration }}</td>
                <td>{{ $item->id }}</td>
                <td>{{ $item->uraian }}</td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor')
                            <x-action :row="$item" custom="" :detail="false" :edit="true"
                                :print="false" :permanentdelete="false" :restore="false" :delete="true" />
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
