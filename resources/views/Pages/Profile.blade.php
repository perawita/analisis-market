@include('Headers')

<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    Profile
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
                            <td class="border">Cash Flow</td>
                            <td class="border">{{ $cash_flow }}</td>
                        </tr>
                        <tr>
                            <td class="border">Free Cash Flow</td>
                            <td class="border">{{ $free_cash_flow }}</td>
                        </tr>
                        <tr>
                            <td class="border">Deviden</td>
                            <td class="border">{{ $deviden }}</td>
                        </tr>
                        <tr>
                            <td class="border">PeR</td>
                            <td class="border">{{ $PeR }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <br>
            <div class="card">
                <div class="card-header">
                    Profile Information
                </div>

                @foreach ($response as $item)
                <p class="card-text">{{ nl2br(htmlspecialchars($item)) }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>


<!-- Menyertakan JS Bootstrap dan jQuery -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.3/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


@include('Footers')
