<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Dashboard</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    <style>

    </style>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="antialiased">
    <main>
        {{-- Header --}}
        <header class="p-3 mb-3 border-bottom">
            <div class="container">
                <div class="d-flex flex-wrap align-items-center justify-content-between">

                    <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                        <li><a href="{{ route('Index') }}" class="btn btn-primary nav-link px-2 text-white">Home</a>
                        </li>
                    </ul>

                    <form method="GET" action="{{ route('cari-data') }}"
                        class="d-flex col-12 col-lg-auto mb-3 mb-lg-0 me-lg-3">
                        @csrf
                        <input class="form-control me-2" name="cari-nama" id="cari-nama" type="search"
                            placeholder="Search for Symbols" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit">Search</button>
                    </form>
                </div>
            </div>
        </header>
