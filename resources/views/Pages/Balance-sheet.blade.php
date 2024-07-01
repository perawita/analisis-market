@include('Headers')

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    Financials - Balance sheet
                </div>

                @if ($navLink)
                    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                        @foreach ($navLink as $item)
                            <li>
                                <a href="{{ $item['href'] }}" class="nav-link px-2 link-dark">{{ $item['text'] }}</a>
                            </li>
                        @endforeach
                    </ul>
                @endif
                
                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            @foreach ($response[0]['labels'] as $label)
                            <th class="border">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $num_rows = count($response[0]['values']);
                        @endphp
                        @for ($i = 0; $i < $num_rows; $i++) <tr>
                            @foreach ($response[0]['values'][$i] as $value)
                            <td class="border">{{ $value }}</td>
                            @endforeach
                            </tr>
                            @endfor
                    </tbody>
                </table>

                
            </div>
        </div>
    </div>
</div>


@include('Footers')
