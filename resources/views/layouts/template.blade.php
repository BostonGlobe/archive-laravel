<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $title }} &mdash; Boston.com Archive</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Source+Serif+Pro:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#fafafa">
    <!-- Scripts -->
    @vite(['resources/css/app.scss'])
</head>

<body>
    <div id="container" class="container">
        <div id="containerBorder">
            @include('layouts.header')
            @include('layouts.nav')
            <div id="introad" class="adContainer"></div>
            <div id="billboardAd" class="adContainer"></div>
            <div class="row">
                <div class="col-lg-12">
                    <hr class="content-divider">
                </div>
                <div id="content" class="article main col-lg-7">
                    {!! $content !!}
                    <div id="articleFootAd">
                        <div id="articleBottomAd"></div>
                    </div>
                </div>
                <div id="Col2" class="col-lg-5">
                    <div id="Col2Top">
                    </div>
                    <div id="rightAd">
                        <div class="advertisement">Advertisement</div>
                        <div></div>
                    </div>
                    <div id="Col2LRCont">
                        <div id="Col2L"></div>
                        <div id="Col2R">
                            <div></div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <hr>
                </div>
            </div>
            @include('layouts.footer')
        </div>
    </div>
    @vite(['resources/js/app.js'])
</body>

</html>
