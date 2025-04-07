<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name') }} | @yield('title')</title>

    <!-- FONT LINK -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;900&display=swap"
          rel="stylesheet">

    <!-- CSS Link -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/owl.carousel.min.css') }}">
    {{-- <link rel="stylesheet" href="{{ asset('css/login.css') }}"> --}}
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="{{ asset('css/material-design-iconic-font.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/media.css') }}">

    <!-- Font Awesome Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" integrity="sha512-KfkfwYDsLkIlwQp6LFnl8zNdLGxu9YAA1QvwINks4PhcElQSvqcyVLLD9aMhXd13uQjoXtEKNosOWaZqXgel0g==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <!-- Sweet Alert Link -->
    <link rel="stylesheet" type="text/css" href="https://common.olemiss.edu/_js/sweet-alert/sweet-alert.css">

</head>
<body>

<div>
    @include('panel.partials.__sidebar')
    @include('panel.partials.__header')

    <div class="section_gaps"></div>

    <div id="app">
        @yield('content')
    </div>


</div>
<script src="{{ asset('js/jquery-1.12.4.min.js') }}"></script>
<script src="{{ mix('/js/app.js') }}"></script>

<script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('js/slick.min.js') }}"></script>
<script src="{{ asset('js/all.min.js') }}"></script>
<script src="{{ asset('js/owl.carousel.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>


<!-- Sweet Alert Link -->
<script src="https://common.olemiss.edu/_js/sweet-alert/sweet-alert.min.js"></script>

{{-- Chart statistics --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
<script>
    // Order count per day of a month
    $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: `merchants/per-day-order-count?month=${06}&year=${2024}`,
            dataType: 'json',
            type: 'GET',
                error: function(error) {
                    console.log(error)
                },
                success: function(data) {
                    let xValues = [];
                    let yValues = [];

                    xValues = data.days;
                    yValues = data.order_counts;

                    const yValuesMax = Math.max(...yValues);
                    const yValuesMin = Math.min(...yValues);
                    
                    new Chart("orderCountChart", {
                        type: "line",
                        data: {
                                labels: xValues,
                                datasets: [{
                                fill: false,
                                lineTension: 0,
                                backgroundColor: "rgba(0,0,255,1.0)",
                                borderColor: "rgba(0,0,255,0.1)",
                                data: yValues
                            }]
                        },
                        options: {
                            legend: {display: false},
                            scales: {
                            yAxes: [{ticks: {min: yValuesMin, max:yValuesMax}}],
                            }
                        }
                    });
                },
    });


</script>

<script>
    // User count per day of a month
    $.ajax({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            url: `merchants/per-day-new-register-count?month=${02}&year=${2024}`,
            dataType: 'json',
            type: 'GET',
                error: function(error) {
                    console.log(error)
                },
                success: function(data) {
                    let xValues = [];
                    let yValues = [];

                    xValues = data.days;
                    yValues = data.user_counts;

                    const yValuesMax = Math.max(...yValues);
                    const yValuesMin = Math.min(...yValues);
                    
                    new Chart("userCountChart", {
                        type: "line",
                        data: {
                                labels: xValues,
                                datasets: [{
                                fill: false,
                                lineTension: 0,
                                backgroundColor: "rgba(0,0,255,1.0)",
                                borderColor: "rgba(0,0,255,0.1)",
                                data: yValues
                            }]
                        },
                        options: {
                            legend: {display: false},
                            scales: {
                            yAxes: [{ticks: {min: yValuesMin, max:yValuesMax}}],
                            }
                        }
                    });
                },
    });


</script>


@yield('scripts')

{{-- <script>
    window.Echo.channel('order-channel')
        .listen('OrderWebsocketEvent', (e) => {
            console.log('private channel - ', e);
        });
</script> --}}


{{-- Order count filter in super admin --}}
<script>
    // order count day wise filter
    $("#orderCountFilter li a" ).click(function() {
        let filterType = $(this).attr('data-filter');
        $('button#orderCountDropDuwn span').text(filterType);

        if(filterType == "Custom"){
            $('.custom-order-filter').removeClass('d-none');
            $('.order-day-wise-filter').addClass('d-none');
        }

        if(filterType != "Custom"){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'order-count-filter',
                dataType: 'json',
                data : {filterType},
                type: 'POST',
                    error: function(error) {
                        console.log(error)
                    },
                    success: function(data) {
                        console.log(data.orders)
                        $('.order-count-show h2').text(data.orders);
                    },
            });
        }
    });

    // custom order count filter
    $('.custom-order-filter form a').on('click', function(){
        const startDate = $('input[name="start_date"]').val();
        const endDate = $('input[name="end_date"]').val();
        let filterType = 'Custom';

        if(filterType && startDate && endDate){
            $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: 'order-count-filter',
                dataType: 'json',
                data : {startDate, endDate, filterType},
                type: 'POST',
                    error: function(error) {
                        console.log(error) 
                    },
                    success: function(data) {
                        console.log(data.orders)
                        $('.order-count-show h2').text(data.orders);
                    },
            });
        }else {
            swal('Missing dates !')
        }
    });

    // Back to order count day filter
    $('span#back-to-day-order-filter').click(function(){
        $('.custom-order-filter').addClass('d-none');
        $('.order-day-wise-filter').removeClass('d-none');
        $('button#orderCountDropDuwn span').text('Filter');
    });
</script>


</body>
</html>
