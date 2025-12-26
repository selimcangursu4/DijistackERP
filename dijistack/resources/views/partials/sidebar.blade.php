    <nav>
        <div class="app-logo">
            <a class="logo d-inline-block" href="index-2.html">
                <img alt="#" style="width:100%" src="{{ asset('assets/images/logo/logo.png') }}">
            </a>
            <span class="bg-light-primary toggle-semi-nav d-flex-center">
                <i class="ti ti-chevron-right"></i>
            </span>
            <div class="d-flex align-items-center nav-profile p-3">
                <span class="h-45 w-45 d-flex-center b-r-10 position-relative bg-danger m-auto">
                    <img alt="avatar" class="img-fluid b-r-10" src="{{ asset('assets/images/avatar/woman.jpg') }}">
                    <span
                        class="position-absolute top-0 end-0 p-1 bg-success border border-light rounded-circle"></span>
                </span>
                <div class="flex-grow-1 ps-2">
                    <h6 class="text-primary mb-0">{{ auth()->user()->name }}</h6>
                    <p class="text-muted f-s-12 mb-0"> {{ Auth::user()->role->name ?? 'Rol Yok' }} </p>
                </div>
                <div class="dropdown profile-menu-dropdown">
                    <a aria-expanded="false" data-bs-auto-close="true" data-bs-placement="top" data-bs-toggle="dropdown"
                        role="button">
                        <i class="ti ti-settings fs-5"></i>
                    </a>
                    <ul class="dropdown-menu">
                        <li class="dropdown-item">
                            <a class="f-w-500" href="profile.html" target="_blank">
                                <i class="ph-duotone  ph-user-circle pe-1 f-s-20"></i> Profilim
                            </a>
                        </li>
                        <li class="dropdown-item">
                            <a class="f-w-500" href="setting.html" target="_blank">
                                <i class="ph-duotone  ph-gear pe-1 f-s-20"></i> Bildirimler
                            </a>
                        </li>
                        <li class="app-divider-v dotted py-1"></li>

                        <li class="dropdown-item">
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="mb-0 text-danger btn btn-link p-0 text-decoration-none">
                                    <i class="ph-duotone ph-sign-out pe-1 f-s-20"></i> Güvenli Çıkış
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="app-nav" id="app-simple-bar">
            <ul class="main-nav p-0 mt-2">
                <li class="menu-title">
                    <span>Menü</span>
                </li>
                <li class="no-sub">
                    <a href="/{{ auth()->user()->company->domain }}/dashboard">
                        <svg stroke="currentColor" stroke-width="1.5">
                            <use xlink:href="{{ asset('assets/svg/_sprite.svg#home') }}"></use>
                        </svg>
                        Kontrol Paneli
                    </a>
                </li>
                @foreach ($modules as $companyModule)
                    @php $module = $companyModule->module; @endphp
                    <li>
                        <a data-bs-toggle="collapse" href="#module_{{ $module->id }}">
                            <img class="me-2" src="{{ asset($module->icon) }}" style="width:22px; height:22px;">
                            {{ $module->name }}
                        </a>

                        <ul class="collapse" id="module_{{ $module->id }}" data-bs-parent="#sidebarMenu">
                            @foreach ($module->features as $feature)
                                <li>
                                    <a href="/{{ auth()->user()->company->domain }}/{{ $feature->route }}"
                                        class="feature-link" data-feature="{{ $feature->code }}">
                                        {{ $feature->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    </li>
                @endforeach
                <li>
                    <a aria-expanded="false" data-bs-toggle="collapse" href="#settings">
                        <svg stroke="currentColor" stroke-width="1.5">
                            <use xlink:href="{{ asset('assets/svg/_sprite.svg#settings') }}"></use>
                        </svg>
                        Ayarlar
                    </a>
                    <ul class="collapse" id="settings">
                        <li><a href="firma-profili.html">Firma Profili ve Logosu</a></li>
                        <li><a href="sistem-ayarlari.html">Genel Sistem Ayarları (Dil, Zaman Dilimi)</a></li>
                        <li><a href="abonelik-ve-paketler.html">Abonelik Planı ve Aktif Modüller</a></li>
                        <li><a href="kullanim-limitleri.html">Kullanım Limitleri (Kullanıcı/Depo Sayısı)</a></li>
                        <li><a href="kullanici-yonetimi.html">Kullanıcı Tanımları</a></li>
                        <li><a href="rol-ve-yetkiler.html">Rol Bazlı Yetkilendirme (RBAC)</a></li>
                        <li><a href="guvenlik-gunlugu.html">Giriş ve İşlem Günlükleri (Audit Log)</a></li>
                        <li><a href="doviz-ayarlari.html">Para Birimleri ve Kur Entegrasyonu</a></li>
                        <li><a href="vergi-tanimlari.html">Vergi Oranları ve Grupları</a></li>
                        <li><a href="odeme-yontemleri.html">Ödeme Türleri ve Banka Tanımları</a></li>
                        <li><a href="numara-serileri.html">Evrensel Numara Serileri (Fatura, Sipariş No)</a></li>
                        <li><a href="belge-sablonlari.html">Yazdırma ve Tasarım Şablonları (PDF Tasarımı)</a></li>
                        <li><a href="api-anahtarlari.html">API ve Webhook Ayarları</a></li>
                        <li><a href="e-donusum-ayarlari.html">E-Fatura / E-Arşiv Entegratör Ayarları</a></li>
                        <li><a href="eposta-servisleri.html">SMTP ve Bildirim Ayarları</a></li>
                        <li><a href="veri-aktarimi.html">Excel ile Veri Aktarımı / Dışa Aktar</a></li>
                        <li><a href="yedekleme.html">Veri Yedekleme ve Arşivleme</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <div class="menu-navs">
            <span class="menu-previous"><i class="ti ti-chevron-left"></i></span>
            <span class="menu-next"><i class="ti ti-chevron-right"></i></span>
        </div>
    </nav>
