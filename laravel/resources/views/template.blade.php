<!doctype html>
<html lang='fr'>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>
        Equipment borrow management app
    </title>
    <script src="{{ asset('js/app.js') }}" defer></script>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link media="all" type="text/css" rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css">
    <link media="all" type="text/css" rel="stylesheet" href="https://netdna.bootstrapcdn.com/bootstrap/3.3.6/css/bootstraptheme.min.css">
    <style>
        textarea {
            resize: none
        }
    </style>
</head>

<body>
    <header class="jumbotron">
        <div class="container">
            <h1 class="page-header"><a href="{{route('categories')}}"> Equipment borrow management app</a></h1>
            @yield('header')
        </div>
    </header>
    <div class="container">
        @yield('contenu')
    </div>
</body>

</html>