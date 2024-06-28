@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            <h5 class="text-center">Dapatkan nilai intrinsik sebuah saham serta Margin of Safety (MoS) sebuah saham</h5>
            <p class="text-center">Menghitung nilai intrinsik sebuah saham serta Margin of Safety (MoS), dengan mencari symbol nya langsung</p>
            <form method="GET" action="{{ route('cari-data') }}" <div class="input-group mb-3">
                @csrf
                <input type="search" class="form-control" name="cari-nama" id="cari-nama" placeholder="Search for Symbols" aria-label="Search">
                <button class="btn btn-outline-secondary" type="Submit">Search</button>
        </div>
        </form>

        <br>

        @if($results)
        <div class="card-body">
            <div class="row g-5">
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
                            <span class="text-body-secondary">{{ $results['intrinsik'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Harga Saham Saat Ini</h6>
                                <small class="text-body-secondary">Harga saham saat ini</small>
                            </div>
                            <span class="text-body-secondary">{{ $results['harga'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Margin of Safety</h6>
                                <small class="text-body-secondary">Margin of safety</small>
                            </div>
                            <span class="text-body-secondary">{{ $results['mos'] }}</span>
                        </li>
                    </ul>
                </div>



                <div class="col-md-7 col-lg-8">

                    <h4 class="mb-3">Value Margin of Safety (MoS)</h4>
                    <div class="row gy-3">
                        <div class="col-md-6">
                            <label for="eps" class="form-label">EPS</label>
                            <input type="text" class="form-control" id="eps">
                        </div>

                        <div class="col-md-3">
                            <label for="growth_rate" class="form-label">Growth rate prjection</label>
                            <input type="text" class="form-control" id="growth_rate">
                        </div>

                        <div class="col-md-6">
                            <label for="current_stock_price" class="form-label">PE of company w/ no growth</label>
                            <input type="text" class="form-control" id="current_stock_price">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>

    <br>

    <div class="card">
        <div class="card-header">
            Calculator
        </div>
        <div class="card-body">
            <div class="row g-5">
                @if($results)
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
                            <span class="text-body-secondary">{{ $results['intrinsik'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Harga Saham Saat Ini</h6>
                                <small class="text-body-secondary">Harga saham saat ini</small>
                            </div>
                            <span class="text-body-secondary">{{ $results['harga'] }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">Margin of Safety</h6>
                                <small class="text-body-secondary">Margin of safety</small>
                            </div>
                            <span class="text-body-secondary">{{ $results['mos'] }}</span>
                        </li>
                    </ul>
                </div>
                @endif



                <div class="col-md-7 col-lg-8">

                    <h4 class="mb-3">Margin of Safety (MoS)</h4>
                    <form class="needs-validation" action="{{route('Calculator-result')}}" method="POST">
                        @csrf
                        <div class="row gy-3">
                            <div class="col-md-6">
                                <label for="eps" class="form-label">EPS</label>
                                <input type="text" class="form-control" id="eps" name="eps" required="true">
                            </div>

                            <div class="col-md-3">
                                <label for="growth_rate" class="form-label">Growth rate prjection</label>
                                <input type="text" class="form-control" id="growth_rate" name="growth_rate" required="true">
                            </div>

                            <div class="col-md-6">
                                <label for="current_stock_price" class="form-label">PE of company w/ no growth</label>
                                <input type="text" class="form-control" id="current_stock_price" name="current_stock_price" required="true">
                            </div>
                        </div>

                        <hr class="my-4">

                        <button class="w-100 btn btn-primary btn-lg" type="submit">Get results</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @include('Footers')
