<?php

namespace App\Http\Controllers\API\V1\Client;

use App\Http\Controllers\MerchantBaseController;
use App\Http\Requests\PasswordUpdateRequest;
use App\Http\Resources\MerchantResource;
use App\Http\Resources\PasswordUpdateResource;
use App\Http\Resources\UserHashEncrypResource;
use App\Models\Shop;
use App\Models\User;
use App\Services\Sms;
use App\Traits\sendApiResponse;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;

class ForgetPasswordController extends MerchantBaseController
{
    use sendApiResponse;

    public function forgetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required'
        ]);
        $user = User::query()->where('role', User::MERCHANT)
            ->where('phone', User::normalizePhone($request->input('phone')))
            ->orWhere('phone', User::removeCode($request->input('phone')))
            ->first();

        if (!$user) {
            return $this->sendApiResponse('', 'No account found with this phone', 'NotFound');
        }

        $send = new Sms();
        $response = $send->sendOtp($user);

        if ($response->status() == 200) {
            return $this->sendApiResponse('', 'Otp Has been send to the number you provided');
        } else {
            return $this->sendApiResponse('', 'Something went wrong', 'SomethingWrong');
        }
    }

    public function verifyOtp(Request $request): JsonResponse
    {
        $request->validate([
            'phone' => 'required',
            'otp'   => 'required'
        ]);
        $user = User::query()->where('role', User::MERCHANT)
            ->where('phone', User::normalizePhone($request->input('phone')))
            ->orWhere('phone', User::removeCode($request->input('phone')))
            ->first();

        if ($user->otp === $request->input('otp')) {
            $user['otp_verified'] = true;

            return $this->sendApiResponse(new MerchantResource($user), 'Otp has been verified');
        } else {
            return $this->sendApiResponse('', 'Invalid Otp', 'Invalid');
        }
    }

    public function updatePassword(Request $request): JsonResponse
    {
        $request->validate([
            'phone'    => ['required'],
            'password' => ['required', 'confirmed', Password::default()]
        ]);

        $merchant = User::query()->where('phone', $request->input('phone'))->first();

        if (!$merchant) {
            return $this->sendApiResponse('', 'No user found associated with this phone', 'NotFound');
        }

        $merchant->update([
            'password' => $request->input('password'),
        ]);

        return $this->sendApiResponse(new MerchantResource($merchant), 'Password has been changed successfully');
    }

    public function updatePasswordAfterRegistration(PasswordUpdateRequest $request): JsonResponse
    {
        try {
            $phone = Crypt::decryptString($request->hash);

            $shop = Shop::query()
                ->where('phone', $phone)
                ->first();

            if($shop) {
                return $this->sendApiResponse('', 'Shop already created !', 'false');
            }

            $merchant = User::query()
                ->with('transactions')
                ->orWhere('phone', User::normalizePhone($phone))
                ->orWhere('phone', User::removeCode($phone))
                ->first();

            if (!$merchant) {
                return $this->sendApiResponse('', 'Invalid user !', 'false');
            }

            $exitsDomain = Shop::query()->where('domain', $request->input('shop_name'))->first();
            $domain = Str::slug($request->input('shop_name'));
            $new_domain = ($exitsDomain) ? $domain . random_int(11, 99) : $domain;

            $merchant->shop()->create([
                'name'           => $request->input('shop_name'),
                'email'          => $merchant->email,
                'phone'          => $merchant->phone,
                'domain'         => $new_domain,
                'sms_balance'    => "5.1",
                'shop_id'        => random_int(111111, 999999),
                'order_sms'      => json_encode(User::ORDERSMS),
                'privacy_policy' => "<p>At " . $request->input('shop_name') . " , we take your privacy seriously. This Privacy Policy explains how we collect, use, and protect your personal information. By using our website or services, you agree to the terms of this Privacy Policy.</p><p><br></p><p><strong>What information do we collect?</strong></p><p>We may collect personal information such as your name, email address, phone number, and billing information when you use our website or services. We may also collect non-personal information such as your browser type, IP address, and other technical information.</p><p><br></p><p><strong>How do we use your information?</strong></p><p>We may use your personal information to provide you with our products or services, communicate with you about your account or our services, and improve our website or services. We may also use your information to send you marketing materials or newsletters if you have opted-in to receive them.</p><p><br></p><p><strong>Do we share your information?</strong></p><p>We do not sell, rent, or share your personal information with third parties except as required by law or to provide you with our services. We may share your information with our service providers or business partners who help us deliver our services.</p><p><br></p><p><strong>How do we protect your information?</strong></p><p>We take reasonable and appropriate measures to protect your personal information from unauthorised access, use, or disclosure. We use encryption, firewalls, and other security measures to protect your information.</p><p>Your choices</p><p><br></p><p>You can choose not to provide us with your personal information, but this may prevent us from providing you with our services. You can also opt-out of receiving marketing materials or newsletters by following the instructions in the email or contacting us directly.</p><p><br></p><p><strong>Changes to this Privacy Policy</strong></p><p>We may update this Privacy Policy from time to time. If we make any material changes to this Privacy Policy, we will notify you by email or by posting a notice on our website.</p><p><strong>Contact Us</strong></p><p>If you have any questions or concerns about this Privacy Policy or our privacy practices, please contact us at contact@" . $request->input('shop_name') . ".</p>",
                'about_us'       => "<p>Welcome to " . $request->input('shop_name') . ", where we believe in [" . $request->input('shop_name') . "'s mission or values]. </p><p>Our company was founded in [year], and we have been dedicated to providing [product/service] to our customers ever since. </p><p>We pride ourselves on [what sets your company apart from others in your industry]. </p><p>At [Company Name], we are committed to [core principles of your business, such as honesty, quality, and customer service]. </p><p><br></p><p>Our team of [number of employees] works tirelessly to ensure that every customer has the best possible experience with our products/services. </p><p>We believe in giving back to our community, and we [describe your company's philanthropic efforts, such as charitable donations or volunteering]. </p><p>Thank you for choosing " . $request->input('shop_name') . " for your [product/service] needs. </p><p>We look forward to serving you and continuing to grow as a company.</p>",
                'tos'            => '<ol><li><strong class="ql-size-large">Introduction: </strong><span class="ql-size-large">This section should identify the parties to the agreement and provide an overview of the products or services being offered.</span></li><li><strong class="ql-size-large">Acceptance:</strong><span class="ql-size-large"> This section should outline the conditions under which the customer agrees to the terms and conditions, such as by clicking a button or placing an order.</span></li><li><strong class="ql-size-large">Product descriptions: </strong><span class="ql-size-large">This section should provide accurate descriptions of the products or services being offered, including any specifications or limitations.</span></li><li><strong class="ql-size-large">Pricing and payment:</strong><span class="ql-size-large"> This section should provide information about the price of the products or services, any applicable taxes or fees, and the payment methods that are accepted.</span></li><li><strong class="ql-size-large">Shipping and delivery:</strong><span class="ql-size-large"> This section should outline the shipping and delivery policies, including estimated delivery times, shipping fees, and any restrictions or limitations.</span></li><li><strong class="ql-size-large">Returns and refunds: </strong><span class="ql-size-large">This section should describe the return and refund policies, including any conditions or restrictions that apply.</span></li><li><span class="ql-size-large">U</span><strong class="ql-size-large">ser conduct: </strong><span class="ql-size-large">This section should outline the rules and guidelines that users must follow when using the ecommerce website or mobile application, such as prohibitions on illegal activities, harassment, or abuse.</span></li><li><strong class="ql-size-large">Intellectual property:</strong><span class="ql-size-large"> This section should describe the intellectual property rights that the company holds, including trademarks, copyrights, and patents.</span></li><li><strong class="ql-size-large">Limitations of liability:</strong><span class="ql-size-large"> This section should specify the limitations of the company liability in case of damages or losses incurred by the customer.</span></li><li><span class="ql-size-large">T</span><strong class="ql-size-large">ermination and cancellation:</strong><span class="ql-size-large"> This section should outline the circumstances under which the agreement can be terminated or cancelled, such as for non-payment or violation of the terms and conditions.</span></li><li><span class="ql-size-large">Go</span><strong class="ql-size-large">verning law and jurisdiction:</strong><span class="ql-size-large"> This section should specify the laws and courts that will govern any disputes between the parties.</span></li><li><strong class="ql-size-large">Privacy policy:</strong><span class="ql-size-large"> This section should outline the companys data collection, usage, and protection policies in compliance with applicable data protection laws.</span></li></ol><p><br></p><p><br></p><p><br></p>',
            ]);

            $merchant->merchantinfo()->create();
            $sms = new Sms();
            $sms->sendSmsForRegistration($merchant, $merchant->shop->shop_id);

            $merchant->update([
                'password' => $request->input('password')
            ]);

            return $this->sendApiResponse(new PasswordUpdateResource($merchant), 'Password updated successfully');
        } catch (DecryptException $e) {
            return $this->sendApiResponse('', 'Invalid hash !', 'false');
        }
    }

    public function userHashEncryption(Request $request)
    {
        try {
            $phone = Crypt::decryptString($request->hash);

            $merchant = User::query()
                    ->with('transactions')
                    ->orWhere('phone', User::normalizePhone($phone))
                    ->orWhere('phone', User::removeCode($phone))
                    ->first();

                    

            return $this->sendApiResponse(new UserHashEncrypResource($merchant), 'User hash encrypted');
        } catch (DecryptException $e) {
            return $this->sendApiResponse('', 'Invalid hash !', 'false');
        }
    }
}