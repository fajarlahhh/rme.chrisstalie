<div x-data="siteMarkingForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Site Marking')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Site Marking</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    <h1 class="page-header">Site Marking</h1>

    <x-alert />

    @if ($data)
        <div class="note alert-primary mb-2">
            <div class="note-content">
                <h5>Data Pasien</h5>
                <hr>
                <table class="w-100">
                    <tr>
                        <td class="w-200px">No. RM</td>
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
    @endif
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        @if ($data)
            <form wire:submit.prevent="submit">
                <div class="panel-body">
                    <div class="border p-3 mb-3">
                        <strong>Tindakan :</strong>
                        <ul>
                            @foreach ($data->tindakan as $row)
                                @if ($row->membutuhkan_sitemarking)
                                    <li>{{ $row->tarifTindakan->nama }} ({{ $row->qty }}x)</li>
                                @endif
                            @endforeach
                        </ul>

                    </div>
                    <div class="form-container">
                        <fieldset>
                            <div class="form-group">
                                <div class="interactive-diagram-container">
                                    <div class="overflow-auto">
                                        <canvas id="imgCanvas" width="550" height="700"></canvas>
                                    </div>
                                </div>
                            </div>
                            <table class="table">
                                @if ($marker)
                                    <tr>
                                        <th>Label</th>
                                        <th>Catatan</th>
                                    </tr>
                                    @foreach (json_decode($marker) as $key => $item)
                                        <tr>
                                            <td class="w-20px">{{ $item->label }}</td>
                                            <td>
                                                <input type="text" class="form-control"
                                                    wire:model="catatan.{{ $key }}">
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                            </table>
                        </fieldset>
                    </div>
                </div>
                <div class="panel-footer">
                    @role('administrator|supervisor|operator')
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Simpan
                        </button>
                    @endrole
                    <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/klinik/sitemarking'">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Data
                    </button>
                    @error('catatan')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
            </form>
        @else
            <div class="panel-body">
                <div class="row">
                    <div class="form-group">
                        <label class="form-label">Cari Data Registrasi</label>
                        <select class="form-control" x-init="$($el).selectpicker({
                            liveSearch: true,
                            width: 'auto',
                            size: 10,
                            container: 'body',
                            style: '',
                            showSubtext: true,
                            styleBase: 'form-control'
                        })" wire:model.live="registrasi_id"
                            data-width="100%" @if (isset($updating) && $updating === 'registrasi_id') disabled @endif
                            wire:loading.attr="disabled" wire:target="registrasi_id">
                            <option selected value="">-- Pilih Data Registrasi --</option>
                            @foreach ($dataRegistrasi as $row)
                                <option value="{{ $row->id }}">
                                    {{ $row->pasien_id }} - {{ $row->pasien->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('registrasi_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/sitemarking/data'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
            </div>
        @endif
    </div>
    <x-alert />
    @if ($data)
        <script>
            const imgCanvas = document.getElementById('imgCanvas');
            let frontCtx = null;
            if (imgCanvas) {
                frontCtx = imgCanvas.getContext('2d');
            }
            const markedLocationsInput = document.getElementById('marked_locations');

            // ===================================================================
            // PERUBAHAN DI SINI: Menggunakan path gambar lokal
            // ===================================================================
            const imgSrc = "{{ asset('assets/img/sitemarking.jpg') }}";

            let gambarImg = new Image();

            let markers = []; // Array untuk menyimpan semua marker {canvasId, x, y, label}

            // Fungsi untuk menggambar ulang canvas
            function drawCanvas(canvas, ctx, image, markersToDraw) {
                ctx.clearRect(0, 0, canvas.width, canvas.height); // Bersihkan canvas
                if (image.complete && image.naturalWidth > 0) {
                    ctx.drawImage(image, 0, 0, canvas.width, canvas.height); // Gambar ulang background
                } else {
                    // Jika gambar belum load atau error, tampilkan placeholder teks
                    ctx.fillStyle = '#666';
                    ctx.font = '14px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('Memuat gambar diagram...', canvas.width / 2, canvas.height / 2);
                }

                markersToDraw.forEach(marker => {
                    ctx.beginPath();
                    ctx.arc(marker.x, marker.y, 8, 0, Math.PI * 2, true); // Lingkaran
                    ctx.fillStyle = 'red';
                    ctx.fill();
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#003300';
                    ctx.stroke();

                    // Opsional: Tulis label marker
                    ctx.fillStyle = 'blue';
                    ctx.font = 'bold 12px Arial';
                    ctx.fillText(marker.label, marker.x, marker.y - 10);
                });
            }

            function addMarker(canvasId, event) {
                const rect = event.target.getBoundingClientRect();
                const scaleX = event.target.width / rect.width;
                const scaleY = event.target.height / rect.height;

                const x = (event.clientX - rect.left) * scaleX;
                const y = (event.clientY - rect.top) * scaleY;

                let label = `P${markers.length + 1}`;
                if (!label) label = `P${markers.length + 1}`;

                markers.push({
                    canvasId,
                    x: Math.round(x),
                    y: Math.round(y),
                    label
                });
                redrawAllCanvases();
                markers[markers.length - 1].catatan = '';
                @this.set('marker', JSON.stringify(markers));
            }

            function removeMarker(index) {
                markers.splice(index, 1);
                redrawAllCanvases();
            }

            function redrawAllCanvases() {
                drawCanvas(imgCanvas, frontCtx, gambarImg, markers.filter(m => m.canvasId === 'imgCanvas'));
            }

            gambarImg.onload = () => redrawAllCanvases();

            gambarImg.onerror = () => {
                console.error("Gambar body-front.png tidak ditemukan di folder 'images'.");
                alert(
                    "Error: Gagal memuat gambar diagram depan. Pastikan file 'body-front.png' ada di dalam folder 'images'."
                );
            }

            gambarImg.src = imgSrc;

            imgCanvas.addEventListener('click', (e) => addMarker('imgCanvas', e));
        </script>
    @endif
    @if (isset($data) && $data && $data->siteMarking && count($data->siteMarking) > 0)
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                let backendMarkers = @json(json_decode($marker));
                markers = backendMarkers;
                redrawAllCanvases();
            });
        </script>
    @endif
</div>
