@extends('layouts.landing')
@section('title', 'Landing Page')

@section('content')
    <!-- Hero Section -->
    <section id="hero" class="hero section light-background">

        <div class="container">
            <div class="row gy-4 justify-content-center justify-content-lg-between">
                <div class="col-lg-5 order-2 order-lg-1 d-flex flex-column justify-content-center">
                    <h1 data-aos="fade-up">HKBP Kayu Tinggi Jakarta</h1>
                    <p data-aos="fade-up" data-aos-delay="100">We are team of talented designers making websites with
                        Bootstrap</p>
                    <div class="d-flex" data-aos="fade-up" data-aos-delay="200">
                        @if ($fileName)
                            <a href="{{ asset('warta/' . $fileName) }}" class="btn-get-started" target="_blank">Lihat Warta
                                Jemaat</a>
                        @else
                            <p class="btn-get-started">Warta Jemaat belum tersedia.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </section><!-- /Hero Section -->

    <!-- About Section -->
    <section id="about" class="about section">

        <!-- Section Title -->
        <div class="container section-title" data-aos="fade-up">
            <h2>About Us<br></h2>
            <p><span>Learn More</span> <span class="description-title">About Us</span></p>
        </div><!-- End Section Title -->

        <div class="container">

            <div class="row gy-4">
                <div class="col-lg-7" data-aos="fade-up" data-aos-delay="100">
                    <img src="assets/img/about.jpg" class="img-fluid mb-4" alt="">
                    <div class="book-a-table">
                        <h3>Book a Table</h3>
                        <p>+1 5589 55488 55</p>
                    </div>
                </div>
                <div class="col-lg-5" data-aos="fade-up" data-aos-delay="250">
                    <div class="content ps-0 ps-lg-5">
                        <p class="fst-italic">
                            Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor
                            incididunt ut labore et dolore
                            magna aliqua.
                        </p>
                        <ul>
                            <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea
                                    commodo consequat.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i> <span>Duis aute irure dolor in reprehenderit
                                    in voluptate velit.</span></li>
                            <li><i class="bi bi-check-circle-fill"></i> <span>Ullamco laboris nisi ut aliquip ex ea
                                    commodo consequat. Duis aute irure dolor in reprehenderit in voluptate trideta
                                    storacalaperda mastiro dolore eu fugiat nulla pariatur.</span></li>
                        </ul>
                        <p>
                            Ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in
                            reprehenderit in voluptate
                            velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non
                            proident
                        </p>

                        <div class="position-relative mt-4">
                            <img src="assets/img/about-2.jpg" class="img-fluid" alt="">
                            <a href="https://www.youtube.com/watch?v=Y7f98aduVJ8" class="glightbox pulsating-play-btn"></a>
                        </div>
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
                            <img src="assets/img/chefs/chefs-1.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info text-center">
                            <h4>Pdt. Hotlan Nahulae, M. Th</h4>
                            <span>Pendeta Ressort</span>
                            <p>Velit aut quia fugit et et. Dolorum ea voluptate vel tempore tenetur ipsa quae aut.
                                Ipsum exercitationem iure minima enim corporis et voluptate.</p>
                        </div>
                    </div>
                </div><!-- End Chef Team Member -->

                <div class="col-md-6 col-lg-5 d-flex align-items-stretch" data-aos="fade-up" data-aos-delay="200">
                    <div class="team-member">
                        <div class="member-img">
                            <img src="assets/img/chefs/chefs-2.jpg" class="img-fluid" alt="">
                        </div>
                        <div class="member-info text-center">
                            <h4>Pdt. Sampe Waruwu, S. Th</h4>
                            <span>Pendeta Fungsional</span>
                            <p>Quo esse repellendus quia id. Est eum et accusantium pariatur fugit nihil minima
                                suscipit corporis. Voluptate sed quas reiciendis animi neque sapiente.</p>
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
                <div class="swiper-button-prev"></div>
                <div class="swiper-button-next"></div>
            </div>

            <div class="swiper init-swiper">
                <script type="application/json" class="swiper-config">
                    {
                        "loop": true,
                        "speed": 600,
                        "autoplay": {
                            "delay": 5000
                        },
                        "slidesPerView": "auto",
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
                                "slidesPerView": 3,
                                "spaceBetween": 20
                            },
                            "1200": {
                                "slidesPerView": 5,
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
