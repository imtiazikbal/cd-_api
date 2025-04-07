<?php

namespace App\Http\Controllers\Merchant\Auth;

use App\Http\Controllers\MerchantBaseController;
use App\Http\Requests\MerchantLoginRequest;
use App\Http\Resources\MerchantResource;
use App\Http\Requests\Merchant\MerchantRegister;
use App\Http\Resources\UserLoginResource;
use App\Models\MerchantToken;
use App\Models\Order;
use App\Models\User;
use App\Models\Shop;
use App\Services\Sms;
use App\Traits\sendApiResponse;
use Carbon\Carbon;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class LoginController extends MerchantBaseController
{
    use sendApiResponse;

    /**
     * Show the merchant registration page
     *
     * @return Application|Factory|View
     */
    public function index(): Factory|View|Application
    {
        // return view('auth.register');
        return abort(404, 'Not Found');
    }

    public function register(MerchantRegister $request): JsonResponse
    {
        $data = Arr::except($request->validated(), ['shop_name']);
        $data['role'] = User::MERCHANT;
        $data['next_due_date'] = today()->format('Y-m-d');
        $data['payment_status'] = 'unpaid';
        $domain = Str::slug($request->input('shop_name'));
        $exitsDomain = Shop::query()->where('domain', $domain)->first();
        $new_domain = ($exitsDomain) ? $domain . random_int(11, 99) : $domain;

        try {
            $merchant = User::query()->create($data);

            $order_sms = [
                'cancelled' => '1',
                'confirmed' => '1',
                'shipped'   => '1',
                'returned'  => '1',
                'delivered' => '1',
                'pending'   => '1',
                'hold_on'   => '1',
            ];

            $merchant->shop()->create([
                'name'           => $request->input('shop_name'),
                'domain'         => $new_domain,
                'sms_balance'    => "50",
                'shop_id'        => random_int(111111, 999999),
                'order_sms'      => json_encode($order_sms),
                'privacy_policy' => "<p>At " . $request->input('shop_name') . " , we take your privacy seriously. This Privacy Policy explains how we collect, use, and protect your personal information. By using our website or services, you agree to the terms of this Privacy Policy.</p><p><br></p><p><strong>What information do we collect?</strong></p><p>We may collect personal information such as your name, email address, phone number, and billing information when you use our website or services. We may also collect non-personal information such as your browser type, IP address, and other technical information.</p><p><br></p><p><strong>How do we use your information?</strong></p><p>We may use your personal information to provide you with our products or services, communicate with you about your account or our services, and improve our website or services. We may also use your information to send you marketing materials or newsletters if you have opted-in to receive them.</p><p><br></p><p><strong>Do we share your information?</strong></p><p>We do not sell, rent, or share your personal information with third parties except as required by law or to provide you with our services. We may share your information with our service providers or business partners who help us deliver our services.</p><p><br></p><p><strong>How do we protect your information?</strong></p><p>We take reasonable and appropriate measures to protect your personal information from unauthorised access, use, or disclosure. We use encryption, firewalls, and other security measures to protect your information.</p><p>Your choices</p><p><br></p><p>You can choose not to provide us with your personal information, but this may prevent us from providing you with our services. You can also opt-out of receiving marketing materials or newsletters by following the instructions in the email or contacting us directly.</p><p><br></p><p><strong>Changes to this Privacy Policy</strong></p><p>We may update this Privacy Policy from time to time. If we make any material changes to this Privacy Policy, we will notify you by email or by posting a notice on our website.</p><p><strong>Contact Us</strong></p><p>If you have any questions or concerns about this Privacy Policy or our privacy practices, please contact us at contact@" . $request->input('shop_name') . ".</p>",
                'about_us'       => "<p>Welcome to " . $request->input('shop_name') . ", where we believe in [" . $request->input('shop_name') . "'s mission or values]. </p><p>Our company was founded in [year], and we have been dedicated to providing [product/service] to our customers ever since. </p><p>We pride ourselves on [what sets your company apart from others in your industry]. </p><p>At [Company Name], we are committed to [core principles of your business, such as honesty, quality, and customer service]. </p><p><br></p><p>Our team of [number of employees] works tirelessly to ensure that every customer has the best possible experience with our products/services. </p><p>We believe in giving back to our community, and we [describe your company's philanthropic efforts, such as charitable donations or volunteering]. </p><p>Thank you for choosing " . $request->input('shop_name') . " for your [product/service] needs. </p><p>We look forward to serving you and continuing to grow as a company.</p>",
                'tos'            => '<ol><li><strong class="ql-size-large">Introduction: </strong><span class="ql-size-large">This section should identify the parties to the agreement and provide an overview of the products or services being offered.</span></li><li><strong class="ql-size-large">Acceptance:</strong><span class="ql-size-large"> This section should outline the conditions under which the customer agrees to the terms and conditions, such as by clicking a button or placing an order.</span></li><li><strong class="ql-size-large">Product descriptions: </strong><span class="ql-size-large">This section should provide accurate descriptions of the products or services being offered, including any specifications or limitations.</span></li><li><strong class="ql-size-large">Pricing and payment:</strong><span class="ql-size-large"> This section should provide information about the price of the products or services, any applicable taxes or fees, and the payment methods that are accepted.</span></li><li><strong class="ql-size-large">Shipping and delivery:</strong><span class="ql-size-large"> This section should outline the shipping and delivery policies, including estimated delivery times, shipping fees, and any restrictions or limitations.</span></li><li><strong class="ql-size-large">Returns and refunds: </strong><span class="ql-size-large">This section should describe the return and refund policies, including any conditions or restrictions that apply.</span></li><li><span class="ql-size-large">U</span><strong class="ql-size-large">ser conduct: </strong><span class="ql-size-large">This section should outline the rules and guidelines that users must follow when using the ecommerce website or mobile application, such as prohibitions on illegal activities, harassment, or abuse.</span></li><li><strong class="ql-size-large">Intellectual property:</strong><span class="ql-size-large"> This section should describe the intellectual property rights that the company holds, including trademarks, copyrights, and patents.</span></li><li><strong class="ql-size-large">Limitations of liability:</strong><span class="ql-size-large"> This section should specify the limitations of the company liability in case of damages or losses incurred by the customer.</span></li><li><span class="ql-size-large">T</span><strong class="ql-size-large">ermination and cancellation:</strong><span class="ql-size-large"> This section should outline the circumstances under which the agreement can be terminated or cancelled, such as for non-payment or violation of the terms and conditions.</span></li><li><span class="ql-size-large">Go</span><strong class="ql-size-large">verning law and jurisdiction:</strong><span class="ql-size-large"> This section should specify the laws and courts that will govern any disputes between the parties.</span></li><li><strong class="ql-size-large">Privacy policy:</strong><span class="ql-size-large"> This section should outline the companys data collection, usage, and protection policies in compliance with applicable data protection laws.</span></li></ol><p><br></p><p><br></p><p><br></p>',
            ]);
            $merchant->merchantinfo()->create();

            // sms send
            $sms = new Sms();
            $sms->sendOtp($merchant);
            $merchant->load('shop');

            return $this->sendApiResponse(new MerchantResource($merchant), 'Account created Successfully, Verify phone to Use our service.');
        } catch (\Exception $exception) {
            return $this->sendApiResponse($exception->getMessage());
        }
    }

    public function merchant_login(MerchantLoginRequest $request): JsonResponse
    {
        $user = User::query()->with('shop')
            ->where('role', User::MERCHANT)
            ->where('email', $request->input('email'))
            ->orWhere('phone', User::normalizePhone($request->input('email')))
            ->orWhere('phone', User::removeCode($request->input('email')))
            ->first();

        $startDate = Carbon::parse($user->next_due_date)->subDays(30);
        $endDate = Carbon::parse($user->next_due_date);

        if (!$user) {
            return $this->sendApiResponse([], 'Account not found !', 'NotFound', [], 200);
        }

        if ($user->phone_verified_at === null) {
            return $this->sendApiResponse(new UserLoginResource($user), 'Account is not verified !');
        }

        if (Hash::check($request->input('password'), $user->password)) {
            $token = $this->generateToken($user->id, $request->header('ipaddress'), $request->header('browsername'));
            $check_date = Carbon::parse($endDate)->timezone('Asia/Dhaka')->addDays(7);

            $orders = Order::query()
            ->where('shop_id', $user->shop->shop_id)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->withTrashed()
            ->count();

            if ($user->next_due_date !== null && Carbon::today()->timezone('Asia/Dhaka')->gt($check_date)) {
                $user->status = User::STATUS_EXPIRED;
                $user->payment_status = User::UNPAID;
                $user->save();

                return $this->sendApiResponse(new MerchantResource($user), 'Successfully logged in! Your Plan has been Expired', 'PartialAuthorized', ['token' => $token, 'orders' => $orders]);
            }

            return $this->sendApiResponse(new MerchantResource($user), 'Successfully logged in', '', ['token' => $token, 'orders' => $orders]);
        }

        return $this->sendApiResponse('', 'Unable to sign in with given credentials', 'Unauthorized');
    }

    public function generateToken(int $merchant, string $ip, string $browser): string
    {
        $token = Str::random(80);
        $newToken = new MerchantToken();
        $newToken->user_id = $merchant;
        $newToken->token = $token;
        $newToken->ip = $ip;
        $newToken->browser = $browser;
        $newToken->save();

        return $token;
    }

    public function merchant_logout(Request $request)
    {
        try {
            $merchants = MerchantToken::query()->where('user_id', $request->header('id'))
                ->where('ip', $request->header('ipaddress'))
                ->where('browser', $request->header('browsername'))
                ->get();

            foreach ($merchants as $merchant) {
                $merchant->delete();
            }

            return $this->sendApiResponse('', 'Successfully Logout!');
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    public function verify(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required',
            'otp'   => 'required'
        ]);
        $email_check = filter_var($request->input('phone'), FILTER_VALIDATE_EMAIL);

        if ($email_check) {
            $user = User::query()->where('email', $request->input('phone'))->first();
        } else {
            $user = User::query()->with('shop')
                ->where('role', User::MERCHANT)
                ->where('phone', User::normalizePhone($request->input('phone')))
                ->orWhere('phone', User::removeCode($request->input('phone')))
                ->first();
        }

        if ($user->otp === $request->input('otp')) {
            $user->phone_verified_at = now();
            $user->save();

            // sms send
            $sms = new Sms();
            $sms->sendSmsForRegistration($user, $user->shop->shop_id);

            // mail message
            $email = $user->email;
            $messageData = [
                'name'  => $user->name,
                'email' => $email,
            ];
            // send email
            Mail::send('panel.email.register', $messageData, function ($message) use ($email) {
                $message->to($email)->subject("Welcome to Funnel Liner - Let's Get Started!");
            });

            return $this->sendApiResponse(new MerchantResource($user), 'Account Verification Successful');
        }

        return $this->sendApiResponse('', 'Invalid OTP! Please insert valid OTP', 'Invalid');
    }

    public function resendOTP(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required',
        ]);

        $email_check = filter_var($request->input('phone'), FILTER_VALIDATE_EMAIL);

        if ($email_check) {
            $user = User::query()->where('email', $request->input('phone'))->first();
        } else {
            $user = User::query()->with('shop')
                ->where('role', User::MERCHANT)
                ->where('phone', User::normalizePhone($request->input('phone')))
                ->orWhere('phone', User::removeCode($request->input('phone')))
                ->first();
        }

        $sms = new Sms();
        $sms->sendOtp($user);

        return $this->sendApiResponse('', 'OTP has been send to given number');
    }

    public function checkIp($ip, $browser): JsonResponse
    {
        $user = MerchantToken::query()->where('ip', $ip)->where('browser', $browser)->first();

        if (!$user) {
            return $this->sendApiResponse('', 'No user token found with this ip');
        }

        return $this->sendApiResponse($user);
    }
}
