<div>
    <div wire:ignore.self class="modal fade" id="modal-cetak">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ $judul }}</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
                </div>
                <div class="modal-body h-500px overflow-auto" id="modal-body-cetak">
                    {!! Session::get('cetak') !!}
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" x-init="$($el).on('click', function() {
                        cetak();
                    })" class="btn btn-primary">Cetak</a>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        @if (Session::has('cetak'))
            <script>
                document.addEventListener('livewire:initialized', () => {
                    setTimeout(() => {
                        $('#modal-cetak').modal('show')
                    }, 1000)
                })
            </script>
        @endif
        <script>
            function cetak() {
                var divToPrint = document.getElementById('modal-body-cetak');
                var newWin = window.open('', 'Print-Window');
                newWin.document.open();
                newWin.document.write(
                    '<link href="/assets/css/app.min.css" rel="stylesheet" /><body class="bg-white" onload="window.print()"><div class="m-l-40 m-r-40">' +
                    divToPrint.innerHTML + '</div></body>'
                );
                setTimeout(function() {
                    newWin.document.close();
                }, 500);
            }
        </script>
    @endpush

</div>
