@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>
            <div class="card">
                <div class="card-header">
                    Compare Stocks
                </div>
                @foreach ($table_rows as $table)
                    <table class="table table-bordered border-primary">
                        <thead>
                            <tr>
                                @foreach ($table['labels'] as $label)
                                    <th>{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($table['values'] as $row)
                                <tr>
                                    @foreach ($row as $value)
                                        <td>{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endforeach
            </div>
            <br>
        </div>
    </div>

    @foreach ($table_grup as $item)
        <br>
        <div class="card">
            <div class="card-body">
                <div class="card">
                    <div class="card-header">
                        {{ $item['title_tables'] }}
                    </div>
                    @foreach ($item['table'] as $tables)
                        <table class="table table-bordered border-primary">
                            <thead>
                                <tr>
                                    @foreach ($tables['labels'] as $label)
                                        <th>{{ $label }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($tables['values'] as $row)
                                    <tr>
                                        @foreach ($row as $value)
                                            <td>{{ $value }}</td>
                                        @endforeach
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endforeach
                </div>
                <br>
            </div>
        </div>
    @endforeach

</div>
@include('Footers')

{{-- {{ $table_rows }} --}}
