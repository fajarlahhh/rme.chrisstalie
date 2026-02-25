@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Pegawai</h5>
        <hr>
    </div>
@endif
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>ID</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Tanggal Lahir</th>
            <th>Alamat</th>
            <th>No. Hp</th>
            <th>No. BPJS</th>
            <th>Tanggal Masuk</th>
            <th>Satuan Tugas</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 0;
        @endphp
        @foreach ($data as $key => $row)
            <tr>
                <td nowrap>{{ ++$no }}</td>
                <td nowrap>{{ $row['id'] }}</td>
                <td nowrap>{{ $row['nik'] }}</td>
                <td nowrap>{{ $row['nama'] }}</td> 
                <td nowrap>{{ $row['tanggal_lahir'] }}</td>
                <td nowrap>{{ $row['alamat'] }}</td>
                <td nowrap>{{ $row['no_hp'] }}</td>
                <td nowrap>{{ $row['no_bpjs'] }}</td>
                <td nowrap>{{ $row['tanggal_masuk'] }}</td>
                <td nowrap>{{ $row['satuan_tugas'] }}</td>
                <td nowrap>{{ $row['status'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
