@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            {{-- Loop untuk semua data --}}
            @foreach ($response as $section => $data)
                <div class="card">
                    <div class="card-header">
                        {{ ucfirst($section) }}
                    </div>

                    <table class="table table-bordered border-primary">
                        <thead>
                            <tr>
                                @foreach ($data['labels'] as $label)
                                    <th class="border">{{ $label }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data['values'] as $row)
                                <tr>
                                    @foreach ($row as $value)
                                        <td class="border">{{ $value }}</td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <br>
            @endforeach
        </div>
    </div>
</div>
@include('Footers')
