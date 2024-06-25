@include('Headers')

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    Financials
                </div>

                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            @foreach ($response as $row_labels)
                                @foreach ($row_labels['labels'] as $label)
                                    <th class="border">{{ $label }}</th>
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $num_rows = count($response[0]['values']);
                        @endphp
                        @for ($i = 0; $i < $num_rows; $i++)
                            <tr>
                                @foreach ($response as $row_labels)
                                    @foreach ($row_labels['values'][$i] as $value)
                                        <td class="border">{{ $value }}</td>
                                    @endforeach
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
