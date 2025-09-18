<div>
    @section('title', 'Site Marking')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Site Marking</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    <h1 class="page-header">Site Marking</h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->

        @if ($data->exists)
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <form wire:submit.prevent="submit">
                <div class="panel-body">
                    <div class="form-container">
                        <h1>Formulir Penandaan Lokasi Operasi (Site Marking)</h1>
                        <form action="/submit-site-marking" method="post">


                            <fieldset>
                                <div class="form-group">
                                    <label>Tandai Lokasi Prosedur pada Diagram Tubuh</label>
                                    <div class="interactive-diagram-container">
                                        <div class="diagram-wrapper">
                                            <canvas id="frontCanvas" width="350" height="500"></canvas>
                                            <div class="diagram-label">Diagram Tubuh Depan</div>
                                        </div>
                                        <div class="diagram-wrapper">
                                            <canvas id="backCanvas" width="350" height="500"></canvas>
                                            <div class="diagram-label">Diagram Tubuh Belakang</div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="marked_locations" name="marked_locations">
                                    <label for="marked_locations_display">Daftar Lokasi Ditandai:</label>
                                    <ul id="marked_locations_display" class="marker-list">
                                    </ul>
                                </div>

                                <div class="form-group">
                                    <label for="catatan_diagram">Catatan Tambahan Lokasi Penandaan</label>
                                    <textarea id="catatan_diagram" name="catatan_diagram"
                                        placeholder="Deskripsikan lokasi penandaan secara lebih spesifik, contoh: 'X' pada lutut kanan lateral, 'O' pada pergelangan tangan kiri."></textarea>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                </div>
                <div class="panel-footer" wire:loading.remove>
                    @role('administrator|supervisor|operator')
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                            Simpan
                        </button>
                    @endrole
                    <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/klinik/registrasi/data'">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Data
                    </button>
                    <button type="button" class="btn btn-secondary m-r-3"
                        onclick="window.location.href='/klinik/registrasi'" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Reset
                    </button>
                </div>
            </form>
        @else
            <div class="panel-body">
                <div class="row">
                    <div class="mb-3 position-relative">
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
                        <div style="position: absolute; right: 20px; top: 33px; z-index: 10;">
                            <span wire:loading wire:target="registrasi_id">
                                <span class="spinner-border spinner-border-sm text-primary" role="status"
                                    aria-hidden="true"></span>
                            </span>
                        </div>
                        @error('registrasi_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        @endif
    </div>
    <script>
        const frontCanvas = document.getElementById('frontCanvas');
        const backCanvas = document.getElementById('backCanvas');
        const frontCtx = frontCanvas.getContext('2d');
        const backCtx = backCanvas.getContext('2d');
        const markedLocationsInput = document.getElementById('marked_locations');
        const markedLocationsDisplay = document.getElementById('marked_locations_display');

        // ===================================================================
        // PERUBAHAN DI SINI: Menggunakan path gambar lokal
        // ===================================================================
        const frontBodyImgSrc = 'images/body-front.png';
        const backBodyImgSrc = 'images/body-back.png';

        let frontBodyImg = new Image();
        let backBodyImg = new Image();

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

            // Gambar semua marker yang relevan untuk canvas ini
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

        // Fungsi untuk mengupdate daftar marker yang ditampilkan di UI
        function updateMarkersDisplay() {
            markedLocationsDisplay.innerHTML = ''; // Bersihkan daftar
            if (markers.length === 0) {
                const li = document.createElement('li');
                li.style.backgroundColor = '#f8f9fa';
                li.style.textAlign = 'center';
                li.style.color = '#6c757d';
                li.textContent = 'Belum ada lokasi yang ditandai.';
                markedLocationsDisplay.appendChild(li);
            } else {
                markers.forEach((marker, index) => {
                    const li = document.createElement('li');
                    li.textContent =
                        `(${marker.canvasId === 'frontCanvas' ? 'Depan' : 'Belakang'}) Lokasi: X${marker.x}, Y${marker.y} - Penanda: ${marker.label}`;
                    const removeBtn = document.createElement('button');
                    removeBtn.textContent = 'Hapus';
                    removeBtn.onclick = () => removeMarker(index);
                    li.appendChild(removeBtn);
                    markedLocationsDisplay.appendChild(li);
                });
            }
            // Update input hidden untuk disubmit
            markedLocationsInput.value = JSON.stringify(markers);
        }

        // Fungsi untuk menambahkan marker
        function addMarker(canvasId, event) {
            const rect = event.target.getBoundingClientRect();
            const scaleX = event.target.width / rect.width; // Hitung rasio skala
            const scaleY = event.target.height / rect.height; // Hitung rasio skala

            // Dapatkan koordinat X dan Y yang diskalakan ke ukuran canvas internal
            const x = (event.clientX - rect.left) * scaleX;
            const y = (event.clientY - rect.top) * scaleY;

            // Prompt untuk label marker (bisa diganti dengan custom modal/input lebih baik)
            let label = prompt("Masukkan label untuk penanda ini (cth: 'Kiri Atas', 'X', 'Operasi') :",
                `P${markers.length + 1}`);
            if (!label) label = `P${markers.length + 1}`; // Default label jika kosong

            markers.push({
                canvasId,
                x: Math.round(x),
                y: Math.round(y),
                label
            });

            redrawAllCanvases();
            updateMarkersDisplay();
        }

        // Fungsi untuk menghapus marker
        function removeMarker(index) {
            markers.splice(index, 1);
            redrawAllCanvases();
            updateMarkersDisplay();
        }

        // Fungsi untuk menggambar ulang semua canvas
        function redrawAllCanvases() {
            drawCanvas(frontCanvas, frontCtx, frontBodyImg, markers.filter(m => m.canvasId === 'frontCanvas'));
            drawCanvas(backCanvas, backCtx, backBodyImg, markers.filter(m => m.canvasId === 'backCanvas'));
        }

        // Muat gambar dan gambar canvas awal
        frontBodyImg.onload = () => redrawAllCanvases();
        backBodyImg.onload = () => redrawAllCanvases();

        // Menambahkan penanganan error jika gambar tidak ditemukan
        frontBodyImg.onerror = () => {
            console.error("Gambar body-front.png tidak ditemukan di folder 'images'.");
            alert(
                "Error: Gagal memuat gambar diagram depan. Pastikan file 'body-front.png' ada di dalam folder 'images'.");
        }
        backBodyImg.onerror = () => {
            console.error("Gambar body-back.png tidak ditemukan di folder 'images'.");
            alert(
                "Error: Gagal memuat gambar diagram belakang. Pastikan file 'body-back.png' ada di dalam folder 'images'.");
        }

        frontBodyImg.src = frontBodyImgSrc;
        backBodyImg.src = backBodyImgSrc;

        // Tambahkan event listener untuk klik pada canvas
        frontCanvas.addEventListener('click', (e) => addMarker('frontCanvas', e));
        backCanvas.addEventListener('click', (e) => addMarker('backCanvas', e));

        // Inisialisasi tampilan marker saat pertama kali load
        updateMarkersDisplay();
    </script>
</div>
