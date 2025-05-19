@extends('layouts.landing')
@section('title', 'Landing Page')

@section('content')
    <section id="hero" class="hero section light-background"
        style="background-image: url('{{ asset('assets/img/insidegereja.jpg') }}'); background-size: cover; background-position: center;">

        <div class="container">
            <div class="row gy-4 justify-content-center justify-content-lg-between">
                <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center">
                    <h1 data-aos="fade-up" style="color: #000000;">HKBP Kayu Tinggi Jakarta</h1>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200" style="padding-top: 50px;">
                        @if ($fileName)
                            <a href="{{ url('/warta/' . $fileName) }}" class="btn-get-started" target="_blank"
                                style="background-color: #35476e;">Lihat Warta Jemaat</a>
                        @else
                            <p class="btn-get-started" style="background-color: #35476e;">Warta Jemaat belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">
        <div class="container section-title" data-aos="fade-up">
            <h2>Tentang Kami</h2>
        </div>

        <div class="container">
            <div class="row gy-4 align-items-center">
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
                    <img src="assets/img/kayutinggi.png" class="img-fluid mb-4" alt="">
                </div>
                <div class="col-md-6" data-aos="fade-up" data-aos-delay="250">
                    <div class="content">
                        <p class="fst">
                            HKBP Kayu Tinggi Jakarta adalah bagian dari Huria Kristen Batak Protestan (HKBP), sebuah gereja
                            Protestan terbesar di Indonesia yang berakar pada tradisi Lutheran dan budaya Batak. Berdiri di
                            wilayah Jakarta Timur, HKBP Kayu Tinggi hadir sebagai tempat pertumbuhan iman, pelayanan kasih,
                            dan persekutuan jemaat.
                            <br><br>
                            Sejak didirikan pada tahun [tahun berdiri - isi di sini], gereja ini telah menjadi rumah rohani
                            bagi banyak orang dari berbagai latar belakang, khususnya warga Batak di perantauan. Kami
                            mengutamakan pelayanan berbasis kasih, pengajaran Alkitab yang mendalam, serta kegiatan-kegiatan
                            yang mempererat komunitas dalam kasih Kristus.
                        </p>
                        <p class="fst">
                            Visi kami adalah "<strong>Menjadi berkat bagi dunia</strong>"
                            <br><br>
                            Misi kami adalah :
                        </p>
                        <ul>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Beribadah kepada Allah
                                    Tri Tunggal Bapa, Anak,
                                    dan Roh Kudus, dan bersekutu dengan saudara-saudara seiman.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Mendidik jemaat supaya
                                    sungguh-sungguh menjadi
                                    anak Allah dan warga negara yang baik.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Mengabarkan Injil
                                    kepada yang belum mengenal
                                    Kristus dan yang sudah menjauh dari gereja.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Mendoakan dan
                                    menyampaikan pesan kenabian
                                    kepada masyarakat dan negara.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Menggarami dan
                                    menerangi budaya Batak,
                                    Indonesia dan global dengan Injil.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Memulihkan harkat dan
                                    martabat orang kecil dan
                                    tersisih melalui pendidikan, kesehatan, dan pemberdayaan ekonomi masyarakat.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Membangun dan
                                    mengembangkan kerjasama antar
                                    gereja dan dialog lintas agama.</span></li>
                            <li><i class="bi bi-check-circle-fill" style="color: #35476e;"></i> <span>Mengembangkan
                                    penatalayanan (pelayan,
                                    organisasi, administrasi, keuangan, dan aset) dan melaksanakan pembangunan gereja dan
                                    lingkungan hidup.</span></li>
                        </ul>
                        <p>
                            Kami mengadakan ibadah setiap hari Minggu dan berbagai kegiatan rohani lainnya seperti kebaktian
                            kategorial, pelayanan diakonia, sekolah minggu, serta pembinaan remaja dan pemuda.

                            Kami mengundang Anda untuk bertumbuh bersama kami dalam iman, kasih, dan pengharapan.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section><!-- /About Section -->

    <!-- Pendeta Section -->
    <section id="pendeta" class="pendeta section">

        <!-- Section Title -->
        <div class="container section-title text-center" data-aos="fade-up">
            <h2>Pendeta</h2>
            <p><span>Pendeta</span> <span>HKBP Kayu Tinggi<br></span></p>
        </div><!-- End Section Title -->

        <div class="container d-flex justify-content-center">

            <div class="row gy-4 justify-content-center">

                <div class="col-md-6 col-lg-5 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="100">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="{{ asset('images/pdt1.jpg') }}" class="img-fluid" alt=""
                                style="width: 70%; display: block; margin: 0 auto;">
                        </div>
                        <div class="member-info text-center">
                            <h4>Pdt. Hotlan Nahulae, M. Th</h4>
                            <span>Pendeta Ressort</span>
                        </div>
                    </div>
                </div><!-- End Chef Team Member -->

                <div class="col-md-6 col-lg-5 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="{{ asset('images/pdt2.jpg') }}" class="img-fluid" alt=""
                                style="width: 70%; display: block; margin: 0 auto;">
                        </div>
                        <div class="member-info text-center">
                            <h4>Pdt. Sampe Waruwu, S. Th</h4>
                            <span>Pendeta Fungsional</span>
                        </div>
                    </div>
                </div><!-- End Chef Team Member -->

            </div>

        </div>

    </section><!-- /Chefs Section -->

    <!-- Gallery Section -->
    <section id="gallery" class="gallery section light-background">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>Gallery</h2>
            <p><span>Check</span> <span class="description-title">Our Gallery</span></p>
        </div><!-- End Section Title -->

        <div class="container" data-aos="fade-up" data-aos-delay="100">
            <!-- Add Navigation -->
            <div class="d-flex justify-content-between mb-3">
                <!-- Remove navigation buttons from here since they should be inside the swiper container -->
            </div>

            <div class="swiper init-swiper">
                <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": 1,
                        "centeredSlides": true,
                        "pagination": {
                            "el": ".swiper-pagination",
                            "type": "bullets",
                            "clickable": true
                        },
                        "navigation": {
                            "nextEl": ".swiper-button-next",
                            "prevEl": ".swiper-button-prev"
                        },
                        "breakpoints": {
                            "320": {
                                "slidesPerView": 1,
                                "spaceBetween": 0
                            },
                            "768": {
                                "slidesPerView": 2,
                                "spaceBetween": 20
                            },
                            "1200": {
                                "slidesPerView": 3,
                                "spaceBetween": 20
                            }
                        }
                    }
                </script>
                <div class="swiper-wrapper align-items-center">
                    @foreach ($images as $image)
                        <div class="swiper-slide">
                            <a class="glightbox" data-gallery="images-gallery"
                                href="{{ asset('storage/' . $image->image) }}">
                                <img src="{{ asset('storage/' . $image->image) }}" class="img-fluid"
                                    alt="{{ $image->title }}">
                            </a>
                        </div>
                    @endforeach
                </div>
                <div class="swiper-pagination"></div>
            </div>

        </div>
    </section><!-- /Gallery Section -->

@endsection
