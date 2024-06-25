<div class="card-header">
    Profile
</div>
@foreach ($headerData as $data)
    <h6 class="fs-5 col-md-8 m-0 p-0">{{ $data['exchange'] }}</h6>
    <p class="fs-6 col-md-4 ">{{ $data['symbolName'] }}</p>
    @foreach ($priceData as $data)
        <p class="fs-6 col-md-4 m-0 p-0">{{ $data['livePrice'] }} {{ $data['priceChange'] }}
            {{ $data['priceChangePercent'] }}</p>
        <p class="fs-8 col-md-4 m-0 p-0">{{ $data['marketTimeNotice'] }}</p>
    @endforeach
    <div class="card-header">
    </div>

    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
        @foreach ($navItems as $item)
            <li>
                <a href="{{ $item['link'] }}" class="nav-link px-2 link-dark">{{ $item['text'] }}</a>
            </li>
        @endforeach
    </ul>
@endforeach
