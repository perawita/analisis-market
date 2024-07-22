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

            </div>
        </div>
    </div>
</div>

@include('Footers')
