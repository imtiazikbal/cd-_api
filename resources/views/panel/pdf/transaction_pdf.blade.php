<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Invoice-8</title>

    <style>
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');

    * {
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Poppins', sans-serif !important;
    }
    </style>

</head>

<body>

    <div
        style="padding: 20px; border: 1px solid #ccc; border-radius: 10px;  -webkit-print-color-adjust: exact; font-family: 'Poppins', sans-serif !important;">

        <!-- Header -->
        <table style="width: 100%; border-collapse: collapse;">

            <tr>

                <td>
                    <img src="https://web.funnelliner.com/email/logo.png" alt="" style="height: 50px;">
                </td>

                <td style="text-align: right;">

                    <h3
                        style="color: #101011; font-size: 25px; line-height: 30px; font-weight: 600; padding: 0; margin: 0; ">
                        Invoice </h3>

                    <h4
                        style="font-size: 13px; line-height: 15px; color: #484B4E; margin-top: 5px; font-weight: 400; padding: 0; margin: 0;">
                        Invoice Number
                        <span
                            style="color: #101011; font-weight: 600; padding: 0; margin: 0">#{{$transaction->invoice_num}}</span>
                    </h4>

                    <p style="font-size: 13px; margin-top: 5px; padding: 0; margin: 0; ">
                        <span style="color: #101011; font-weight: 600;">Date:</span>
                        {{\Carbon\Carbon::today()->format('d M, Y')}}
                    </p>

                </td>

            </tr>

        </table>

        <!-- border -->
        <table style="width: 100%; border-collapse: collapse;">

            <tr>
                <td>
                    <h6 style="border-bottom: 1px solid #ddd; padding: 0; margin: 0; padding-top: 10px; "></h6>
                </td>
            </tr>

        </table>

        <!-- Billing Part -->
        <table style="width: 100%; border-collapse: collapse;">

            <tr>

                <td style="padding-top: 20px;">
                    <h5
                        style="font-size: 14px; font-weight: 600; color: #894bca; line-height: 20px; padding: 0; margin: 0; ">
                        BILLED TO
                    </h5>
                    <h3
                        style="font-size: 18px; line-height: 26px; color: #101011; font-weight: 600; padding: 5px 0; padding: 0; margin: 0; ">
                        Funnel Liner</h3>
                    <p
                        style="font-size: 14px; line-height: 20px; color: #484B4E; font-weight: 400; padding: 0; margin: 0; ">
                        SAR Bhaban, Ka-78
                        Pragati Sarani Main Road, 1229</p>
                </td>

                <td style=" padding-top: 20px; text-align: right;">
                    <h5
                        style="font-size: 14px; font-weight: 600; color: #894bca; line-height: 20px; padding: 0; margin: 0; ">
                        PAYABLE TO
                    </h5>
                    <h3
                        style="font-size: 18px; line-height: 26px; color: #101011; font-weight: 600; padding: 5px 0; padding: 0; margin: 0; ">
                        {{$transaction->user->name}}</h3>
                    <p
                        style="font-size: 14px; line-height: 20px; color: #484B4E; font-weight: 400; padding: 0; margin: 0; ">
                        {{$transaction->user->address}}</p>
                </td>

            </tr>

        </table>

        <!-- border -->
        <table style="width: 100%; border-collapse: collapse;">

            <tr>
                <td>
                    <h6 style="border-bottom: 1px solid #ddd; padding: 0; margin: 0; padding-top: 20px;"></h6>
                </td>
            </tr>

        </table>

        <!-- Order Details -->
        <table style="width: 100%; border-collapse: collapse;">

            <thead>

                <th
                    style="font-size: 14px; padding: 10px 15px; padding-left: 0; line-height: 20px; font-weight: 600; color: #101011; text-transform: capitalize; text-align: left; padding: 0; margin: 0; ">
                    DESCRIPTION</th>

                <th
                    style="font-size: 14px; padding: 10px 15px; padding-left: 0; line-height: 20px; font-weight: 600; color: #101011; text-transform: capitalize; text-align: center; ">
                    Duration</th>

                <th
                    style="font-size: 14px; padding: 10px 15px; padding-left: 0; line-height: 20px; font-weight: 600; color: #101011; text-transform: capitalize; text-align: center; padding: 0; margin: 0; ">
                    Fee</th>

                <th
                    style="font-size: 14px; padding: 10px 15px; padding-left: 0; line-height: 20px; font-weight: 600; color: #101011; text-transform: capitalize; text-align: right; padding: 0; margin: 0; ">
                    TOTAL</th>

            </thead>

            <tbody>

                <tr style="background: #def0ff; border-radius: 10px;">

                    <td
                        style="font-size: 14px; padding: 10px 15px; line-height: 20px; color: #484B4E; font-weight: 400; border-radius: 10px 0 0 10px; text-align: left; ">
                        {{$transaction->type}} ({{ $transaction->createdDate ." - ". $transaction->nexDueDate}})
                    </td>

                    <td
                        style="font-size: 14px; padding: 10px 15px; line-height: 20px; color: #484B4E; font-weight: 400; text-align: center; ">
                        1 Month</td>

                    <td
                        style="font-size: 14px; padding: 10px 15px; line-height: 20px; color: #484B4E; font-weight: 400; text-align: center; ">
                        Tk. {{$transaction->amount}}</td>
                    <td
                        style="font-size: 14px; padding: 10px 15px; line-height: 20px; color: #484B4E; font-weight: 400; border-radius: 0 10px 10px 0; text-align: right; ">
                        Tk. {{$transaction->amount}}</td>

                </tr>

            </tbody>

        </table>

        <!-- border -->
        <table style="width: 100%; border-collapse: collapse;">

            <tr>
                <td>
                    <h6 style="border-bottom: 1px solid #ddd;padding: 0; margin: 0; padding-top: 20px; "></h6>
                </td>
            </tr>

        </table>

        <!-- Payment Method -->
        <table style="width: 100%; border-collapse: collapse;">

            <tr>

                <td style="padding-top: 20px;">

                    <h4 style="font-size: 16px; line-height: 20px; padding: 0; margin: 0; ">Payment Method</h4>

                    <p
                        style="width: 100%; font-size: 14px; margin-top: 5px; color: #484B4E; font-weight: 400; padding: 0; margin: 0; ">
                        {{ $paymentType}}</p>

                    <h5
                        style="font-size: 16px; line-height: 24px; margin-top: 10px; font-weight: 600; padding: 0; margin: 0; ">
                        Transaction ID</h5>


                    <h6
                        style="font-size: 14px; margin-top: 0px; color: #484B4E; font-weight: 400; padding: 0; margin: 0; ">
                        {{ $transaction->trxid}} </h6>

                </td>

                <td style="text-align: right;">

                    <table style="width: 100%; border-collapse: collapse;">

                        <tr>

                            <td>
                                <h3
                                    style="font-size: 18px; line-height: 30px; color: #101011; width: -webkit-fill-available;  padding: 10px 15px; position: relative;margin:0;">
                                    Sub-Total
                                    <span
                                        style="font-size: 18px; color: #101011; font-weight: 600;  position: absolute; top: 10px; right: 15px;">
                                        Tk. {{$transaction->amount}}</span>
                                </h3>
                            </td>

                        </tr>

                        <tr>

                            <td>
                                <h3
                                    style="font-size: 20px; line-height: 30px; color: #FFF; width: -webkit-fill-available;  padding: 5px 15px; padding-top: 2px; border-radius: 10px; background: #894bca; position: relative; margin: 0">
                                    Total
                                    <span
                                        style="font-size: 18px; color: #FFF; font-weight: 600;  position: absolute; top: 0px; right: 15px;">
                                        Tk. {{$transaction->amount}}</span>
                                </h3>
                            </td>

                        </tr>

                    </table>

                </td>

            </tr>

        </table>


        <!-- border -->
        <table style="width: 100%; border-collapse: collapse">

            <tr>
                <td>
                    <h6 style="padding: 0; padding-top: 30px; margin: 0"></h6>
                </td>
            </tr>

        </table>

        <!-- Thank you -->
        <table style="width: 100%; border-collapse: collapse">

            <tr>
                <td style="background: #edeeef; padding: 10px 0; text-align: center; ">
                    <h3
                        style="font-size: 16px; line-height: 24px; color:#101011; font-weight: 500; padding: 0; margin: 0">
                        Thank you for being with us
                    </h3>
                </td>
            </tr>

        </table>

    </div>

</body>

</html>