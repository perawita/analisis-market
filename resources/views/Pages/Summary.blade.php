@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
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
                                <td class="border">{{ $data['label'] }}</td>
                                <td class="border">{{ $data['value'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    Simple data
                </div>

                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            <th class="border">Label</th>
                            <th class="border">Value</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="border">EPS</td>
                            <td class="border">{{ $eps }}</td>
                        </tr>
                        <tr>
                            <td class="border">CASH FLOW</td>
                            <td class="border">{{ $cash_flow }}</td>
                        </tr>
                        <tr>
                            <td class="border">FREE CASH FLOW</td>
                            <td class="border">{{ $free_cash_flow }}</td>
                        </tr>
                        <tr>
                            <td class="border">DEVIDEN</td>
                            <td class="border">{{ $deviden }}</td>
                        </tr>
                        <tr>
                            <td class="border">PER</td>
                            <td class="border">{{ $PeR }}</td>
                        </tr>
                        <tr>
                            <td class="border">BOOK VALUE PER SHARE</td>
                            <td class="border">${{$bvps}}</td>
                        </tr>
                        <tr>
                            <td class="border">SOLVABILITY</td>
                            <td class="border">(Debt to Equity Ratio) {{$debt_to_equity_ratio}} / (Equity Ratio) {{$equity_ratio}} </td> 
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            @include('Compare')
        </div>
    </div>
</div>
@include('Footers')
