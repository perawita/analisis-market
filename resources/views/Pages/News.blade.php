@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    News
                </div>

                @if (isset($response['error']))
                    @foreach ($response['error'] as $error)
                        <img src="{{ $error['img'] }}" alt="Yahoo Logo" />
                        <h1 class="text-center" style="margin-top:20px;">{{ $error['h1'] }}</h1>
                        <p class="text-center">{{ $error['text1'] }}</p>
                        <p class="text-center">{{ $error['text2'] }}</p>
                    @endforeach
                @else
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
                                    <img class="bd-placeholder-img" width="200" height="200"
                                        src="{{ $data['thumbnail'] }}" role="img"
                                        aria-label="Placeholder: Thumbnail" preserveAspectRatio="xMidYMid slice"
                                        focusable="false">

                                </div>
                            </div>
                        @endforeach
                    </div>
            </div>
            @endif
        </div>
    </div>
</div>
</div>
@include('Footers')
