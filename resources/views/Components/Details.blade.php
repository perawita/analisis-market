@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">

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

            <br>

            <div class="card">
                <div class="card-header">
                    Summary
                </div>
                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            <th class="border">Label</th>
                            <th class="border">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($statistics as $data)
                            <tr>
                                @foreach ($data as $value)
                                    <td class="border">{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function() {
        // Menangani klik pada tautan
        $("#kirimFormulir").on("click", function(e) {
            // Mencegah aksi default dari tautan
            e.preventDefault();

            // Membuka formulir
            $("#cariDataForm").submit();
        });
    });
</script>


@include('Footers')
