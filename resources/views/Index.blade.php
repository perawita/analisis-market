@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="text-center">Dapatkan data saham terbaru</h5>
            <p class="text-center">Cari market dengan memasukan nama simbolnya</p>
            <form method="GET" action="{{ route('cari-data') }}" 
                <div class="input-group mb-3">
                        @csrf
                    <input type="search" class="form-control" name="cari-nama" id="cari-nama"
                        placeholder="Search for Symbols" aria-label="Search">
                    <button class="btn btn-outline-secondary" type="Submit">Search</button>
                </div>
            </form>
        </div>
    </div>
</div>
@include('Footers')
