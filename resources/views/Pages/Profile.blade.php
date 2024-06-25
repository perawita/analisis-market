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

                @foreach ($response as $item)
                    <p class="fs-6 col-md-10">{{ $item }}</p>
                @endforeach
            </div>
        </div>
    </div>
</div>

@include('Footers')
