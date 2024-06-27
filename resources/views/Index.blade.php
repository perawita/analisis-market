@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="text-center">Dapatkan data saham terbaru</h5>
            <p class="text-center">Cari market dengan memasukan nama simbolnya</p>
            <form method="GET" action="{{ route('cari-data') }}" <div class="input-group mb-3">
                @csrf
                <input type="search" class="form-control" name="cari-nama" id="cari-nama"
                    placeholder="Search for Symbols" aria-label="Search">
                <button class="btn btn-outline-secondary" type="Submit">Search</button>
        </div>
        </form>

    </div>

    <br>
    
    <div class="card">
        <div class="card-body">
    <div class="container px-4 py-5" id="hanging-icons">
        <h2 class="pb-2 border-bottom">Layanan Kami</h2>
        <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
      <div class="col d-flex align-items-start">
        <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
          <svg class="bi" width="1em" height="1em"><use xlink:href="#toggles2"></use></svg>
        </div>
        <div>
          <h2>Featured title</h2>
          <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
          <a href="#" class="btn btn-primary">
            Primary button
          </a>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
          <svg class="bi" width="1em" height="1em"><use xlink:href="#cpu-fill"></use></svg>
        </div>
        <div>
          <h2>Featured title</h2>
          <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
          <a href="#" class="btn btn-primary">
            Primary button
          </a>
        </div>
      </div>
      <div class="col d-flex align-items-start">
        <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
          <svg class="bi" width="1em" height="1em"><use xlink:href="#tools"></use></svg>
        </div>
        <div>
          <h2>Featured title</h2>
          <p>Paragraph of text beneath the heading to explain the heading. We'll add onto it with another sentence and probably just keep going until we run out of words.</p>
          <a href="#" class="btn btn-primary">
            Primary button
          </a>
        </div>
      </div>
        </div>
    </div>
    </div>
    </div>
    
    <br>

    <div class="card">
        <div class="card-header">
            More News
        </div>

        <div class="card-body">
            @foreach ($news as $index => $data)
                <div
                    class="row g-0 border rounded overflow-hidden flex-md-row mb-4 shadow-sm h-md-250 position-relative">
                    <div class="col p-4 d-flex flex-column position-static">
                        <strong class="d-inline-block mb-2 text-primary">World News</strong>
                        <h3 class="mb-0">{{ $data['title'] }}</h3>
                        <div class="mb-1 text-muted">{{ $data['source'] }}</div>
                        <p class="card-text mb-auto">{{ Str::limit($data['description'], 250) }}</p>
                        <a href="{{ $data['link'] }}" target="blank" class="stretched-link">Continue
                            reading</a>
                    </div>
                    <div class="col-auto d-none d-lg-block">
                        <img class="bd-placeholder-img" width="200" height="200" src="{{ $data['thumbnail'] }}"
                            role="img" aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice"
                            focusable="false">

                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
@include('Footers')
