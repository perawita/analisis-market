@include('Headers')
<div class="container py-4">
    <form action="{{ route('Index') }}" method="GET">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Information</h5>
                <button type="submit" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Terjadi Error Pada Halaman. Gagal memuat halaman coba lagi </p> <br>
                <p> {{ $error }}</p>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </form>
</div>
@include('Footers')
