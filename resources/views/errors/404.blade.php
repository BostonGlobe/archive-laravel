<!DOCTYPE html>
<html lang="en">

@include('partials.head', ['title' => '404 Not Found', 'description' => ''])
<body>
    @include('partials.bannerads')
    <div id="container" class="container">
        <div id="containerBorder">
            @include('partials.header')
            @include('partials.nav')
            <div class="row">
                <div id="content" class="article main col-lg-7">
					<h1>Oops&hellip; 404, Not Found.</h1>
				</div>
				<div id="Col2" class="col-lg-5">
                    @include('partials.sidebar')
                </div>
                <div class="col-12">
                    <hr>
                </div>
			</div>
            @include('partials.footer')
        </div>
    </div>
    @vite(['resources/js/app.js'])
</body>

</html>
