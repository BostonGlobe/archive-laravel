<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ $title }} &mdash; Boston.com Archive</title>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $description }}">
    <link rel="icon" href="/favicon.png" sizes="any">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <meta name="theme-color" content="#fafafa">
    <!-- Scripts -->
    @vite(['resources/css/app.scss'])
</head>

<body>
    <div id="container" class="container">
        <div id="containerBorder">
            <div id="header">
                <div id="headL">
                    <div id="mastHead">
                        <a href="http://www.boston.com" class="imageLink"><img
                                src="http://cache.boston.com/universal/site_graphics/bcom_small.gif"
                                alt="Boston.com"></a>
                        <div id="searchForm">
                            <form action="http://search.boston.com/local/Search.do" onsubmit="searchSubmit();">
                                <input type="text" name="s.sm.query" id="textField"><input type="submit" value="GO"
                                    class="form-button">
                                <input type="hidden" id="tab" name="s.tab" value="">
                            </form>
                        </div>
                    </div>
                </div>
                <div id="headR">
                    <div id="signIn">
                        <span id="login" class="utility">
                        </span>
                        <span id="globeLogo"><span id="gLogoSub"><a
                                    href="https://bostonglobe.com/subscriber/offer/go/zip.asp?cd=WW015697&amp;od=28">Home
                                    Delivery</a></span><a href="http://www.boston.com/bostonglobe/"><img
                                    src="http://cache.boston.com/universal/site_graphics/glogo.jpg"
                                    alt="Boston Globe"></a></span>
                    </div>
                    <div id="headAd">
                        <div>
                            <div class="bannerAd">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <nav id="globalNavRedux">
                <ul class="gnavContainer" id="nav">
                    <li id="ghome"><a href="http://www.boston.com/">Home</a></li>
                    <li id="gglobe"><a href="http://www.boston.com/bostonglobe">Today's Globe</a></li>
                    <li id="gnews"><a href="http://www.boston.com/news/">News</a></li>
                    <li id="gbusiness"><a href="http://www.boston.com/business/">Business</a></li>
                    <li id="gsports"><a href="http://www.boston.com/sports/">Sports</a></li>
                    <li id="glifestyle"><a href="http://www.boston.com/lifestyle/">Lifestyle</a></li>
                    <li id="gae"><a href="http://www.boston.com/ae/">A&amp;E</a></li>
                    <li id="gthings"><a href="http://www.boston.com/thingstodo/">Things to do</a></li>
                    <li id="gtravel"><a href="http://www.boston.com/travel/">Travel</a></li>
                    <li id="gcars"><a href="http://www.boston.com/cars/">Cars</a></li>
                    <li id="gjobs"><a href="http://www.boston.com/jobs/">Jobs</a></li>
                    <li id="gre"><a href="http://www.boston.com/realestate/">Homes</a></li>
                    <li id="gsearch"><a href="http://search.boston.com/">Local Search</a></li>
                </ul>

                <ul id="sNav">
                    <li><a href="http://www.boston.com/business/technology/" id="secnav_technology">Technology</a></li>
                    <li><a href="http://www.boston.com/business/healthcare/" id="secnav_healthcare">Healthcare</a></li>
                    <li><a href="http://finance.boston.com/boston?Page=MarketSummary" id="secnav_markets">Markets</a></li>
                    <li><a href="http://www.boston.com/business/personalfinance/" id="secnav_personalfinance">Personal finance</a></li>
                    <li><a href="http://www.boston.com/business/columnists/" id="secnav_columnists">Columnists</a></li>
                </ul>
            </nav>
            <div id="introad" class="adContainer"></div>
            <div id="billboardAd" class="adContainer"></div>
            <div id="pfHeader"><img src="http://cache.boston.com/universal/site_graphics/bcom_logo_printerfriendly.gif"
                    alt="boston.com"><span class="mssg">THIS STORY HAS BEEN FORMATTED FOR EASY PRINTING</span>
            </div>
            <div class="row">
                <div id="content" class="article main col-md-7">
                    {!! $content !!}
                    <div id="articleFootAd">
                        <div id="articleBottomAd"></div>
                    </div>
                </div>
                <div id="Col2" class="col-md-5">
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
                        <div id="mostPopular">
                            <!--passthru-->
                            <div class="hslice" id="mostpopular"><span class="entry-title" style="display:none;">Boston.com
                                    Most Emailed</span><a rel="entry-content"
                                    href="http://www.boston.com/partners/ieslice/mostpopular.html"
                                    style="display:none;">Subscribe
                                    to Slice</a><a rel="bookmark"
                                    href="http://www.boston.com/?camp=misc:on:mostpopslice:bookmark" target="_blank"
                                    style="display:none;">Bookmark Boston.com Most Emailed</a><span class="ttl"
                                    style="display:none;">120</span>
                                <div id="mEmail">
                                    <h3 class="sectionHeader"><a
                                            href="http://tools.boston.com/pass-it-on/popular?time=hour&p1=MEWell_See_Full_List">MOST
                                            E-MAILED &raquo; </a></h3>
                                    <div class="dotted1px"></div>
                                    <!--passthru-->
                                    <ol class="linklist">
                                        <li class="up"><a
                                                href="/news/local/massachusetts/2014/07/03/report-warrant-issued-for-roggie-bar-owner/index.html">Report:
                                                Warrant Issued for Roggie&#8217;s Bar Owner</a></li>
                                        <li class="down"><a
                                                href="/health/2014/07/03/map-greater-boston-farmers-markets/AIVWLgD8yfkcDiJW5I2HBM/index.html">Map
                                                of Greater Boston Farmers Markets</a></li>
                                        <li class="up"><a
                                                href="/entertainment/events/2014/07/03/boston-pops-concert-move-keeps-the-beach-boys-ditches-joey-mcintyre/index.html">Boston
                                                Pops Concert Move Keeps the Beach Boys, Ditches Joey McIntyre</a></li>
                                        <li class="down"><a
                                                href="/news/opinion/2014/07/03/tried-out-those-new-solar-benches/4e2aqfpZKMImTjZI0j8aLM/index.html?">We
                                                Tried Out Those New Solar Benches</a></li>
                                        <li class="up"><a
                                                href="/travel/new-england/vermont/2013/07/15/new-england-top-outdoor-water-parks/e49xOgFux6THgmiLDXN3xH/pictures.html">New
                                                England&#8217;s top outdoor water parks</a></li>
                                        <li class="down"><a
                                                href="/food-dining/food/2014/07/02/drink-the-week-mojito-italiano/mVcpYl5P7IrDqRSpBx3KbL/video.html">Drink
                                                of the Week: Mojito Italiano</a></li>
                                        <li class="up"><a
                                                href="/news/local/massachusetts/2014/06/30/lawmakers-unveil-compounding-pharmacy-bill/uhQpaM1j2BqYQJLHPljtcO/index.html">Lawmakers
                                                pass compounding pharmacy oversight bill</a></li>
                                    </ol>

                                    <div class="leadOut"><a href="http://twitter.com/intent/user?screen_name=BostonPopular"
                                            class="tt">Follow this list on Twitter: @BostonPopular</a></div>
                                    <div class="padTop16"></div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
            </div>
            <div id="footer">
                <div id="bottomBanner">
                    <div>
                        <div id="bottomLinks">
                            <ul class="gnavContainer">
                                <li><a href="http://www.boston.com/">Home</a></li>
                                <li><a href="http://www.boston.com/news/globe/">Today's Globe</a></li>
                                <li><a href="http://www.boston.com/news/">News</a></li>
                                <li><a href="http://www.boston.com/business/">Business</a></li>
                                <li><a href="http://www.boston.com/sports/">Sports</a></li>
                                <li><a href="http://www.boston.com/lifestyle/">Lifestyle</a></li>
                                <li><a href="http://www.boston.com/ae/">A&amp;E</a></li>
                                <li><a href="http://www.boston.com/thingstodo/">Things to Do</a></li>
                                <li><a href="http://www.boston.com/travel/">Travel</a></li>
                                <li><a href="http://www.boston.com/cars/" class="cfied">Cars</a></li>
                                <li><a href="http://www.boston.com/jobs/" class="cfied">Jobs</a></li>
                                <li><a href="http://www.boston.com/realestate/" class="cfied">Homes</a></li>
                                <li><a href="http://www.boston.com/search/" class="cfied">Local Search</a></li>
                            </ul>
                            <ul id="bcomLinks">
                                <li class="first"><a href="http://www.boston.com/help/bostoncom_info/">Contact
                                        Boston.com</a>
                                </li>
                                <li><a href="http://www.boston.com/help/">Help</a></li>
                                <li><a href="http://www.boston.com/mediakit/bgm/index.html">Advertise</a></li>
                                <li><a
                                        href="http://boston.monster.com/search.aspx?q=%22boston.com%22&amp;cy=us&amp;cnme=boston&amp;sid=40&amp;re=100&amp;jto=1">Work
                                        here</a></li>
                                <li><a href="http://www.boston.com/help/privacy_policy/">Privacy Policy</a></li>
                                <li>
                                    <a
                                        href="http://members.boston.com/reg/login.do?dispatch=loginpage&amp;p1=Foot_ContactBostonCom_Newsletters">Newsletters</a>
                                </li>
                                <li><a href="http://www.boston.com/mobile/">Mobile</a></li>
                                <li><a href="http://www.boston.com/tools/rss/">RSS feeds</a></li>
                                <li><a href="http://spiderbites.boston.com/sitemap-service/Home.xml">Sitemap</a>
                                </li>
                                <li><a href="http://www.boston.com/help/homepage/">Make Boston.com your homepage</a>
                                </li>
                            </ul>
                            <ul id="bglobeLinks">
                                <li class="first"><a
                                        href="http://bostonglobe.com/aboutus/contact_us/default.aspx">Contact The
                                        Boston
                                        Globe</a></li>
                                <li><a href="http://bostonglobe.com/subscribers/homedelivery.aspx?id=5278">Subscribe</a>
                                </li>
                                <li><a href="http://bostonglobe.com/subscribers/custserv.aspx?id=5274">Manage your
                                        subscription</a>
                                </li>
                                <li><a href="https://bostonglobe.com/advertiser/">Advertise</a></li>
                                <li><a href="http://bostonglobe.com/subscribers/extras/index.aspx">The Boston Globe
                                        Extras</a>
                                </li>
                                <li><a
                                        href="http://services.bostonglobe.com/globestore/category.cgi?category=0&amp;source=boston.com&amp;kw=boston.com">The
                                        Boston Globe Store</a></li>
                                <li>&copy;
                                    <script>
                                        var crYear = new Date();
                                        document.write(crYear.getFullYear());
                                    </script> NY Times Co.
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div id="bottomBanner"></div>
                </div>
            </div>
        </div>

</body>

</html>
