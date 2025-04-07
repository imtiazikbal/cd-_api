@extends('layouts.auth')

@section('title', 'Login')

@section('content')

    <section id="login">

        <div class="container">

            <div class="row">
                <div class="col-lg-6">
                    <form action="{{ route('check.login') }}" method="POST">
                        @csrf
                        <div class="left_login">

                            <div class="img">
                                <img src="{{ asset('images/logo_1.svg') }}" alt="">
                            </div>

                            <div class="text">
                                <h3>Welcome To </h3>
                                <p>The Best Business 360 Solution For Your Business, Just Login & You’re Ready To Go
                                    !</p>


                                <div class="form_part">

                                    <!-- E-mail -->
                                    <div class="custome_input">
                                        <label class="@error('password') validation-error-label @enderror">E-mail
                                            Address or Phone Number</label>
                                        <input type="text" name="email"
                                               class="@error('email') validation-error @enderror"
                                               placeholder="Enter e-mail address or phone number here"
                                               value="{{ old('email') }}">

                                        @error('email')
                                        <span>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Password -->
                                    <div class="custome_input">
                                        <label
                                            class="@error('password') validation-error-label @enderror">Password</label>
                                        <input type="password" name="password"
                                               class="@error('password') validation-error @enderror"
                                               placeholder="Enter Password here">

                                        @error('password')
                                        <span>{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <!-- Submit -->
                                    <div class="custome_input">
                                        <button type="submit">Login</button>
                                        <a href="" class="forgot_pass">Forgot Password?</a>
                                    </div>


                                </div>

                            </div>

                        </div>
                    </form>
                </div>

                <div class="col-lg-6">

                    <div class="right_login">

                        <div class="right_login_content">

                            <div class="owl-carousel owl-theme">

                                <!-- item -->
                                <div class="item">
                                    <img src="{{ asset('images/login_right.png') }}" alt="">
                                </div>

                                <!-- item -->
                                <div class="item">
                                    <img src="{{ asset('images/login_right.png') }}" alt="">
                                </div>

                                <!-- item -->
                                <div class="item">
                                    <img src="{{ asset('images/login_right.png') }}" alt="">
                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>
@endsection
