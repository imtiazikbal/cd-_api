<?php

namespace App\Services;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Facades\Http;

class Nagad
{
    private string $key = 'nagad';

    private string $type = 'production';

    private $url;

    private $merchantId;

    private $accountNumber;

    private $public_key;

    private $private_key;

    public function __construct()
    {
        $this->url = config("services.{$this->key}.{$this->type}_url");
        $this->merchantId = config("services.{$this->key}.{$this->type}_merchantId");
        $this->accountNumber = config("services.{$this->key}.{$this->type}_merchant_number");
        $this->public_key = config("services.{$this->key}.{$this->type}_public_key");
        $this->private_key = config("services.{$this->key}.{$this->type}_private_key");
    }

    public function request(): PendingRequest
    {
        return Http::withHeaders([
            'X-Requested-With' => 'XMLHttpRequest',
            'accept'           => 'application/json',
            'content-type'     => 'application/json',
            'X-KM-Api-Version' => 'v-0.2.0',
            'X-KM-IP-V4'       => $this->getClientIp(),
            'X-KM-Client-Type' => 'PC_WEB'
        ])->asJson();
    }

    public function initPayment($data)
    {
        $invoice = 'Inv' . Date('YmdH') . random_int(1000, 10000);
        $sensitiveData = [
            'merchantId' => $this->merchantId,
            'datetime'   => Date('YmdHis'),
            'orderId'    => $invoice,
            'challenge'  => $this->generateRandomString()
        ];

        $postData = [
            'accountNumber' => $this->accountNumber, //optional
            'dateTime'      => Date('YmdHis'),
            'sensitiveData' => $this->EncryptDataWithPublicKey(json_encode($sensitiveData)),
            'signature'     => $this->SignatureGenerate(json_encode($sensitiveData))
        ];

        $res = $this->generatePaymentUrl($postData, $invoice);

        if (isset($res->sensitiveData, $res->signature)) {
            $decrypted = json_decode($this->DecryptDataWithPrivateKey($res->sensitiveData), false, 512, JSON_THROW_ON_ERROR);

            if (isset($decrypted->paymentReferenceId) && $decrypted->paymentReferenceId !== "" && isset($decrypted->challenge) && $decrypted->challenge !== "") {
                $sensitiveDataOrder = [
                    'merchantId'   => $this->merchantId,
                    'orderId'      => $invoice,
                    'currencyCode' => '050',
                    'amount'       => $data['amount'],
                    'challenge'    => $decrypted->challenge
                ];

                $data['tnx_id'] = random_int(00000, 99999);
                $PostDataOrder = [
                    'sensitiveData'          => $this->EncryptDataWithPublicKey(json_encode($sensitiveDataOrder, JSON_THROW_ON_ERROR)),
                    'signature'              => $this->SignatureGenerate(json_encode($sensitiveDataOrder, JSON_THROW_ON_ERROR)),
                    'merchantCallbackURL'    => route('payment.nagad.callback'),
                    'additionalMerchantInfo' => $data,
                    'additionalFieldNameEN'  => 'Order Type',
                    'additionalFieldNameBN'  => 'অর্ডার প্রকার',
                    'additionalFieldValue'   => $data['order_type']
                ];

                $order = $this->request()->post($this->url . 'api/dfs/check-out/complete/' . $decrypted->paymentReferenceId, $PostDataOrder);

                return data_get(json_decode($order->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR), 'callBackUrl');
            }
        }

        return 'something went wrong';

    }

    public function verifyPayment($payment_ref_id)
    {
        $response = $this->request()->get($this->url . 'api/dfs/verify/payment/' . $payment_ref_id);

        return json_decode($response->getBody()->getContents(), false, 512, JSON_THROW_ON_ERROR);
    }

    public function generatePaymentUrl($data, $invoice)
    {
        $response = $this->request()->post($this->url . 'api/dfs/check-out/initialize/' . $this->merchantId . '/' . $invoice, $data);

        return json_decode($response->getBody()->getContents());
    }

    public function generateRandomString(int $length = 40): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';

        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    public function EncryptDataWithPublicKey($data): string
    {
        $pgPublicKey = $this->public_key;
        $key = "-----BEGIN PUBLIC KEY-----\n" . $pgPublicKey . "\n-----END PUBLIC KEY-----";
        $public_key = openssl_get_publickey($key);
        openssl_public_encrypt($data, $encryption, $public_key, OPENSSL_PKCS1_PADDING);

        return base64_encode($encryption);
    }

    public function DecryptDataWithPrivateKey($data)
    {
        $merchantPrivateKey = $this->private_key;
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_private_decrypt(base64_decode($data), $plain_text, $private_key);

        return $plain_text;
    }

    public function SignatureGenerate($data): string
    {
        $merchantPrivateKey = $this->private_key;
        $private_key = "-----BEGIN RSA PRIVATE KEY-----\n" . $merchantPrivateKey . "\n-----END RSA PRIVATE KEY-----";
        openssl_sign($data, $signature, $private_key, OPENSSL_ALGO_SHA256);

        return base64_encode($signature);
    }

    public function getClientIp(): string
    {
        return match (true) {
            isset($_SERVER['HTTP_CLIENT_IP'])       => $_SERVER['HTTP_CLIENT_IP'],
            isset($_SERVER['HTTP_X_FORWARDED_FOR']) => $_SERVER['HTTP_X_FORWARDED_FOR'],
            isset($_SERVER['HTTP_X_FORWARDED'])     => $_SERVER['HTTP_X_FORWARDED'],
            isset($_SERVER['HTTP_FORWARDED_FOR'])   => $_SERVER['HTTP_FORWARDED_FOR'],
            isset($_SERVER['HTTP_FORWARDED'])       => $_SERVER['HTTP_FORWARDED'],
            isset($_SERVER['REMOTE_ADDR'])          => $_SERVER['REMOTE_ADDR'],
            default                                 => 'UNKNOWN',
        };
    }
}
