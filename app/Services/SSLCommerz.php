<?php

/** @noinspection PhpUnused */

namespace App\Services;

use App\Traits\SSCLZ\SSLCommerzRequestField;
use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Fluent;

class SSLCommerz
{
    use SSLCommerzRequestField;

    protected $config = [];

    protected $primary = [];

    protected $emi = [];

    protected $customer_information = [];

    protected $shipment_information = [];

    protected $product_information = [];

    protected $additional_information = [];

    protected $api_url = null;

    protected $payment_display_type = null;

    protected $is_production = false;

    protected $tran_id = null;

    protected $response;

    protected $api_domain = null;

    protected $trans_status_api_url = null;

    protected $order_validate_api_url = null;

    protected $refund_payment_api_url = null;

    protected $refund_status_api_url = null;

    protected $api_env = null;

    public static $_ENV_SANDBOX = 'sandbox';

    public static $_ENV_PRODUCTION = 'production';

    public static $_API_DOMAIN = 'api_domain';

    public static $_API_URL = 'api_url';

    public static $_API_INIT_PAYMENT = 'init_payment';

    public static $_API_TRANSACTION_STATUS = 'transaction_status';

    public static $_API_ORDER_VALIDATE = 'order_validate';

    public static $_API_REFUND_PAYMENT = 'refund_payment';

    public static $_API_REFUND_STATUS = 'refund_status';

    public static $_PAYMENT_DISPLAY_HOSTED = 'hosted';

    public static $_PAYMENT_DISPLAY_CHECKOUT = 'checkout';

    public static $_CONFIG = 'sslcommerz';

    public static $_STORE_ID = 'store_id';

    public static $_STORE_PASSWORD = 'store_password';

    public static $_IS_PRODUCTION = 'is_production';

    public function __construct($config = [])
    {
        $this->config = config(self::$_CONFIG);
        $this->primary[self::$_STORE_ID] = $this->config[self::$_STORE_ID];
        $this->primary[self::$_STORE_PASSWORD] = $this->config[self::$_STORE_PASSWORD];
        $this->is_production = !empty($config[self::$_IS_PRODUCTION]) ? $config[self::$_IS_PRODUCTION] : $this->config[self::$_IS_PRODUCTION];
        $this->api_env = $this->is_production ? self::$_ENV_PRODUCTION : self::$_ENV_SANDBOX;
        $this->api_domain = $this->config[self::$_API_DOMAIN][$this->api_env];
        $this->triggerUpdateApiUrls();
    }

    public function getApiUrl()
    {
        return $this->api_url;
    }

    public function getTranId()
    {
        return $this->tran_id;
    }

    public function setTranId($id): bool
    {
        $this->tran_id = $id;

        return true;
    }

    public function setApiUrl(string $url): bool
    {
        $this->api_url = $url;

        return true;
    }

    public function isProductionMode(): bool
    {
        return $this->is_production;
    }

    public function setProductionMode(bool $is_production): bool
    {
        $this->is_production = $is_production;
        $this->api_env = $this->is_production ? self::$_ENV_PRODUCTION : self::$_ENV_SANDBOX;
        $this->setApiEnvironment($this->api_env);

        return true;
    }

    public function getPaymentDisplayType(): ?string
    {
        return $this->payment_display_type;
    }

    public function setPaymentDisplayType(string $type)
    {
        $this->payment_display_type = $type;
    }

    //    Primary Information
    public function getPrimaryInformation(): array
    {
        return $this->primary;
    }

    public function setPrimaryInformation(array $data): bool
    {
        $validated = $this->validateInformation($data, 'primary');

        if ($validated['status'] == 'fail') {
            throw new Exception($validated['message']);
        }

        foreach ($data as $field => $value) {
            $this->primary[$field] = $value;
        }

        return true;
    }

    public function getStoreId()
    {
        return $this->primary['store_id'] ?? null;
    }

    public function setStoreId(string $store_id): bool
    {
        $this->primary['store_id'] = $store_id;

        return true;
    }

    public function getStorePassword()
    {
        return $this->primary['store_password'] ?? null;
    }

    public function setStorePassword(string $store_password): bool
    {
        $this->primary['store_password'] = $store_password;

        return true;
    }

    public function getTotalAmount()
    {
        return $this->primary['total_amount'] ?? null;
    }

    public function setTotalAmount($amount): bool
    {
        $this->primary['total_amount'] = $amount;

        return true;
    }

    public function getCurrency()
    {
        return $this->primary['currency'] ?? null;
    }

    public function setCurrency(string $currency): bool
    {
        $this->primary['currency'] = $currency;

        return true;
    }

    public function getSuccessUrl()
    {
        return $this->primary['success_url'] ?? null;
    }

    public function setSuccessUrl(string $url): bool
    {
        $this->primary['success_url'] = $url;

        return true;
    }

    public function getFailUrl()
    {
        return $this->primary['fail_url'] ?? null;
    }

    public function setFailUrl(string $url): bool
    {
        $this->primary['fail_url'] = $url;

        return true;
    }

    public function getCancelUrl()
    {
        return $this->primary['cancel_url'] ?? null;
    }

    public function setCancelUrl(string $url): bool
    {
        $this->primary['cancel_url'] = $url;

        return true;
    }

    public function getIpnUrl()
    {
        return $this->primary['ipn_url'] ?? null;
    }

    public function setIpnUrl(string $url): bool
    {
        $this->primary['ipn_url'] = $url;

        return true;
    }

    public function getMultiCardName()
    {
        return $this->primary['multi_card_name'] ?? null;
    }

    public function setMultiCardName(string $name): bool
    {
        $this->primary['multi_card_name'] = $name;

        return true;
    }

    public function getAllowedBin()
    {
        return $this->primary['allowed_bin'] ?? null;
    }

    public function setAllowedBin(string $name): bool
    {
        $this->primary['allowed_bin'] = $name;

        return true;
    }

    //    EMI Information
    public function getEmiInformation(): array
    {
        return $this->emi;
    }

    public function setEmiInformation(array $data): bool
    {
        $validated = $this->validateInformation($data, 'emi');

        if ($validated['status'] == 'fail') {
            throw new Exception($validated['message']);
        }

        foreach ($data as $field => $value) {
            $this->emi[$field] = $value;
        }

        return true;
    }

    public function getEmiOption()
    {
        return $this->emi['emi_option'] ?? null;
    }

    public function setEmiOption(int $option): bool
    {
        $this->emi['emi_option'] = $option;

        return true;
    }

    //    Customer Information
    public function getCustomerInformation(): array
    {
        return $this->customer_information;
    }

    public function setCustomerInformation(array $data): bool
    {
        $validated = $this->validateInformation($data, 'customer_information');

        if ($validated['status'] == 'fail') {
            throw new Exception($validated['message']);
        }

        foreach ($data as $field => $value) {
            $this->customer_information[$field] = $value;
        }

        return true;
    }

    public function getCustomerName()
    {
        return $this->customer_information['cus_name'] ?? null;
    }

    public function setCustomerName(string $name): bool
    {
        $this->customer_information['cus_name'] = $name;

        return true;
    }

    public function getCustomerEmail()
    {
        return $this->customer_information['cus_email'] ?? null;
    }

    public function setCustomerEmail(string $name): bool
    {
        $this->customer_information['cus_email'] = $name;

        return true;
    }

    public function getCustomerAddress1()
    {
        return $this->customer_information['cus_add1'] ?? null;
    }

    public function setCustomerAddress1(string $name): bool
    {
        $this->customer_information['cus_add1'] = $name;

        return true;
    }

    public function getCustomerAddress2()
    {
        return $this->customer_information['cus_add2'] ?? null;
    }

    public function setCustomerAddress2(string $name): bool
    {
        $this->customer_information['cus_add2'] = $name;

        return true;
    }

    public function getCustomerCity()
    {
        return $this->customer_information['cus_city'] ?? null;
    }

    public function setCustomerCity(string $name): bool
    {
        $this->customer_information['cus_city'] = $name;

        return true;
    }

    public function getCustomerState()
    {
        return $this->customer_information['cus_state'] ?? null;
    }

    public function setCustomerState(string $name): bool
    {
        $this->customer_information['cus_state'] = $name;

        return true;
    }

    public function getCustomerPostCode()
    {
        return $this->customer_information['cus_postcode'] ?? null;
    }

    public function setCustomerPostCode(string $name): bool
    {
        $this->customer_information['cus_postcode'] = $name;

        return true;
    }

    public function getCustomerCountry()
    {
        return $this->customer_information['cus_country'] ?? null;
    }

    public function setCustomerCountry(string $name): bool
    {
        $this->customer_information['cus_country'] = $name;

        return true;
    }

    public function getCustomerPhone()
    {
        return $this->customer_information['cus_phone'] ?? null;
    }

    public function setCustomerPhone(string $name): bool
    {
        $this->customer_information['cus_phone'] = $name;

        return true;
    }

    public function getCustomerFax()
    {
        return $this->customer_information['cus_fax'] ?? null;
    }

    public function setCustomerFax(string $name): bool
    {
        $this->customer_information['cus_fax'] = $name;

        return true;
    }

    //    Shipment Information
    public function getShipmentInformation(): array
    {
        return $this->shipment_information;
    }

    public function setShipmentInformation(array $data): bool
    {
        $validated = $this->validateInformation($data, 'shipment_information');

        if ($validated['status'] == 'fail') {
            throw new Exception($validated['message']);
        }

        foreach ($data as $field => $value) {
            $this->shipment_information[$field] = $value;
        }

        return true;
    }

    public function getShippingMethod()
    {
        return $this->shipment_information['shipping_method'] ?? null;
    }

    public function setShippingMethod(string $method): bool
    {
        $this->shipment_information['shipping_method'] = $method;

        return true;
    }

    //    Product Information
    public function getProductInformation(): array
    {
        return $this->product_information;
    }

    public function getProductName()
    {
        return $this->product_information['product_name'] ?? null;
    }

    public function setProductName(string $name): bool
    {
        $this->product_information['product_name'] = $name;

        return true;
    }

    public function getProductCategory()
    {
        return $this->product_information['product_category'] ?? null;
    }

    public function setProductCategory(string $name): bool
    {
        $this->product_information['product_category'] = $name;

        return true;
    }

    public function getProductProfile()
    {
        return $this->product_information['product_profile'] ?? null;
    }

    public function setProductProfile(string $name): bool
    {
        $this->product_information['product_profile'] = $name;

        return true;
    }

    //    v2
    public function getApiEnvironment()
    {
        return $this->api_env;
    }

    public function setApiEnvironment(string $environment): bool
    {
        if ($environment == self::$_ENV_SANDBOX || $environment == self::$_ENV_PRODUCTION) {
            $this->api_env = $environment;
            $this->api_domain = $this->config[self::$_API_DOMAIN][$this->api_env];
            $this->triggerUpdateApiUrls();

            return true;
        }

        return false;
    }

    public function getApiDomain()
    {
        return $this->api_domain;
    }

    public function getTransactionStatusApiUrl()
    {
        return $this->trans_status_api_url;
    }

    public function setTransactionStatusApiUrl(string $url): bool
    {
        $this->trans_status_api_url = $url;

        return true;
    }

    public function getOrderValidateApiUrl()
    {
        return $this->order_validate_api_url;
    }

    public function setOrderValidateApiUrl(string $url): bool
    {
        $this->order_validate_api_url = $url;

        return true;
    }

    public function getRefundPaymentApiUrl()
    {
        return $this->refund_payment_api_url;
    }

    public function setRefundPaymentApiUrl(string $url): bool
    {
        $this->refund_payment_api_url = $url;

        return true;
    }

    public function getRefundStatusApiUrl()
    {
        return $this->refund_status_api_url;
    }

    public function setRefundStatusApiUrl(string $url): bool
    {
        $this->refund_status_api_url = $url;

        return true;
    }

    protected function triggerUpdateApiUrls()
    {
        $this->api_url = $this->api_domain . $this->config[self::$_API_URL][self::$_API_INIT_PAYMENT];
        $this->trans_status_api_url = $this->api_domain . $this->config[self::$_API_URL][self::$_API_TRANSACTION_STATUS];
        $this->order_validate_api_url = $this->api_domain . $this->config[self::$_API_URL][self::$_API_ORDER_VALIDATE];
        $this->refund_payment_api_url = $this->api_domain . $this->config[self::$_API_URL][self::$_API_REFUND_PAYMENT];
        $this->refund_status_api_url = $this->api_domain . $this->config[self::$_API_URL][self::$_API_REFUND_STATUS];
        $this->payment_display_type = self::$_PAYMENT_DISPLAY_HOSTED;
    }

    public function validateInformation(array $data, $info_type): array
    {
        if (empty($data)) {
            return $this->response = [
                'status'  => 'fail',
                'message' => 'Invalid data.'
            ];
        }

        foreach ($data as $field => $value) {
            if (!is_string($field)) {
                return $this->response = [
                    'status'  => 'fail',
                    'message' => 'Invalid field.'
                ];
            }

            if (!in_array($field, $this->required_fields[$info_type])
                && !in_array($field, $this->optional_fields[$info_type])) {
                return $this->response = [
                    'status'  => 'fail',
                    'message' => 'Field `' . $field . '` is not valid.'
                ];
            }

            if (in_array($field, $this->required_fields[$info_type]) && empty($value)) {
                return $this->response = [
                    'status'  => 'fail',
                    'message' => 'Value of `' . $field . '` can not be empty.'
                ];
            }
        }

        return $this->response = [
            'status'  => 'success',
            'message' => 'All ok.'
        ];
    }

    public function initPayment(object $data)
    {
        $post_data = [];

        //        Primary Information
        if (!empty($data->primary)) {
            foreach ($data->primary as $field => $value) {
                $post_data[$field] = $value;
            }
        }

        $post_data['store_passwd'] = $post_data['store_password'];
        unset($post_data['store_password']);
        $post_data['tran_id'] = $data->tran_id;

        //        Customer Information
        if (!empty($data->customer_information)) {
            foreach ($data->customer_information as $field => $value) {
                $post_data[$field] = $value;
            }
        }

        //        Shipment Information
        if (!empty($data->shipment_information)) {
            foreach ($data->shipment_information as $field => $value) {
                $post_data[$field] = $value;
            }
        }

        //        Product Information
        if (!empty($data->product_information)) {
            foreach ($data->product_information as $field => $value) {
                $post_data[$field] = $value;
            }
        }

        //        Make API Request
        $response = $this->initPaymentApiRequest($post_data);

        return json_decode($response, true);
    }

    public static function validate($val_id): Fluent
    {
        $response = (new SSLCommerz())->orderValidate([
            'val_id' => $val_id
        ]);

        return new Fluent($response);
    }

    public function validateOrderParams(array $data): bool
    {
        if (empty($data) || empty($data['val_id'])) {
            return false;
        }

        return true;
    }

    public function orderValidate(array $data): array
    {
        if (!$this->validateOrderParams($data)) {
            return $this->response = [
                'status'  => 'FAIL',
                'message' => 'Please provide valid val_id or post request data'
            ];
        }

        $data['store_id'] = isset($data['store_id']) && !empty($data['store_id'])
            ? $data['store_id'] : $this->getStoreId();
        $data['store_password'] = isset($data['store_password']) && !empty($data['store_password'])
            ? $data['store_password'] : $this->getStorePassword();
        $data['v'] = (isset($data['v']) && !empty($data['v'])) ? $data['v'] : '1';
        $data['format'] = (isset($data['format']) && !empty($data['format'])) ? $data['format'] : 'json';

        $response = $this->orderValidateApiRequest($data);

        return json_decode($response, true);
    }

    public function orderValidateApiRequest($data): string
    {
        $query_params = [
            'val_id'       => $data['val_id'],
            'store_id'     => $data['store_id'],
            'store_passwd' => $data['store_password'],
            'v'            => $data['v'],
            'format'       => $data['format']
        ];

        $response = Http::get($this->getOrderValidateApiUrl(), $query_params);

        return $response->body();
    }

    public function redirect($url, $permanent = false)
    {
        header('Location: ' . $url, true, $permanent ? 301 : 302);
        exit();
    }

    public function initPaymentApiRequest($data): string
    {
        $response = Http::asForm()->post($this->getApiUrl(), $data);

        return $response->body();
    }
}
