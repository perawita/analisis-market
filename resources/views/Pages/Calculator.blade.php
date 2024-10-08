@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="text-center">Dapatkan nilai intrinsik sebuah saham serta Margin of Safety (MoS) sebuah saham</h5>
            <p class="text-center">Menghitung nilai intrinsik sebuah saham serta Margin of Safety (MoS), dengan menginput symbol nya secara langsung</p>
            <form method="GET" action="{{ route('Calculator-result') }}" <div class="input-group mb-3">
                @csrf
                <input type="search" class="form-control" name="cari-nama" id="cari-nama" placeholder="Search for Symbols" aria-label="Search">
                <button class="btn btn-outline-secondary" type="Submit">Search</button>
        </div>
        </form>

        <br>

        @if($results)
        <div class="card-body">
            <div class="row g-5">

                <!-- Bagian Kanan -->
                <div class="col-md-5 col-lg-4 order-md-last">
                    <h4 class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-primary">Results</span>
                    </h4>
                    <ul class="list-group mb-3">
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Intrinsik</h6>
                                <small class="text-body-secondary">Nilai intrinsik</small>
                            </div>
                            <span class="text-body-secondary">{{ number_format($results['intrinsic_value']['intrinsik'], 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Harga Saham Saat Ini</h6>
                                <small class="text-body-secondary">Harga saham saat ini</small>
                            </div>
                            <span class="text-body-secondary">{{ number_format($results['stock_price'], 2) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Margin of Safety</h6>
                                <small class="text-body-secondary">Margin of safety</small>
                            </div>
                            <span class="text-body-secondary">{{ number_format($results['mos_value']['mos'], 2) }}%</span>
                        </li>
                    </ul>
                </div>

                <!-- Bagian Kiri -->
                <div class="col-md-7 col-lg-8">
                    <h4 class="mb-3">Value Margin of Safety (MoS) of {{ isset($symbol) ? strtoupper($symbol) : '' }}</h4>
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="eps" class="form-label">EPS</label>
                            <input type="text" class="form-control" id="eps" value="{{ number_format($results['intrinsic_value']['eps'], 2) }}" readonly>
                        </div>

                        <div class="col-md-3">
                            <label for="growth_rate" class="form-label">Growth rate anual (%)</label>
                            <input type="text" class="form-control" id="growth_rate" value="{{ number_format($results['intrinsic_value']['growth_rate'], 2) }}%" readonly>
                        </div>

                        <div class="col-md-4">
                            <label for="intrinsik" class="form-label">Intrinsik</label>
                            <input type="text" class="form-control" id="intrinsik" value="{{ number_format($results['intrinsic_value']['intrinsik'], 2) }}" readonly>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif


    </div>

    <br>

    @include('Footers')
