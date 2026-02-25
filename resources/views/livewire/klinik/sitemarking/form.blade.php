<div x-data="siteMarkingForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Site Marking')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Site Marking</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    <h1 class="page-header">Site Marking</h1>

    @include('livewire.klinik.informasipasien', ['data' => $data])

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
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
                                    <canvas id="imgCanvas" width="550" height="700"
                                        @click="addMarker($event)"></canvas>
                                </div>
                            </div>
                        </div>
                        <table class="table">
                            <template x-if="markers.length > 0">
                                <tbody>
                                    <tr>
                                        <th>Label</th>
                                        <th>Catatan</th>
                                        <th></th>
                                    </tr>
                                    <template x-for="(marker, index) in markers" :key="index">
                                        <tr>
                                            <td class="w-20px" x-text="marker.label"></td>
                                            <td>
                                                <input type="text" class="form-control" x-model="marker.catatan">
                                            </td>
                                            <td class="w-10px align-middle">
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    @click="removeMarker(index)">
                                                    X
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </template>
                        </table>
                    </fieldset>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endrole
                <button type="button" class="btn btn-secondary m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/sitemarking'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
                <x-alert />
            </div>

            <x-modal.konfirmasi />
        </form>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>

@push('scripts')
    <script>
        function siteMarkingForm() {
            return {
                markers: @js($marker ? json_decode($marker) : []),
                imgCanvas: null,
                frontCtx: null,
                gambarImg: null,
                imgSrc: "{{ asset('assets/img/sitemarking.jpg') }}",

                init() {
                    this.$nextTick(() => {
                        this.imgCanvas = document.getElementById('imgCanvas');
                        if (this.imgCanvas) {
                            this.frontCtx = this.imgCanvas.getContext('2d');
                        }

                        this.gambarImg = new Image();
                        this.gambarImg.onload = () => this.redrawCanvas();
                        this.gambarImg.onerror = () => {
                            console.error("Gambar sitemarking.jpg tidak ditemukan.");
                            alert(
                                "Error: Gagal memuat gambar diagram. Pastikan file 'sitemarking.jpg' ada di dalam folder 'assets/img'."
                                );
                        };
                        this.gambarImg.src = this.imgSrc;

                        // Initialize existing markers
                        if (this.markers && this.markers.length > 0) {
                            this.markers.forEach(marker => {
                                if (!marker.catatan) marker.catatan = '';
                            });
                        }
                    });
                },

                addMarker(event) {
                    const rect = event.target.getBoundingClientRect();
                    const scaleX = event.target.width / rect.width;
                    const scaleY = event.target.height / rect.height;

                    const x = (event.clientX - rect.left) * scaleX;
                    const y = (event.clientY - rect.top) * scaleY;

                    let label = `P${this.markers.length + 1}`;

                    this.markers.push({
                        canvasId: 'imgCanvas',
                        x: Math.round(x),
                        y: Math.round(y),
                        label: label,
                        catatan: ''
                    });

                    this.redrawCanvas();
                },

                removeMarker(index) {
                    this.markers.splice(index, 1);
                    // Update labels for remaining markers
                    this.markers.forEach((marker, idx) => {
                        marker.label = `P${idx + 1}`;
                    });
                    this.redrawCanvas();
                },

                redrawCanvas() {
                    if (!this.frontCtx || !this.imgCanvas) return;

                    this.frontCtx.clearRect(0, 0, this.imgCanvas.width, this.imgCanvas.height);

                    if (this.gambarImg && this.gambarImg.complete && this.gambarImg.naturalWidth > 0) {
                        this.frontCtx.drawImage(this.gambarImg, 0, 0, this.imgCanvas.width, this.imgCanvas.height);
                    } else {
                        this.frontCtx.fillStyle = '#666';
                        this.frontCtx.font = '14px Arial';
                        this.frontCtx.textAlign = 'center';
                        this.frontCtx.fillText('Memuat gambar diagram...', this.imgCanvas.width / 2, this.imgCanvas.height /
                            2);
                    }

                    this.markers.forEach(marker => {
                        this.frontCtx.beginPath();
                        this.frontCtx.arc(marker.x, marker.y, 8, 0, Math.PI * 2, true);
                        this.frontCtx.fillStyle = 'red';
                        this.frontCtx.fill();
                        this.frontCtx.lineWidth = 2;
                        this.frontCtx.strokeStyle = '#003300';
                        this.frontCtx.stroke();

                        this.frontCtx.fillStyle = 'blue';
                        this.frontCtx.font = 'bold 12px Arial';
                        this.frontCtx.fillText(marker.label, marker.x, marker.y - 10);
                    });
                },

                syncToLivewire() {
                    // Prepare catatan object for Livewire
                    let catatanObj = {};
                    this.markers.forEach((marker, index) => {
                        catatanObj[marker.label] = marker.catatan || '';
                    });

                    // Sync data to Livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('marker', JSON.stringify(this.markers), false);
                                $wire.set('siteMarking', catatanObj, false);
                            }
                        }
                    }
                }
            }
        }
    </script>
@endpush
