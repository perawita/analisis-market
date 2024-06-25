@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">

            <div class="card-header">
                Profile
            </div>
            @foreach ($information as $data)
                <h6 class="fs-5 col-md-8">{{ $data['title'] }}</h6>
                <p class="fs-6 col-md-4">{{ $data['detailInfo'] }}</p>
                <div class="card-header">
                    Curent
                </div>
                <p class="fs-5 col-md-4">{{ $data['detailPrice'] }}</p>
                @foreach ($createButton as $data)
                    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                        <li>
                            <form id="cariDataForm" method="GET" action="{{ route('cari-data') }}">
                                @csrf
                                <input type="search" class="form-control" name="cari-nama" id="cari-nama"
                                    placeholder="Search for Symbols" aria-label="Search" value="{{ $symbol }}"
                                    hidden>
                                <a href="#" id="kirimFormulir"
                                    class="nav-link px-2 link-dark">{{ $data['sumray'] }}</a>
                            </form>
                        </li>
                        <li><a href="{{ route('analisis', ['Symbol' => $symbol]) }}"
                                class="nav-link px-2 link-dark">{{ $data['analisis'] }}</a></li>
                        <li> <a href="{{ route('statistik', ['Symbol' => $symbol]) }}" name="cari-nama" id="cari-nama"
                                class="nav-link px-2 link-dark">
                                {{ $data['statistik'] }}
                            </a></li>
                        <li> <a href="{{ route('finansial', ['Symbol' => $symbol]) }}" name="cari-nama" id="cari-nama"
                                class="nav-link px-2 link-dark">
                                {{ $data['finansial'] }}
                            </a></li>
                        <li> <a href="{{ route('profile', ['Symbol' => $symbol]) }}" name="cari-nama" id="cari-nama"
                                class="nav-link px-2 link-dark">
                                {{ $data['profile'] }}
                            </a></li>
                    </ul>
                @endforeach
            @endforeach

            <br>

            <div class="card">
                <div class="card-header">
                    Finansial
                </div>

                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 mb-md-0">
                    <li>
                        <p class="nav-link px-2 link-dark">Show:</p>
                    </li>
                    <li>
                        <a href="{{ route('finansial', ['Symbol' => $symbol]) }}"
                            class="nav-link px-2 link-dark">Income Statement</a>
                    </li>
                    <li>
                        <a href="{{ route('neraca', ['Symbol' => $symbol]) }}"
                            class="nav-link px-2 link-dark">Balance Sheet</a>
                    </li>
                    <li>
                        <a href="{{ route('arus-kas', ['Symbol' => $symbol]) }}" name="cari-nama" id="cari-nama"
                            class="nav-link px-2 link-dark">
                            Cash Flow
                        </a>
                    </li>
                </ul>
                @foreach ($data_income_steatmen as $count)
                    @foreach ($count['title'] as $title)
                        <h3 class="fs-5 col-md-8">{{ $title }}</h3>
                    @endforeach

                    <div class="row g-5">
                        <div class="col-md-6">
                            <table class="table table-bordered border-primary">
                                <thead>
                                    <tr>
                                        @foreach ($count['labels'] as $labels)
                                            @foreach ($labels as $label)
                                                <th class="border">{{ $label }}</th>
                                            @endforeach
                                        @endforeach
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($count['values'] as $row)
                                        <tr>
                                            @foreach ($row as $value)
                                                <td class="border">{{ $value }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach

            </div>
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
