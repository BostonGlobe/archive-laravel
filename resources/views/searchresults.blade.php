<!DOCTYPE html>
<html lang="en">

@include('partials.head', ['title' => 'Search results for ' . $keyphrase, 'description' => ''])
<body>
    @include('partials.bannerads')
    <div id="container" class="container">
        <div id="containerBorder">
            @include('partials.header')
            @include('partials.nav')
            <div class="row">
                <div id="content" class="article main col-lg-7">
                <h2 class="searchHeading">{{ $totalHits }} search results for "{{ $keyphrase }}"</h2>

                <ul class="search-results-list">
                @foreach ($results as $result)
                    <li class="search-result">
                        <h4><a href="https://archive-laravel.test{{$result['url']}}">{{ $result['title'] }}</a></h4>
                        <p>{!! $result['excerpt'] !!}</p>
                    </li>
                @endforeach
                </ul>
                <p>{{ $results->links() }}</p>
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
