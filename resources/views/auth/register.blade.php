@extends('layouts.auth')

@section('title', 'Register')

@section('content')
    <section class="Register">

        <div class="RegisterForm">

            <div class="HeaderPart">

                <div class="Logo">
                    <img src="{{ asset('images/logo_1.svg') }}" alt="">
                </div>

                <div class="text">
                    <h3>Welcome To </h3>
                    <p>The Best Business 360 Solution For Your Business, Just Login & You’re Ready To Go !</p>
                </div>

            </div>

            <form action="{{ route('merchant.register.store') }}" method="POST">
                @csrf

                <div class="form_part">

                    <div class="CustomerInput">
                        <label
                            class="@error('name') validation-error-label @enderror">{{ __('Full Name') }}</label>
                        <input type="text" name="name" class="@error('name') validation-error @enderror"
                               placeholder="Enter Your Full Name " value="{{ old('name') }}">

                        @error('name')
                        <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="CustomerInput">
                        <label
                            class="@error('shop_name') validation-error-label @enderror">{{ __('Shop Name') }}</label>
                        <input type="text" name="shop_name"
                               class="@error('shop_name') validation-error @enderror"
                               placeholder="Enter Shop Name" value="{{ old('shop_name') }}">

                        @error('shop_name')
                        <span>{{ $message }}</span>
                        @enderror
                    </div>


                    <!-- E-mail -->
                    <div class="CustomerInput">
                        <label
                            class="@error('email') validation-error-label @enderror">{{ __('E-mail Address') }}</label>
                        <input type="text" name="email" class="@error('email') validation-error @enderror"
                               placeholder="Enter e-mail address" value="{{ old('email') }}">

                        @error('email')
                        <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="CustomerInput">
                        <label
                            class="@error('phone') validation-error-label @enderror">{{ __('Phone Number') }}</label>
                        <input type="text" name="phone" class="@error('phone') validation-error @enderror"
                               placeholder="Enter phone number" value="{{ old('phone') }}" >

                        @error('phone')
                        <span>{{ $message }}</span>
                        @enderror
                    </div>
                  <!-- pattern="^(?:01[3-9]\d{8}|02[1-9]\d{7}|0[3-9]\d{8})$"-->

                    <!-- Password -->
                    <div class="CustomerInput">
                        <label
                            class="@error('password') validation-error-label @enderror">{{ __('Password') }}</label>
                        <input type="password" name="password"
                               class="@error('password') validation-error @enderror"
                               placeholder="Enter Password here">

                        @error('password')
                        <span>{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div class="CustomerInput">
                        <label>{{ __('Confirm Password') }}</label>
                        <input type="password" name="password_confirmation" placeholder="Confirm Your Password">

                    </div>

                    <!-- Submit -->
                    <div class="CustomerInput">
                        <button type="submit">Sign Up</button>
                    </div>

                    <!-- Submit -->
                    <div class="CustomerInput">
                        <p>Already Have An Account ? <a href="https://dashboard.funnelliner.com/">Log In</a> </p>
                    </div>

                </div>
            </form>

        </div>

    </section>


@endsection
