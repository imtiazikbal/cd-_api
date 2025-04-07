<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('landing/images/favicon.png') }}">
    
    <title>Funnel Liner - Automated Sales Funnel</title>


    <!-- FONT LINK -->
    <!-- font-family: 'Montserrat', sans-serif; -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">



    <!-- CSS Link -->
    <link rel="stylesheet" href="{{ asset('landing/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css"/>
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">

    <link rel="stylesheet" href="{{ asset('landing/css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('landing/css/media.css') }}">



</head>

<body>

<!-- Preloader -->

<div class="preloader">
    <img src="{{ asset('landing/images/preloader.gif')}}" alt="">
</div>



<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    START Header PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->

<nav class="Nav">

    <div class="container">

        <div class="DesktopNav">

            <div class="row d_flex">

                <!-- logo -->
                <div class="col-lg-3">

                    <div class="logo">
                        <img src="{{ asset('landing/images/logo.svg') }}" alt="logo">
                    </div>

                </div>

                <!-- Menubar -->
                <div class="col-lg-6">

                    <div class="Menubar">

                        <ul>

                            <li><a href="" class="active">Home</a></li>
                            <li><a href="">Feature</a></li>
                            <li><a href="">Themes</a></li>
                            <li><a href="">Pricing</a></li>
                            <li><a href="">Blogs</a></li>

                        </ul>

                    </div>

                </div>

                <!-- Login -->
                <div class="col-lg-3">

                    <div class="Login">

                        <a href="https://dashboard.funnelliner.com/">Log In</a>
                        <a href="{{ route('merchant.register') }}">Sign Up</a>

                    </div>

                </div>

            </div>

        </div>

        <div class="MobileNav">

            <div class="row d_flex">

                <!-- logo -->
                <div class="col-7">

                    <div class="logo">
                        <img src="{{ asset('landing/images/logo.svg') }}" alt="logo">
                    </div>

                </div>

                <!-- Menubar -->
                <div class="col-5">

                    <button class="MenuIcon" type="button" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample">
                        <img src="{{ asset('landing/images/menu.png')}}" alt="">
                    </button>

                    <div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">

                        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
                        <div class="offcanvas-body">

                            <div class="Menubar">

                                <ul>

                                    <li><a href="" class="active">Home</a></li>
                                    <li><a href="">Feature</a></li>
                                    <li><a href="">Themes</a></li>
                                    <li><a href="">Pricing</a></li>
                                    <li><a href="">Blogs</a></li>

                                </ul>

                                <div class="Login">
                                    <a href="https://dashboard.funnelliner.com/">Log In</a>
                                    <a href="{{ route('merchant.register') }}">Sign Up</a>
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</nav>

<!-- Sections Gaps -->
<div class="section_gaps"></div>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    Banner   PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="Banner">

    <div class="container">

        <div class="row d_flex">

            <div class="col-lg-5">

                <div class="BannerText">
                    <h2> <span>Welcome to</span> The first ever automated E-Commerce Sales funnel <span>in bangladesh</span></h2>
                </div>

            </div>

            <div class="col-lg-2">

                <div class="img">
                    <img src="{{ asset('landing/images/banner_arrow.png')}}" alt="">
                </div>

            </div>

            <div class="col-lg-5">

                <div class="BannerRight">
                    <h2> <span>Create Your</span> Own Online Shop, <span>Decorate Your Shop,</span> Boost Up Your Sales !</h2>
                </div>

            </div>

        </div>

    </div>

    <div class="BannerBg">
        <img src="{{ asset('landing/images/banner_bg.png') }}" alt="">

        <div class="overlay">

            <div class="container">

                <div class="row">

                    <div class="col-lg-6 m-auto">

                        <img src="{{ asset('landing/images/banner_img.png') }}" alt="">

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>




<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    How to setup  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->

<section class="HowToSetUp section_gaps" data-aos="fade-up" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto">

                <div class="Header">

                    <h2>How To Set Up Your Online Shop</h2>
                    <p>You must be thinking of setting up the store nicely. If your answer is yes then follow these five steps.</p>
                </div>

                <div class="img">
                    <img src="{{ asset('landing/images/how-to-set-up.svg')}}" alt="">
                </div>

            </div>

        </div>

    </div>

</section>


<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    BestFeatures  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->

<section class="BestFeatures section_gaps">

    <div class="row d_flex">

        <div class="col-lg-6" data-aos="fade-up" data-aos-duration="1000">

            <div class="BestFeaturesImg">
                <img src="{{ asset('landing/images/best_feature.png')}}" alt="">
            </div>

        </div>

        <div class="col-lg-6" data-aos="fade-down" data-aos-easing="linear" data-aos-duration="1000">

            <div class="BestFeaturesText">

                <h2>Best Features That Will Make <span>Your Shop Best</span></h2>

                <ul>
                    <li>Easy & Smart Dashboard</li>
                    <li>Instant Shop Creation</li>
                    <li>Easy Shop Management</li>
                    <li>Smart Analytics Report</li>
                    <li>Effecient Order Management</li>
                    <li>Quick Courier Setup</li>
                    <li>One Page Funnel Template</li>
                    <li>Vast Collection Of Themes</li>
                    <li>Smart Customer Management</li>
                    <li>Instant Shop Creation</li>
                    <li>Easy Shop Management</li>
                    <li>Smart Analytics Report</li>
                    <li>Effecient Order Management</li>
                </ul>

            </div>

        </div>

    </div>

</section>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    START ManageYourShop PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="ManageYourShop section_gaps">

    <div class="container">

        <div class="row d_flex">

            <div class="col-lg-7" data-aos="fade-up"data-aos-anchor-placement="top-bottom" data-aos-duration="1000">

                <div class="Header">

                    <h2>Manage Your Shop On The Go With <span>Mobile Friendly Application</span></h2>

                    <p>If the store can be maintained beautifully on mobile. Then the store can be easily maintained from anywhere. Funnelliner's mobile friendly app works to bring your store completely into your hands. Your one touch solution.

Four main reasons to be mobile friendly

Smart dashboard: As beautiful as it looks, all the important information can be easily found in the Funnelliner dashboard.

Fast browsing: Browsing with maximum speed will make your shop supervision faster.


Easy website: You don't have to take any stress to manage. You can manage your website without stress.

All Device Compatible: You can easily use the internet using devices in a beautiful and smooth way.
</p>
                    <!--<p>t.</p>-->

                </div>

                <div class="SmartDashbord d_flex d_justify">

                    <!-- item -->
                    <div class="SmartDashbordItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/home.png')}}" alt="">
                        </div>

                        <p>Smart <br> Dashboard</p>

                    </div>

                    <!-- item -->
                    <div class="SmartDashbordItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/browser.png')}}" alt="">
                        </div>

                        <p>Fast <br> Browsing</p>

                    </div>

                    <!-- item -->
                    <div class="SmartDashbordItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/computer.png') }}" alt="">
                        </div>

                        <p>Easy Website <br> Management</p>

                    </div>

                    <!-- item -->
                    <div class="SmartDashbordItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/mobile.png') }}" alt="">
                        </div>

                        <p>All Device <br> Compatible</p>

                    </div>

                </div>

            </div>

            <div class="col-lg-5" data-aos="fade-down"data-aos-anchor-placement="top-bottom" data-aos-duration="1000">

                <div class="ManageShop">
                    <img src="{{ asset('landing/images/manageshop.png') }}" alt="">
                </div>

            </div>

        </div>

    </div>

</section>


<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
  START OurService PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="OurService section_gaps" data-aos="fade-up" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto">

                <div class="Header text-center">
                    <h2>Business Sectors Who Can Use Our Service</h2>
                    <p>Funnelliner is one such solution for businesses. Where you can build your smart store for all types of business. Build all e-commerce websites easily and smartly. Where you can offer your services successfully. The right platform can make the right e-commerce website.</p>
                </div>

            </div>

        </div>

    </div>

    <!-- slider -->
    <div class="OurServiceSliderContent">

        <div class="swiper OurServiceSlider">

            <div class="swiper-wrapper">

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service1.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service2.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service3.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service4.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service5.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service6.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service7.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service1.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service2.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service3.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service4.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service5.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service6.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

                <!-- item -->
                <div class="swiper-slide">

                    <div class="OurServiceSliderItem">

                        <a href="">

                            <div class="img">
                                <img src="{{ asset('landing/images/service7.png') }}" alt="">
                            </div>

                            <p>Watch & Clock Shop</p>

                        </a>

                    </div>

                </div>

            </div>

            <div class="swiper-pagination"></div>

        </div>

    </div>

</section>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    slider  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="ShopTheme section_gaps" data-aos="fade-up" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto">

                <div class="Header text-center">
                    <h2>Choose Your Shop Theme</h2>
                    <p>All the themes you need are here, choose, select, customise and build your favourite website in no time.</p>
                </div>

            </div>

        </div>

        <!-- ShopThemeContent -->

        <div class="ShopThemeContent">

            <div class="row">

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/theme1.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Furniture & Interior Business</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/theme2.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Grocery/Organic Foods Farm</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/theme3.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Restaurant/FoodBusiness</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/theme4.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Electronics And Gadgets Shop</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/theme5.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Fancy Watch & Clock Shop</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/theme6.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Jewellery & Ornaments Shop</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-12">

                    <div class="ShowMore">
                        <a href="">View All Themes</a>
                    </div>

                </div>

            </div>

        </div>


    </div>

</section>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    Subscription Package  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="ShopTheme section_gaps" data-aos="fade-up" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto" >

                <div class="Header text-center">
                    <h2>Choose Your One Page Funnel Template</h2>
                    <p>The One Page Panel template is the flagship champion that combines the sales page and order form into a single page. May be a convenient solution for you.</p>
                </div>

            </div>

        </div>

        <!-- ShopThemeContent -->

        <div class="ShopThemeContent">

            <div class="row">

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/landing1.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Landing Page 1</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/landing2.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Landing Page 2</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/landing3.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Landing Page 3</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/landing4.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Landing Page 4</h3>

                            <a href="https://theme.funnelliner.com/landing-eight">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/landing5.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Landing Page 5</h3>

                            <a href="https://theme.funnelliner.com/landing-six">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-4 col-sm-6">

                    <div class="ShopThemeItem">

                        <div class="img">
                            <img src="{{ asset('landing/images/landing6.png') }}" alt="">
                        </div>

                        <div class="text">

                            <h3>Landing Page 6</h3>

                            <a href="">View Demo</a>

                        </div>

                    </div>

                </div>

                <!-- item -->
                <div class="col-lg-12">

                    <div class="ShowMore">
                        <a href="">View All Themes</a>
                    </div>

                </div>

            </div>

        </div>


    </div>

</section>
<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    ask question  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<!--<section class="CustomerReview section_gaps" data-aos="fade-up" data-aos-duration="1000">-->

<!--    <div class="Customeroverlay">-->
<!--        <img src="{{ asset('landing/images/customer_overlay.png') }}" alt="">-->
<!--    </div>-->

<!--    <div class="CustomeroverlayRight">-->
<!--        <img src="{{ asset('landing/images/customer_overlay.png') }}" alt="">-->
<!--    </div>-->

<!--    <div class="container">-->

<!--        <div class="row">-->

<!--            <div class="col-lg-12">-->

<!--                <div class="swiper CustomerReviewSlider">-->

<!--                    <div class="swiper-wrapper">-->

                        
<!--                        <div class="swiper-slide">-->

<!--                            <div class="CustomerItem">-->

<!--                                <div class="Quate">-->
<!--                                    <img src="{{ asset('landing/images/quote.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <div class="img">-->
<!--                                    <img src="{{ asset('landing/images/profile.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <h3>SaleSolution was best solution for my clothing business !</h3>-->

<!--                                <h4>Yeasmin Chowdhury</h4>-->

<!--                                <h5>Founder & CEO, Myshop</h5>-->

<!--                            </div>-->

<!--                        </div>-->

                        
<!--                        <div class="swiper-slide">-->

<!--                            <div class="CustomerItem">-->

<!--                                <div class="Quate">-->
<!--                                    <img src="{{ asset('landing/images/quote.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <div class="img">-->
<!--                                    <img src="{{ asset('landing/images/profile.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <h3>SaleSolution was best solution for my clothing business !</h3>-->

<!--                                <h4>Yeasmin Chowdhury</h4>-->

<!--                                <h5>Founder & CEO, Myshop</h5>-->

<!--                            </div>-->

<!--                        </div>-->

                        
<!--                        <div class="swiper-slide">-->

<!--                            <div class="CustomerItem">-->

<!--                                <div class="Quate">-->
<!--                                    <img src="{{ asset('landing/images/quote.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <div class="img">-->
<!--                                    <img src="{{ asset('landing/images/profile.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <h3>SaleSolution was best solution for my clothing business !</h3>-->

<!--                                <h4>Yeasmin Chowdhury</h4>-->

<!--                                <h5>Founder & CEO, Myshop</h5>-->

<!--                            </div>-->

<!--                        </div>-->

                        <!-- item -->
<!--                        <div class="swiper-slide">-->

<!--                            <div class="CustomerItem">-->

<!--                                <div class="Quate">-->
<!--                                    <img src="{{ asset('landing/images/quote.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <div class="img">-->
<!--                                    <img src="{{ asset('landing/images/profile.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <h3>SaleSolution was best solution for my clothing business !</h3>-->

<!--                                <h4>Yeasmin Chowdhury</h4>-->

<!--                                <h5>Founder & CEO, Myshop</h5>-->

<!--                            </div>-->

<!--                        </div>-->

                        <!-- item -->
<!--                        <div class="swiper-slide">-->

<!--                            <div class="CustomerItem">-->

<!--                                <div class="Quate">-->
<!--                                    <img src="{{ asset('landing/images/quote.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <div class="img">-->
<!--                                    <img src="{{ asset('landing/images/profile.png') }}" alt="">-->
<!--                                </div>-->

<!--                                <h3>SaleSolution was best solution for my clothing business !</h3>-->

<!--                                <h4>Yeasmin Chowdhury</h4>-->

<!--                                <h5>Founder & CEO, Myshop</h5>-->

<!--                            </div>-->

<!--                        </div>-->

<!--                    </div>-->

<!--                    <div class="swiper-pagination"></div>-->

<!--                </div>-->

<!--            </div>-->

<!--        </div>-->

<!--    </div>-->

<!--</section>-->
<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    ChossePackageConent PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="ChossePackage section_gaps" data-aos="fade-up" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto">

                <div class="Header text-center">
                    <h2>Choose Your Subscription Package</h2>
                    <p>A convenient subscription package for you with all the benefits of professional templates plus customer response order maintenance with store layout in mind.</p>
                </div>

            </div>

        </div>

        <!-- ChossePackageConent -->
        <div class="ChossePackageConent">

            <div class="row">

                <!-- item -->
                <div class="col-lg-4 m-auto">

                    <div class="ChossePackageItem">

                        <div class="PackageImg">
                            <img src="{{ asset('landing/images/package_img.svg') }}" alt="">
                        </div>

                        <h4>Basic</h4>
                        <h3>BDT 5000</h3>
                        <h5>Every Month</h5>

                        <ul>

                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 1 online store</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Unlimited products</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 501-800 Order Monthly</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Payment gateway integration</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Marketing tools</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Free SSL certificate</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Discount codes</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Themes</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Plugins</li>
                            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 24/7 support</li>

                        </ul>

                        <a href="https://funnelliner.com/register">Subscribe</a>

                    </div>

                </div>

                <!-- item -->
                <!--<div class="col-lg-4">-->

                <!--    <div class="ChossePackageItem">-->

                <!--        <div class="PackageImg">-->
                <!--            <img src="{{ asset('landing/images/package_img.svg') }}" alt="">-->
                <!--        </div>-->

                <!--        <h4>Basic</h4>-->
                <!--        <h3>BDT 2500</h3>-->
                <!--        <h5>Every Month</h5>-->

                <!--        <ul>-->

                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 1 online store</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Unlimited products</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 501-800 Order Monthly</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Payment gateway integration</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Marketing tools</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Free SSL certificate</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Discount codes</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Themes</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Plugins</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 24/7 support</li>-->

                <!--        </ul>-->

                <!--        <a href="">Subscribe</a>-->

                <!--    </div>-->

                <!--</div>-->

                <!-- item -->
                <!--<div class="col-lg-4">-->

                <!--    <div class="ChossePackageItem">-->

                <!--        <div class="PackageImg">-->
                <!--            <img src="{{ asset('landing/images/package_img.svg') }}" alt="">-->
                <!--        </div>-->

                <!--        <h4>Basic</h4>-->
                <!--        <h3>BDT 2500</h3>-->
                <!--        <h5>Every Month</h5>-->

                <!--        <ul>-->

                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 1 online store</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Unlimited products</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 501-800 Order Monthly</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Payment gateway integration</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Marketing tools</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Free SSL certificate</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Discount codes</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Themes</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> Plugins</li>-->
                <!--            <li> <img src="{{ asset('landing/images/sign.svg') }}" alt=""> 24/7 support</li>-->

                <!--        </ul>-->

                <!--        <a href="">Subscribe</a>-->

                <!--    </div>-->

                <!--</div>-->

            </div>

        </div>

    </div>

</section>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    toatal client clounter  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="AskQus section_gaps" data-aos="zoom-in-down" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto">

                <div class="Header text-center">
                    <h2>Frequently Asked Questions</h2>
                    <p>If You spend a lot of your time answering emails or social media queries, an FAQ can be a real time save.</p>
                </div>

            </div>

        </div>

        <!-- AskQusContent -->
        <div class="AskQusContent">

            <div class="row d_flex">

                <div class="col-lg-6">

                    <div class="AskQusTabs">

                        <div class="accordion" id="accordionExample">

                            <div class="accordion-item">

                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                       What is a Funnelliner and how does it work?
                                    </button>
                                </h2>

                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">

                                    <div class="accordion-body">
                                        <p>Funnel liner is an easy-to-use platform to run, manage and grow your business online from desktop and mobile. You can create your online store, add products, manage inventory, accept online payments, and do much more. It’s the simplest and fastest way to take your business to the next level. </p>
                                    </div>

                                </div>

                            </div>

                            <div class="accordion-item">

                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                       Do I need to be technically savvy to use Funnelliner?
                                    </button>
                                </h2>

                                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">

                                    <div class="accordion-body">

                                        <p>No, you don’t need to be technically skilled to run an online business on Funnelliner. Funnelliner makes it easy for you to launch an online business and grow it using a suite of marketing tools and plugins.</p>

                                    </div>

                                </div>
                            </div>
                                <div class="accordion-item">

                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefore" aria-expanded="false" aria-controls="collapsefore">
                                       Can I accept online payments?
                                    </button>
                                </h2>

                                <div id="collapsefore" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">

                                    <div class="accordion-body">

                                        <p>Yes, you can accept online payments from your customers. Funnelliner also allows you to integrate with Bkash, Nagad and Sslcommerz to accept online payments..</p>

                                    </div>

                                </div>
                            </div>
                                <div class="accordion-item">

                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapsefive" aria-expanded="false" aria-controls="collapsefive">
                                       Can I customise my online store?
                                    </button>
                                </h2>

                                <div id="collapsefive" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">

                                    <div class="accordion-body">

                                        <p>Yes, you can customise your online store. You can choose from a range of themes that match your brand and make your store stand out.</p>

                                    </div>

                                </div>
                            </div>
                                <div class="accordion-item">

                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                       Can I pay my fee every Month-end?
                                    </button>
                                </h2>

                                <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">

                                    <div class="accordion-body">

                                        <p>Yes, you can pay a fee at the end of the month..</p>

                                    </div>

                                </div>
                            </div>
                                <div class="accordion-item">

                                <h2 class="accordion-header" id="headingTwo">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSiven" aria-expanded="false" aria-controls="collapseSiven">
                                       What about 3rd party payment gateway?
                                    </button>
                                </h2>

                                <div id="collapseSiven" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">

                                    <div class="accordion-body">

                                        <p>If you want to use a third party payment gateway then you have to purchase a 3rd party payment gateway from a corresponding authority like Sslcommerz , bKash, Nagad and we will integrate in your system.</p>

                                    </div>

                                </div>
                            </div>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="headingThree">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                       Can I use my own domain with Funnelliner?
                                    </button>
                                </h2>
                                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                    <div class="accordion-body">
                                        <p>Yes, you can connect your domain name with Funnelliner. You can also purchase premium domain names by paying additional fees.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="col-lg-6">

                    <div class="AskImg">
                        <img src="{{ asset('landing/images/qus.png') }}" alt="">
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>
<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    footer   PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<section class="JoinUs section_gaps" data-aos="fade-up" data-aos-duration="1000">

    <div class="container">

        <div class="row">

            <div class="col-lg-10 m-auto">

                <div class="Header text-center">

                    <h2>Join Us On Social Media</h2>
                    <p>Join our funnel liner community for e-commerce and marketing tips and tricks where we help solo business owners like you save time and grow with better content strategies.</p>

                </div>

                <div class="SocialIcon d_flex">

                    <a href="https://www.facebook.com/funnelliner" class="fb"><i class="uil uil-facebook-f"></i></a>
                    <a href="https://www.facebook.com/funnelliner" class="ins"><i class="uil uil-instagram"></i></a>
                    <a href="https://www.facebook.com/funnelliner" class="youtube"><i class="uil uil-youtube"></i></a>
                    <a href="https://api.whatsapp.com/send/?phone=8801799733234&text&type=phone_number&app_absent=0" class="whats"><i class="uil uil-whatsapp"></i></a>

                </div>

            </div>

        </div>

    </div>

</section>

<!--<section class="OurPlugin section_gaps">-->

<!--    <div class="container">-->

<!--        <div class="row">-->

<!--            <div class="col-lg-3 col-sm-6">-->

<!--                <div class="OurPluginItem">-->
<!--                    <h3>2000+</h3>-->
<!--                    <h4>Clients Onboarded</h4>-->
<!--                </div>-->

<!--            </div>-->

<!--            <div class="col-lg-3 col-sm-6">-->

<!--                <div class="OurPluginItem">-->
<!--                    <h3>50+</h3>-->
<!--                    <h4>Shop Themes</h4>-->
<!--                </div>-->

<!--            </div>-->

<!--            <div class="col-lg-3 col-sm-6">-->

<!--                <div class="OurPluginItem">-->
<!--                    <h3>100+</h3>-->
<!--                    <h4>Shop Crearted</h4>-->
<!--                </div>-->

<!--            </div>-->

<!--            <div class="col-lg-3 col-sm-6">-->

<!--                <div class="OurPluginItem">-->
<!--                    <h3>590+</h3>-->
<!--                    <h4>Plugin Added</h4>-->
<!--                </div>-->

<!--            </div>-->

<!--        </div>-->

<!--    </div>-->

<!--</section>-->


<footer class="Footer section_gaps">

    <div class="container">

        <div class="row">

            <div class="col-lg-6">

                <div class="row">

                    <div class="col-lg-6 col-sm-6">

                        <div class="Address">
                            <h4>Address</h4>
                            <p> SAR Bhaban, Level-5 , Ka-78, Progoti Sarani, Kuril, Vatara, Dhaka-1229, Bangladesh</p>
                        </div>

                        <div class="Address">
                            <h4>Contact No.</h4>
                            <a href="tel:0123456789">+8801799733234</a>
                            <!--<a href="tel:0123456789">+880 123 456 789</a>-->
                        </div>

                        <div class="Address">
                            <h4>E-mail Address</h4>
                            <a href="mailto:support@funnelliner.com">support@funnelliner.com</a>
                        </div>

                        <div class="Logo">
                            <img src="{{ asset('landing/images/logo.svg') }}" alt="">
                        </div>

                    </div>

                    <div class="col-lg-6 col-sm-6">

                        <div class="Address">

                            <h4>Quick Links</h4>

                            <div class="Menubar">

                                <ul>

                                    <li><a href="">Home</a></li>
                                    <li><a href="">Feature</a></li>
                                    <li><a href="">Themes</a></li>
                                    <li><a href="">Pricing</a></li>
                                    <li><a href="">Blogs</a></li>

                                </ul>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

            <div class="col-lg-6">

                <div class="FormPart">

                    <!--<form action="#" method="post">-->

                        <div class="CustomeInput">
                            <input type="text" name="" placeholder="Full Name">
                        </div>

                        <div class="CustomeInput">
                            <input type="text" name="" placeholder="Contact Number">
                        </div>

                        <div class="CustomeInput">
                            <input type="text" name="" placeholder="E-mail Address">
                        </div>

                        <div class="CustomeInput">
                            <textarea name="" id="" rows="5" placeholder="Enter Your Message"></textarea>
                        </div>

                        <div class="CustomeInput">
                            <button type="submit">Submit</button>
                        </div>

                    <!--</form>-->

                </div>

            </div>

        </div>

    </div>

</footer>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    START  PART
---------------------------------------------------------------------------------------------------------------------------------------------------  -->
<a id="backToTop"><i class="fas fa-long-arrow-alt-up"></i></a>

<!-- JS Link -->
<script src="{{ asset('landing/js/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('landing/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('landing/js/all.min.js') }}"></script>
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
<script src="{{ asset('landing/js/custom.js') }}"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init();
</script>

</body>

</html>
