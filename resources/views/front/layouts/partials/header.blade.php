<!-- Navbar-->


<header class="bg-primary shadow-sm fixed-top" data-fixed-element>
    <div class="navbar navbar-expand-lg navbar-dark py-0">
        <div class="container-fluid">
            <a class="navbar-brand d-none d-md-block me-1 flex-shrink-0 py-0" href="{{ route('index') }}">
                <div class="logo-bg" style="background-color:#fff;margin-left:-30px; padding: 0 0px 0 30px; ">
                    <img src="{{ asset('img/logo-kakis.png') }}" width="140" height="73" alt="Rice Kakis | Asian Store " >
                    <span class="arrow"></span>
                </div>
            </a>
            <a class="navbar-brand p-0 d-md-none me-0" href="{{ route('index') }}">
                <div class="logo-bg py-1" style="background-color:#fff;margin-left:-15px; padding: 0 10px 0 10px; ">
                <img src="{{ asset('img/logo-kakis.png') }}"  width="100" height="52" alt="Rice Kakis | Asian Store">

                </div>
            </a>
            <!-- Search-->
            <form action="{{ route('pretrazi') }}" id="search-form-first" class="w-100 d-none d-lg-flex flex-nowrap mx-4" method="get">



                <div class="dropdown w-100">
                    <div class="input-group ">
                        <i class="ci-search position-absolute top-50 start-0 translate-middle-y text-muted fs-base ms-3"></i>
                        <input class="form-control rounded-start ps-5" type="text"
                               name="{{ config('settings.search_keyword') }}"
                               value="{{ request()->query('pojam') ?: '' }}"
                               placeholder="{{ __('front/ricekakis.search_products') }}" id="search_box" data-toggle="dropdown" aria-haspopup="true" autocomplete="off" aria-expanded="false" onkeyup="javascript:load_data(this.value)">

                    </div>
                    <div id="search_result" class="live-search"></div>
                </div>


            </form>
            <!-- Toolbar-->
            <div class="navbar-toolbar d-flex flex-shrink-0 align-items-center ms-xl-2">
                <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" aria-label="Open the menu" data-bs-target="#sideNav"><span class="navbar-toggler-icon" aria-hidden="true"></span></button><a class="navbar-tool d-flex d-lg-none" href="#searchBox" data-bs-toggle="collapse" aria-label="Search" role="button" aria-expanded="false" aria-controls="searchBox"><span class="navbar-tool-tooltip">{{ __('front/ricekakis.search') }}</span>
                    <div class="navbar-tool-icon-box"><i class="navbar-tool-icon ci-search"></i></div></a>

                @if(auth()->user())
                    <a class="navbar-tool d-none d-sm-flex ms-1 ms-lg-0 me-n1 me-lg-2" aria-label="My account" href="{{ route('login') }}" >
                        <div class="navbar-tool-icon-box"><i class="navbar-tool-icon ci-user"></i></div>
                        <div class="navbar-tool-text ms-n3"><small>{{ auth()->user()->details->fname }} {{ auth()->user()->details->lname }}</small>{{ __('front/ricekakis.my_account') }}</div>
                    </a>

                @else
                    <a class="navbar-tool d-none d-sm-flex ms-1 ms-lg-0 me-n1 me-lg-2" data-tab-id="pills-signin-tab" aria-label="{{ __('front/ricekakis.login') }}" href="signin-tab"  role="button" data-bs-toggle="modal" data-bs-target="#signin-modal">
                        <div class="navbar-tool-icon-box bg-dark"><i class="navbar-tool-icon ci-user"></i></div>
                        <div class="navbar-tool-text ">{{ __('front/ricekakis.login') }}</div>
                    </a>
                @endif


                @include('front.layouts.partials.language-selector')

                <cart-nav-icon carturl="{{ route('kosarica') }}" checkouturl="{{ route('naplata') }}"></cart-nav-icon>

            </div>
        </div>
    </div>
    <!-- Search collapse-->
    <div class="collapse" id="searchBox">
        <div class="card pt-2 pb-2 border-0 rounded-0">
            <div class="container">
                <form action="{{ route('pretrazi') }}" id="search-form" method="get">
                    <div class="input-group"><i class="ci-search position-absolute top-50 start-0 translate-middle-y ms-3"></i>
                        <input class="form-control rounded-start" type="text" name="{{ config('settings.search_keyword') }}" value="{{ request()->query('pojam') ?: '' }}" placeholder="{{ __('front/ricekakis.search_products') }}">
                        <button type="submit" class="btn btn-primary btn-lg fs-base"><i class="ci-search"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</header>

<!-- Sidebar menu-->
<aside class="offcanvas offcanvas-expand w-100 border-end zindex-lg-5 pt-lg-5" id="sideNav" style="max-width: 19.875rem;">
    <div class="pt-2 d-none d-lg-block"></div>
    <ul class="nav nav-tabs nav-justified mt-0 mt-lg-5 mb-0" role="tablist" >
        <li class="nav-item"><a class="nav-link fw-medium active" href="#categories" data-bs-toggle="tab" role="tab">{{ __('front/ricekakis.categories') }}</a></li>
        <li class="nav-item"><a class="nav-link fw-medium" href="#menu" data-bs-toggle="tab" role="tab">Info</a></li>
        <li class="nav-item d-lg-none"><a class="nav-link " href="#" data-bs-dismiss="offcanvas" aria-label="Close Navigation" role="tab"><i class="ci-close fs-xs me-2"></i></a></li>
    </ul>
    <div class="offcanvas-body px-0 pt-3 pb-0" data-simplebar>
        <div class="tab-content">

            <div class="sidebar-nav tab-pane fade show active" id="categories" role="tabpanel">
            <filter-view ids="{{ isset($ids) ? $ids : null }}"
                         group="kategorija-proizvoda"
                         cat="{{ isset($cat) ? $cat : null }}"
                         subcat="{{ isset($subcat) ? $subcat : null }}"
                         locale="{{ current_locale() }}">
            </filter-view>

                <div id="pages-filter-app">
                    <div class="sidebar-nav tab-pane fade show active" id="pages-filter-blog" role="tabpanel">
                        <pages-filter-view
                            group="blog"
                            title="Blog"
                            url="{{ route('catalog.route.blog') }}"
                            cat="{{ isset($cat) ? $cat : null }}"
                            subcat="{{ isset($subcat) ? $subcat : null }}"
                            locale="{{ current_locale() }}">
                        </pages-filter-view>
                    </div>
                    <div class="sidebar-nav tab-pane fade show active" id="pages-filter-recipe" role="tabpanel">
                        <pages-filter-view
                            group="recepti"
                            title="{{ __('front/ricekakis.recepti') }}"
                            url="{{ route('catalog.route.recepti') }}"
                            cat="{{ isset($cat) ? $cat : null }}"
                            subcat="{{ isset($subcat) ? $subcat : null }}"
                            locale="{{ current_locale() }}">
                        </pages-filter-view>
                    </div>
                </div>



            </div>

            <!-- Menu-->
            <div class="sidebar-nav tab-pane fade" id="menu" role="tabpanel">
                <div class="widget widget-categories">
                    <div class="accordion" id="shop-menu">
                        <!-- Homepages-->
                        @if (isset($pages) && $pages)
                            @foreach($pages as $page)
                                @if (isset($page->translation->title))
                                    @if($page->translation->title != 'Homepage' and $page->group =='page' )
                                        <div class="accordion-item border-bottom">
                                            <h3 class="accordion-header px-grid-gutter"><a class="nav-link-style d-block fs-md fw-normal py-3" href="{{ current_locale() }}/info/{{ $page->translation->slug }}"><span class="d-flex align-items-center">{{ $page->translation->title }}</span></a></h3>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        @endif

                        <div class="accordion-item border-bottom">
                            <h3 class="accordion-header px-grid-gutter"><a class="nav-link-style d-block fs-md fw-normal py-3" href="{{ route('faq') }}"><span class="d-flex align-items-center">{{ __('front/common.faq') }}</span></a></h3>
                        </div>


                        @if(auth()->user())
                            <div class="accordion-item border-bottom">

                                <h3 class="accordion-header px-grid-gutter"><a class="nav-link-style d-block fs-md fw-normal py-3" href="{{ route('login') }}"><span class="d-flex align-items-center"><i class="icon ci-user pe-2"></i> {{ __('front/ricekakis.my_account') }}</span></a></h3>
                            </div>
                        @else
                            <div class="accordion-item border-bottom">
                                <h3 class="accordion-header px-grid-gutter"><a class="closeside nav-link-style d-block fs-md fw-normal py-3" data-tab-id="pills-signin-tab" aria-label="{{ __('front/ricekakis.login') }}" href="signin-tab"  role="button" data-bs-toggle="modal" data-bs-target="#signin-modal" ><span class="d-flex align-items-center"><i class="icon ci-user pe-2"></i> {{ __('front/ricekakis.login') }}</span></a></h3>
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="offcanvas-footer d-block px-grid-gutter pt-4 pb-3 mb-2">


        <p class="pt-2 fw-medium pb-1">{{ __('front/ricekakis.follow_us') }}</p><a class="btn-social bs-outline bs-facebook me-2 mb-2" href="https://www.facebook.com/ricekakis" aria-label="Facebook"><i class="ci-facebook"></i></a><a class="btn-social bs-outline bs-instagram me-2 mb-2" aria-label="Instagram" href="https://www.instagram.com/ricekakis/"><i class="ci-instagram"></i></a><a class="btn-social bs-outline bs-youtube me-2 mb-2" aria-label="Youtube" href="https://www.youtube.com/channel/UCdNEYWHea1pKfUJbKF6fU4g"><i class="ci-youtube"></i></a><a class="btn-social bs-outline bs-tiktok me-2 mb-2" aria-label="Youtube" href="https://www.tiktok.com/@ricekakis"><i class="ci-tiktok"></i></a>
    </div>
</aside>


@push('js_after')
    <script>
        // Translations from resources/lang/{locale}/front/ricekakis.php
        const T = {
            found:        @json(__('front/ricekakis.found')),        // "Pronađeno: :total rezultata (proizvodi :products, brandovi :brands, kategorije :categories)"
            brands:       @json(__('front/ricekakis.brands')),       // "Brandovi"
            categories:   @json(__('front/ricekakis.categories')),   // "Kategorije"
            products:     @json(__('front/ricekakis.products')),     // "Artikli"
            view_all:     @json(__('front/ricekakis.view_all')),     // "Pogledaj sve rezultate"
            no_results:   @json(__('front/ricekakis.no_results')),   // "Nema pronađenih rezultata"
            error:        @json(__('front/ricekakis.error')),        // "Greška pri pretrazi"
        };

        const DEBOUNCE_MS = 200;
        let t = null;

        function debouncedLoad(q){ clearTimeout(t); t = setTimeout(()=>load_data(q), DEBOUNCE_MS); }

        function escapeHtml(s){ return String(s ?? '')
            .replace(/&/g,'&amp;').replace(/</g,'&lt;')
            .replace(/>/g,'&gt;').replace(/"/g,'&quot;').replace(/'/g,'&#039;'); }

        function closeSearch(){
            $('#search_result').removeClass('show').empty();
            $('#search_overlay').addClass('d-none');
            $('#search_box').attr('aria-expanded', 'false');
        }

        // Close UX
        $(document).on('click', '#search_overlay', closeSearch);
        $(document).on('keydown', function(e){ if(e.key === 'Escape') closeSearch(); });
        $(document).on('click', function(e){
            const $form = $('#search-form-first');
            if(!$form.is(e.target) && $form.has(e.target).length === 0){ closeSearch(); }
        });

        // Fallback group/prefix (promijeni po potrebi)
        const CAT_GROUP   = '{{ $group ?? "kategorija-proizvoda" }}';
        const BRAND_PREFX = '{{ request("brand_prefix","brand") }}';

        function load_data(query) {
            if (query.length > 2) {
                $.ajax({
                    method: 'get',
                    url: '{{ route('api.front.autocomplete') }}'
                        + '?pojam_api=' + encodeURIComponent(query)
                        + '&group=' + encodeURIComponent(CAT_GROUP)
                        + '&brand_prefix=' + encodeURIComponent(BRAND_PREFX),
                    success: function(json, textStatus, xhr) {

                        const headerTotal = parseInt(xhr.getResponseHeader('X-Total-Count') || '0', 10);
                        let html = '';

                        const isStructured = json && (json.counts || json.products || json.brands || json.categories);

                        if (isStructured) {
                            const c = json.counts || {products:0, brands:0, categories:0};
                            const total = headerTotal > 0 ? headerTotal : ((c.products|0) + (c.brands|0) + (c.categories|0));

                            // Header counts (with placeholders)
                            html += '<div class="px-3 py-2 border-bottom fs-md text-dark">'
                                +  T.found
                                    .replace(':total', total)
                                    .replace(':products', (c.products||0))
                                    .replace(':brands', (c.brands||0))
                                    .replace(':categories', (c.categories||0))
                                + '</div>';

                            // Brands
                            if (json.brands && json.brands.length > 0) {
                                html += '<div class="px-3 pt-2 pb-2 fw-medium fs-md bg-secondary text-dark">' + T.brands + '</div>';
                                html += '<ul class="list-group list-group-flush">';
                                json.brands.forEach(function(b){
                                    html += '<li class="list-group-item py-2"><a class="text-dark fs-md" href="'+b.url+'">'+escapeHtml(b.name)+'</a></li>';
                                });
                                html += '</ul>';
                            }

                            // Categories
                            if (json.categories && json.categories.length > 0) {
                                html += '<div class="px-3 pt-2 pb-2 fw-medium fs-md bg-secondary text-dark">' + T.categories + '</div>';
                                html += '<ul class="list-group list-group-flush cat">';
                                json.categories.forEach(function(cg){
                                    html += '<li class="list-group-item py-2"><a class="text-dark fs-md" href="'+cg.url+'">'+escapeHtml(cg.name)+'</a></li>';
                                });
                                html += '</ul>';
                            }

                            // Products
                            if (json.products && json.products.length > 0) {
                                html += '<div class="px-3 pt-2 pb-2 fw-medium fs-md bg-secondary text-dark">' + T.products + '</div>';
                                html += '<table class="px-3 table products"><tbody>';
                                json.products.forEach(function (item) {
                                    html += '<tr>'
                                        +   '<td class="image"><a href="'+item.url+'"><img width="80" alt="'+escapeHtml(item.name)+'" src="'+item.image+'"></a></td>'
                                        +   '<td class="main"><a href="'+item.url+'">'+escapeHtml(item.name)+'<br><small>'+escapeHtml(item.brand_title||"")+'</small></a></td>'
                                        +   '<td class="price text-end"><a href="'+item.url+'"><div class="price"><span class="price">'+(item.main_price_text||"")+'</span></div></a></td>'
                                        + '</tr>';
                                });
                                html += '</tbody></table>';
                            }

                            // Footer CTA
                            html += '<div class="result-text"><a href="'+('{{ route('pretrazi') }}' + '?pojam=' + encodeURIComponent(query))+'" class="btn btn-sm btn-primary w-100">'+T.view_all+'</a></div>';

                            if (total === 0) {
                                html = '<div class="result-text text-muted p-3">'+T.no_results+'</div>';
                            }

                        } else {
                            // Legacy fallback
                            const total = headerTotal > 0 ? headerTotal : (Array.isArray(json) ? json.length : 0);

                            if (Array.isArray(json) && json.length > 0) {
                                html += '<div class="px-3 py-2 border-bottom small text-muted">'
                                    +  T.found
                                        .replace(':total', total)
                                        .replace(':products', total)     // legacy nema odvojene countove; prikažemo total
                                        .replace(':brands', 0)
                                        .replace(':categories', 0)
                                    + '</div>';

                                html += '<table class="table products"><tbody>';
                                json.slice(0, 15).forEach(function (item) {
                                    html += '<tr>'
                                        +   '<td class="image"><a href="'+item.url+'"><img width="80" alt="'+escapeHtml(item.name)+'" src="'+item.image+'"></a></td>'
                                        +   '<td class="main"><a href="'+item.url+'">'+escapeHtml(item.name)+'<br><small>'+escapeHtml(item.brand_title||"")+'</small><br><small>'+escapeHtml(item.sku||"")+'</small></a></td>'
                                        +   '<td class="price text-end"><a href="'+item.url+'"><div class="price"><span class="price">'+(item.main_price_text||"")+'</span></div></a></td>'
                                        + '</tr>';
                                });
                                html += '</tbody></table>';
                                html += '<div class="result-text"><a href="'+('{{ route('pretrazi') }}' + '?pojam=' + encodeURIComponent(query))+'" class="btn btn-sm btn-primary w-100">'+T.view_all+'</a></div>';
                            } else {
                                html += '<div class="result-text text-muted">'+T.no_results+'</div>';
                            }
                        }

                        $('#search_result').html(html).addClass('show');
                        $('#search_overlay').removeClass('d-none');
                        $('#search_box').attr('aria-expanded', 'true');
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        console.log(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                        $('#search_result').html('<div class="result-text text-danger">'+T.error+'</div>').addClass('show');
                        $('#search_overlay').removeClass('d-none');
                        $('#search_box').attr('aria-expanded', 'true');
                    }
                });
            } else {
                closeSearch();
            }
        }

        // Debounce input
        document.getElementById('search_box')?.addEventListener('input', function(e){
            debouncedLoad(e.target.value);
        });
    </script>
@endpush



