@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    Analysis
                </div>

                <table class="table table-bordered border-primary">
                    <thead>
                        <tr>
                            @foreach ($response['labels'] as $label)
                                <th class="border">{{ $label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($response['values'] as $row)
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
    </div>
</div>
@include('Footers')
