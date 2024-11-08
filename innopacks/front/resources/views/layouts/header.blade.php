@hookinsert('layout.header.top')

@if (isset($header_menus))
    <header id="appHeader">
    @else
        <header id="appHeader" style="background-color: #1b1f22">
@endif
<div class="header-top">
    <div class="container d-flex justify-content-between align-items-center">
        <div class="language-switch d-flex align-items-center">
            {{-- @if (locales()->count() > 1)
                    <div class="dropdown">
                        <a class="btn dropdown-toggle" href="javascript:void(0)">
                            <img src="{{ asset($current_locale->image) }}" class="img-fluid"> {{ $current_locale->name }}
                        </a>

                        <div class="dropdown-menu">
                            @foreach (locales() as $locale)
                                <a class="dropdown-item d-flex"
                                    href="{{ front_route('locales.switch', ['code' => $locale->code]) }}">
                                    <div class="wh-20 me-2"><img src="{{ image_origin($locale['image']) }}"
                                            class="img-fluid border"></div>
                                    {{ $locale->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif --}}

            <div id="google_translate_element"></div>

            <style>
                iframe.skiptranslate {
                    display: none !important;
                }

                body {
                    top: 0px !important;
                }

                #:1.container {
                    display: none !important;
                }

                .goog-logo-link {
                    display: none !important;
                }

                .goog-te-gadget {
                    font-size: 0 !important;
                }

                .goog-te-gadget span {
                    display: none !important;
                }

                .goog-te-combo {
                    background-color: transparent !important;
                    color: white !important;
                    border: 0 !important;
                    width: 100px !important;
                }

                .goog-te-combo:focus,
                .goog-te-combo:active,
                .goog-te-combo:hover {
                    background-color: transparent !important;
                    color: white !important;
                    border: 0 !important;
                    width: 100px !important;
                }

                .goog-te-combo option {
                    background-color: #1b1f22;
                    color: white;
                }
            </style>

            <script>
                function addPlaceholderGlow() {
                    const elements = $('font, span, a, font *, font * *, span *');
                    elements.each((index, element) => {
                        try {
                            if (element.innerText.trim() !== '') {
                                element.classList.add('placeholder');
                            }
                        } catch (e) {

                        }
                    });
                }

                function removePlaceholderGlow() {
                    const elements = $('font, span, a, font *, font * *, span *');


                    elements.each((index, element) => {
                        element.classList.remove('placeholder');
                        element.style.visibility = 'visible';
                    });
                }

                function checkTranslateLoaded() {
                    const selectElement = document.querySelector('.goog-te-combo');
                    if (selectElement && selectElement.value === 'en') {
                        removePlaceholderGlow();
                    } else {
                        const firstFontElement = document.querySelectorAll('font, span, font *, font * *, span *')[0];
                        if (firstFontElement) {
                            const firstFontElementText = firstFontElement.innerText;
                            const interval = setInterval(() => {
                                if (firstFontElement.innerText !== firstFontElementText) {
                                    removePlaceholderGlow();
                                    clearInterval(interval);
                                }
                            }, 500);
                        }
                    }
                }

                window.addEventListener('DOMContentLoaded', () => {
                    addPlaceholderGlow();
                    checkTranslateLoaded();
                });

                // add on change event to select with class goog-te-combo
                document.addEventListener('change', (event) => {
                    if (event.target.classList.contains('goog-te-combo')) {
                      sessionStorage.setItem('selectedLanguage', event.target.value);

                        addPlaceholderGlow();
                        // checkTranslateLoaded();
                        // wait 2 seconds before continue
                        setTimeout(() => {
                            removePlaceholderGlow();
                        }, 1000);
                    }
                });
            </script>

            <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit" async defer></script>

            <script type="text/javascript">
                function setLanguage(lang) {
                    // var select = document.querySelector(".goog-te-combo");
                    // if (select) {
                    //     select.value = lang;
                    //     select.dispatchEvent(new Event("change"));
                    // }
                    var select = $('.goog-te-combo')[0];
                    if (select) {
                        select.value = lang;
                        select.dispatchEvent(new Event("change"));
                    }
                }

                function googleTranslateElementInit() {
                    new google.translate.TranslateElement({
                        pageLanguage: 'en'
                    }, 'google_translate_element');

                    setTimeout(() => {
                        // setLanguage('id');

                        const savedLanguage = sessionStorage.getItem('selectedLanguage');
                        if (savedLanguage) {
                            setLanguage(savedLanguage);
                        } else {
                            $.get("https://ipinfo.io", function(response) {
                                const country = response.country.toLowerCase();
                                setLanguage(country);
                                sessionStorage.setItem('selectedLanguage', country);
                            }, "jsonp");
                        }

                    }, 200);
                }
            </script>


            {{-- <script>
                    // Fungsi untuk membuat opsi dengan ikon bendera
                    function addFlagsToOptions() {
                        const select = $('.goog-te-combo')[0];
                        console.log(select);
                        const options = select.options;

                        // Loop setiap option untuk menambahkan bendera
                        for (let i = 0; i < options.length; i++) {
                            const option = options[i];
                            const countryCode = option.value;

                            // Lewati opsi pertama (misalnya: 'Select Language')
                            if (!countryCode) continue;

                            // Buat elemen span untuk bendera
                            const flagIcon = document.createElement('span');
                            flagIcon.classList.add('flag-icon', `flag-icon-${countryCode}`);

                            // Gabungkan flag dengan teks
                            option.textContent = ''; // Hapus teks awal
                            option.appendChild(flagIcon);
                            option.appendChild(document.createTextNode(' ' + option.innerText));
                        }
                    }
                </script> --}}



            @if (isset($header_menus))

                @if (currencies()->count() > 1)
                    <div class="dropdown ms-4">
                        <a class="btn dropdown-toggle" href="javascript:void(0)">
                            {{ current_currency()->name }}
                        </a>

                        <div class="dropdown-menu">
                            @foreach (currencies() as $currency)
                                <a class="dropdown-item"
                                    href="{{ front_route('currencies.switch', ['code' => $currency->code]) }}">
                                    {{ $currency->name }} ({{ $currency->symbol_left }})
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif
        </div>
        @if (isset($header_menus))

            <div class="top-info">
                <a href="{{ front_route('articles.index') }}">News</a>
                @hookupdate('layouts.header.telephone')
                    <span><i class="bi bi-telephone-outbound"></i> {{ system_setting('telephone') }}</span>
                @endhookupdate
            </div>
        @endif


    </div>
</div>

{{-- if isset $header_menus --}}
@if (isset($header_menus))
    <div class="header-desktop">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="left">
                <h1 class="logo">
                    <a href="{{ front_route('home.index') }}">
                        <img src="{{ image_origin(system_setting('front_logo', 'images/logo.svg')) }}"
                            class="img-fluid">
                    </a>
                </h1>
                <div class="menu">
                    <nav class="navbar navbar-expand-md navbar-light">
                        <ul class="navbar-nav">
                            <li class="nav-item">
                                <a class="nav-link" aria-current="page"
                                    href="{{ front_route('home.index') }}">{{ __('front/common.home') }}</a>
                            </li>

                            @hookupdate('layouts.header.menu.pc')
                                @foreach ($header_menus as $menu)
                                    @if ($menu['children'] ?? [])
                                        <li class="nav-item">
                                            <div class="dropdown">
                                                @if ($menu['name'])
                                                    <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                                                        href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
                                                @endif
                                                <ul class="dropdown-menu">
                                                    @foreach ($menu['children'] as $child)
                                                        @if ($child['name'])
                                                            <li><a class="dropdown-item"
                                                                    href="{{ $child['url'] }}">{{ $child['name'] }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </li>
                                    @else
                                        @if ($menu['name'])
                                            <li class="nav-item">
                                                <a class="nav-link {{ equal_url($menu['url']) ? 'active' : '' }}"
                                                    href="{{ $menu['url'] }}">{{ $menu['name'] }}</a>
                                            </li>
                                        @endif
                                    @endif
                                @endforeach
                            @endhookupdate
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="right">
                <form action="{{ front_route('products.index') }}" method="get" class="search-group">
                    <input type="text" class="form-control" name="keyword"
                        placeholder="{{ __('front/common.search') }}" value="{{ request('keyword') }}">
                    <button type="submit" class="btn"><i class="bi bi-search"></i></button>
                </form>
                <div class="icons">
                    <div class="item">
                        <div class="dropdown account-icon">
                            <a class="btn dropdown-toggle px-0" href="{{ front_route('account.index') }}">
                                <img src="{{ asset('icon/account.svg') }}" class="img-fluid">
                            </a>

                            <div class="dropdown-menu dropdown-menu-end">
                                @if (current_customer())
                                    <a href="{{ front_route('account.index') }}"
                                        class="dropdown-item">{{ __('front/account.account') }}</a>
                                    <a href="{{ front_route('account.orders.index') }}"
                                        class="dropdown-item">{{ __('front/account.orders') }}</a>
                                    <a href="{{ front_route('account.favorites.index') }}"
                                        class="dropdown-item">{{ __('front/account.favorites') }}</a>
                                    <a href="{{ front_route('account.logout') }}"
                                        class="dropdown-item">{{ __('front/account.logout') }}</a>
                                @else
                                    <a href="{{ front_route('login.index') }}"
                                        class="dropdown-item">{{ __('front/common.login') }}</a>
                                    <a href="{{ front_route('register.index') }}"
                                        class="dropdown-item">{{ __('front/common.register') }}</a>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="item" translate="no">
                        <a href="{{ account_route('favorites.index') }}"><img src="{{ asset('icon/love.svg') }}"
                                class="img-fluid"><span class="icon-quantity">{{ $fav_total }}</span></a>
                    </div>
                    <div class="item" translate="no">
                        <a href="{{ front_route('carts.index') }}" class="header-cart-icon"><img
                                src="{{ asset('icon/cart.svg') }}" class="img-fluid"><span
                                class="icon-quantity">0</span></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="header-mobile">
        <div class="mb-icon" data-bs-toggle="offcanvas" data-bs-target="#mobile-menu-offcanvas"
            aria-controls="offcanvasExample">
            <i class="bi bi-list"></i>
        </div>

        <div class="logo">
            <a href="{{ front_route('home.index') }}">
                <img src="{{ image_origin(system_setting('front_logo', 'images/logo.svg')) }}" class="img-fluid">
            </a>
        </div>

        <a href="{{ front_route('carts.index') }}" class="header-cart-icon"><img src="{{ asset('icon/cart.svg') }}"
                class="img-fluid"><span class="icon-quantity">12</span></a>

        <div class="offcanvas offcanvas-start" tabindex="-1" id="mobile-menu-offcanvas">
            <div class="offcanvas-header">
                <form action="" method="get" class="search-group">
                    <input type="text" class="form-control" placeholder="Search">
                    <button type="submit" class="btn"><i class="bi bi-search"></i></button>
                </form>
                <a class="account-icon" href="{{ front_route('account.index') }}">
                    <img src="{{ asset('icon/account.svg') }}" class="img-fluid">
                </a>
            </div>
            <div class="close-offcanvas" data-bs-dismiss="offcanvas"><i class="bi bi-chevron-compact-left"></i>
            </div>
            <div class="offcanvas-body mobile-menu-wrap">
                <div class="accordion accordion-flush" id="menu-accordion">
                    <div class="accordion-item">
                        <div class="nav-item-text">
                            <a class="nav-link {{ equal_route_name('home.index') ? 'active' : '' }}"
                                aria-current="page" href="{{ front_route('home.index') }}">首页</a>
                        </div>
                    </div>

                    @hookupdate('layouts.header.menu.mobile')
                        @foreach ($header_menus as $key => $menu)
                            @if ($menu['name'])
                                <div class="accordion-item">
                                    <div class="nav-item-text">
                                        <a class="nav-link" href="{{ $menu['url'] }}"
                                            data-bs-toggle="{{ !$menu['url'] ? 'collapse' : '' }}">
                                            {{ $menu['name'] }}
                                        </a>
                                        @if (isset($menu['children']) && $menu['children'])
                                            <span class="collapsed" data-bs-toggle="collapse"
                                                data-bs-target="#flush-menu-{{ $key }}"><i
                                                    class="bi bi-chevron-down"></i></span>
                                        @endif
                                    </div>

                                    @if (isset($menu['children']) && $menu['children'])
                                        <div class="accordion-collapse collapse" id="flush-menu-{{ $key }}"
                                            data-bs-parent="#menu-accordion">
                                            <div class="children-group">
                                                <ul class="nav flex-column ul-children">
                                                    @foreach ($menu['children'] as $c_key => $child)
                                                        @if ($child['name'])
                                                            <li class="nav-item">
                                                                <a class="nav-link"
                                                                    href="{{ $child['url'] }}">{{ $child['name'] }}</a>
                                                            </li>
                                                        @endif
                                                    @endforeach
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        @endforeach
                    @endhookupdate

                </div>
            </div>
        </div>
    </div>
@endif
{{-- endif $header_menus --}}
</header>

@hookinsert('layout.header.bottom')
