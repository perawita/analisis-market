@include('Headers')

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    Statistics
                </div>

                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            <th class="border">Label</th>
                            <th class="border">Values</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($response as $data)
                            <tr>
                                <td class="border">{{ $data['label'] }}</td>
                                <td class="border">
                                    @foreach ($data['values'] as $value)
                                        {{ $value }}
                                    @endforeach
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="container">
                    @foreach ($financial_highlights as $section)
                        <div class="mb-4">
                            <h3>{{ $section['header'] }}</h3>
                            @foreach ($section['cards'] as $card)
                                <div class="card mb-3">
                                    <div class="card-header">
                                        <h5 class="card-title">{{ $card['title'] }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <tbody>
                                                @foreach ($card['data'] as $item)
                                                    <tr>
                                                        <td>{{ $item['label'] }}</td>
                                                        <td>{{ $item['value'] }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</div>

@include('Footers')
