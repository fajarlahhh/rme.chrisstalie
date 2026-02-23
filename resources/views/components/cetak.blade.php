<div>
    <div wire:ignore.self class="modal fade" id="modal-cetak">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-body overflow-auto height-500" id="modal-body-cetak">
                    {!! Session::get('cetak') !!}
                </div>
                <div class="modal-footer">
                    <a href="javascript:;" x-init="$($el).on('click', function() {
                        cetak();
                    })" class="btn btn-primary">Cetak</a>&nbsp;
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('cetak', id => {
                    $('#modal-cetak').modal('show');
                });
            });
        </script>
        <script>
            function cetak() {
                var divToPrint = document.getElementById('modal-body-cetak');
                var newWin = window.open('', 'Print-Window');
                newWin.document.open();
                newWin.document.write(
                    '<link href="/assets/css/app.min.css" rel="stylesheet" />' +
                    '<body class="bg-white" onload="window.print()" style="font-family: Tahoma, Geneva, sans-serif; color: #000; margin-bottom: 0px; font-size: 10px">' +
                    '<div class="m-l-40 m-r-40">' +
                    divToPrint.innerHTML +
                    '</div>' +
                    '</body>'
                );
                setTimeout(function() {
                    newWin.document.close();
                }, 500);
            }
        </script>
    @endpush
</div>
