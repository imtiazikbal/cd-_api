<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>

    <!-- FONT LINK -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Asap:wght@100;200;300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Niconne&display=swap" rel="stylesheet">

    <!-- CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/payment.css') }}">
</head>

<body>

<!-- ---------------------------------------------------------------------------------------------------------------------------------------------------
    START ClearPayment PART
--------------------------------------------------------------------------------------------------------------------------------------------------- -->
<section class="ClearPayment" style="background-image: url('{{ asset('images/bg.png') }}')">

    <div class="container">

        <div class="row">

            <div class="col-md-12">

                <div class="ClearPaymentContent">

                    <!-- Text -->
                    <div class="Text">

                        <h5>Congratulations !</h5>

                        <h4>Your Account has been successfully Created !</h4>

                        <h3>Now you are on the Waiting List !</h3>

                        <div class="FormPart d_flex">

                            <div class="img">
                                <img src="{{ asset('/images/gif1.gif') }}" alt="">
                            </div>
                            <div class="img">
                                <img src="{{ asset('/images/gif2.gif') }}" alt="">
                            </div>
                            <div class="img">
                                <img src="{{ asset('/images/gif1.gif') }}" alt="">
                            </div>

                        </div>

                        <h4>Please Compleat The Payment and Activate your account!</h4>

                        <a href="https://shop.bkash.com/soft-it-carerm47396/pay/bdt1999/tJoBsF" type="submit" class="bg">Pay Now</a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>






<!-- JS Link -->
<script src="{{ asset('js/jquery-1.12.4.min.js') }}"></script>
<script src="{{ asset('/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{ asset('/js/all.min.js') }}"></script>
<script src="{{ asset('/js/custom.js') }}"></script>

</body>

</html>
