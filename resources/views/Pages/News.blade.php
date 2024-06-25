@include('Headers')
<div class="container py-4">
    <div class="card">
        <div class="card-body">
            @include('Profile')
            <br>

            <div class="card">
                <div class="card-header">
                    News
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
                                <th class="border">No</th>
                                <th class="border">Image</th>
                                <th class="border">Title</th>
                                <th class="border">Link</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($news as $index => $data)
                                <tr>
                                    <td class="border">{{ $index + 1 }}</td>
                                    <td class="border"><img src="{{ $data['thumbnail'] }}" alt="News Image"></td>
                                    <td class="border">{{ $data['title'] }}</td>
                                    <td class="border"><a href="{{ $data['link'] }}" target="blank">Link</a></td>
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
