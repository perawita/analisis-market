@include('Headers')

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    History
                </div>
                @if (isset($response['error']))
                    @foreach ($response['error'] as $error)
                        <img src="{{ $error['img'] }}" alt="Yahoo Logo" />
                        <h1 class="text-center" style="margin-top:20px;">{{ $error['h1'] }}</h1>
                        <p class="text-center">{{ $error['text1'] }}</p>
                        <p class="text-center">{{ $error['text2'] }}</p>
                    @endforeach
                @else
                    <table class="table table-bordered border-primary">
                        <thead>
                            <tr>
                                @foreach ($columnTitles as $column)
                                    <th class="border">{{ $column }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rowData as $index => $row)
                                <tr>
                                    @foreach ($row as $item)
                                        <td class="border">{{ $item }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>

@include('Footers')
