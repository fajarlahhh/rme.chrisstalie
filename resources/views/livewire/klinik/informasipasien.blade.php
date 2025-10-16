<div class="note alert-primary mb-2">
    <div class="note-content">
        <h5>Data Pasien</h5>
        <hr>
        <table class="w-100">
            <tr>
                <td class="w-150px">No. Registrasi</td>
                <td class="w-10px">:</td>
                <td>{{ $data->id }}</td>
            </tr>
            <tr>
                <td class="w-150px">No. RM</td>
                <td class="w-10px">:</td>
                <td>{{ $data->pasien_id }}</td>
            </tr>
            <tr>
                <td>Nama</td>
                <td class="w-10px">:</td>
                <td>{{ $data->pasien->nama }}</td>
            </tr>
            <tr>
                <td>Usia</td>
                <td class="w-10px">:</td>
                <td>{{ $data->pasien->umur }} Tahun</td>
            </tr>
            <tr>
                <td>Jenis Kelamin</td>
                <td class="w-10px">:</td>
                <td>{{ $data->pasien->jenis_kelamin }}</td>
            </tr>
        </table>
    </div>
</div>
