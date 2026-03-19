<?php
/**
 * Marketing 360 API connection class
 */
// error_reporting(E_ALL & ~E_WARNING); // Report all errors except warnings
// ini_set('display_errors', 1); // Ensure errors are displayed
class Marketing_360_Payments
{
    /**
     * Marketing 360 Public API Endpoint
     * @var string
     */
    public const MARKETING_360_PAYMENTS_URL = 'https://payments.marketing360.com';

    // Marketing 360 Payments API Version.
    public const VER = 'v1';

    // Stripe API Version.
    public const STRIPE_VER = 'v1';

    // Integration ID for WooCommerce Source.
    public const INTEGRATION_ID = 'ed55cc8082d445b3961a41f8ba9403a8';

    // Marketing 360 Authentication Endpoint.
    private static $auth_url = 'https://login.marketing360.com/auth/realms/marketing360/protocol/openid-connect/token';

    // Marketing 360 Accounts List Endpoint.
    private static $accounts_url = 'https://app.marketing360.com/api/accounts';

    /**
     * Length of time to cache the Bearer Token in seconds
     * 240 = 4min
     * @var integer
     */
    public const TOKEN_EXPIRATION_LENGTH_IN_SECONDS = 240;

    /**
     * Webhook Events
     * @var array
     */
    private static $webhook_events = [
        "payments.charge.captured",
        "payments.charge.dispute.created",
        "payments.charge.expired",
        "payments.charge.failed",
        "payments.charge.pending",
        "payments.charge.refund.updated",
        "payments.charge.refunded",
        "payments.charge.succeeded",
        "payments.charge.updated",
        "payments.payment_intent.*",
        "payments.review.*",
        "payments.source.canceled",
        "payments.source.chargeable"
    ];

    /**
     * M360 Account Number
     * @var string
     */
    private static $m360_account = '';

    /**
     * M360 API Client ID
     * @var string
     */
    private static $m360_client_id = '';

    /**
     * M360 API Client Secret
     * @var string
     */
    private static $m360_client_secret = '';

    // Get/regenerate the Marketing 360 Client Token.
    public static function get_client_token()
    {
        $time = time();
        $token = get_option('m360_client_token');
        $token_expiration = get_option('m360_client_token_expiration');

        // Check for cached token and expiration and return it if it's still good
        if(
            $token &&
            $token_expiration &&
            $time < $token_expiration
        ) {
            return $token;
        } else {
            $client_id = self::get_client_id();
            $client_secret = self::get_client_secret();

            $token = self::id_secret_get_access_token($client_id, $client_secret);

            if (is_wp_error($token)) {
                $token = null;
            } else {
                self::set_client_token($token);
            }
        }
        return $token;
    }

    // Set the Marketing 360 Client Token and the new expiration date.
    public static function set_client_token($token)
    {
        update_option('m360_client_token', $token);
        update_option('m360_client_token_expiration', time() + self::TOKEN_EXPIRATION_LENGTH_IN_SECONDS);
    }

     // Gets the full Account Details object.
    public static function get_account_details()
    {
        $stripe_settings = get_option('woocommerce_stripe_settings');
    
    // Check if the option exists and is not false
    if ($stripe_settings && is_array($stripe_settings) && isset($stripe_settings['account_details'])) {
        error_log(json_encode($stripe_settings));
        return json_decode($stripe_settings['account_details']);
    }

    // Return null or an appropriate default value if the option is not set
    return null;
    }

    // Overwrites the account details object
    public static function set_account_details($account_details)
    {
        update_option('woocommerce_stripe_account_details', json_encode($account_details));
    }

    // Get the Marketing 360 Account ID from the full account details object.
    public static function get_account()
    {
        if (self::get_account_details()) {
            return self::get_account_details()->accountNumber;
        } else {
            return "";
        }
    }

    // Get the Marketing 360 Client ID from the full account details object.
    public static function get_client_id()
    {
        if (self::get_account_details()) {
            return self::get_account_details()->client_id;
        } else {
            return "";
        }
    }

    // Get the Marketing 360 Client Secret from the full account details object.
    public static function get_client_secret()
    {
        if (self::get_account_details()) {
            return self::get_account_details()->client_secret;
        } else {
            return "";
        }
    }

    // Get the Stripe Account ID from the full account details object.
    public static function get_stripe_id()
    {
        return self::get_account_details() ? self::get_account_details()->stripeAccountId : "";
    }

    // Get the Stripe Key from the full account details object.
    public static function get_stripe_key()
    {
        return self::get_account_details() ? self::get_account_details()->stripeKey : "";
    }

    // Construct the API route.
    public static function get_route($resource)
    {
        return self::get_payments_url() . '/' . self::VER . '/stripe/' . self::STRIPE_VER . '/' . $resource;
    }

    // Helper function for getting the M360 Payments API Endpoint.
    private static function get_payments_url()
    {
        return self::MARKETING_360_PAYMENTS_URL;
    }

    // Return the Bearer Token for request.
    public static function get_authorization()
    {
        return self::get_client_token();
    }

    // Setup the request headers for requests to Marketing 360 Payments API.
    public static function get_m360_payments_request_headers($token = false, $account = false)
    {
        if (!$token) {
            $token = self::get_authorization();
        }

        $account = ($account) ? $account : self::get_account();

        return [
            'Authorization'					=> 'Bearer ' . $token,
            'Marketing360-Account'			=> $account,
            'Marketing360-Payments-Source'	=> self::INTEGRATION_ID
        ];
    }

    // Generate an M360 Access token by using M360 Account Credentials.
    public static function username_password_get_access_token($username = false, $password = false)
    {
        $response = wp_remote_post(
            self::$auth_url,
            [
                'method'      => 'POST',
                'timeout'     => 45,
                'headers'     => [
                    'Content-Type' => 'application/x-www-form-urlencoded'
                ],
                'body'        => [
                    'username' => $username,
                    'password' => $password,
                    'grant_type' => 'password',
                    'client_id' => 'woocommerce_payments'
                ],
            ]
        );

        $response_code = $response['response']['code'];

        if ($response_code !== 200) {
            $error_message = $response['response']['message'];
            return new WP_Error($response_code, $error_message);
        } else {
            $result = json_decode($response['body']);
            return $result->access_token;
        }
    }

    // Generate an M360 Access token by using a client ID and Secret.
    public static function id_secret_get_access_token($client_id = "", $client_secret = "")
    {
        $basic_auth = base64_encode("{$client_id}:{$client_secret}");
        $response = wp_remote_post(
            self::$auth_url,
            [
                'method'      => 'POST',
                'timeout'     => 45,
                'headers'     => [
                    'Authorization' => "Basic {$basic_auth}"
                ],
                'body'        => [
                    'grant_type' => 'client_credentials'
                ],
            ]
        );

        $response_code = $response['response']['code'];

        if ($response_code !== 200) {
            $error_message = $response['response']['message'];
            return new WP_Error($response_code, $error_message);
        } else {
            $result = json_decode($response['body']);
            return $result->access_token;
        }
    }

    // Get the list of authorized M360 accounts using the token.
    public static function get_m360_accounts($token)
    {
        $response = wp_remote_post(
            self::$accounts_url,
            [
                'method'	=> 'GET',
                'headers'	=> [
                    'Authorization' => "Bearer {$token}"
                ],
                'body'		=> [
                    'limit' => 999
                ]
            ]
        );

        if (is_wp_error($response)) {
            return new WP_Error(500, $response->get_error_message());
        }

        $response_code = $response['response']['code'];

        if ($response_code !== 200) {
            $error_message = $response['response']['message'];
            return new WP_Error($response_code, $error_message);
        } else {
            $accounts = json_decode($response['body'])->response;
            return $accounts;
        }
    }

    // Get the details for the M360 Account using the token.
    public static function get_m360_account_details($token, $account)
    {
        $response = wp_remote_post(
            self::get_payments_url(). '/' . self::VER . '/api/integrations/' . self::INTEGRATION_ID,
            [
                'method'	=> 'PUT',
                'headers'	=> [
                    'Authorization'					=> 'Bearer ' . $token,
                    'Content-Length' 				=> 0,
                    'marketing360-account'			=> $account,
                ]
            ]
        );

        if (is_wp_error($response)) {
            return $response->get_error_message();
        }

        $response_code = $response['response']['code'];

        if ($response_code !== 201) {
            $error_message = $response['response']['message'];
            return new WP_Error($response_code, $error_message);
        } else {
            $account_details = json_decode($response['body']);
            return $account_details;
        }
    }

    // The REST Endpoint function to return a list of M360 Accounts the provided credentials are authorized for.
    public static function rest_list_m360_accounts(WP_REST_Request $request)
    {
        $username = $request['username'];
        $password = $request['password'];

        $token = self::username_password_get_access_token($username, $password);

        if (is_wp_error($token)) {
            http_response_code($token->get_error_code());
            die($token->get_error_message());
        } else {
            $accounts = self::get_m360_accounts($token);

            if (is_wp_error($accounts)) {
                http_response_code($accounts->get_error_code());
                die($accounts->get_error_message());
            }

            if ($accounts) {
                foreach($accounts as $index => &$account) {
                    $details = self::get_m360_account_details($token, $account->accountNumber);

                    if (is_wp_error($details) || isset($account->details->errors)) {
                        unset($accounts[$index]);
                        continue;
                    }

                    if (isset($details->clientId)) {
                        $account->client_id = $details->clientId;
                    }
                    if (isset($details->secret)) {
                        $account->client_secret = $details->secret;
                    }
                    
                    $account->payload = json_encode($account);

                    ob_start(); ?>
<div class="m360-account">
  <?php if ($account->accountIcon): ?>
  <div class="m360-account-icon">
    <img src="<?php echo $account->accountIcon; ?>">
  </div>
  <?php endif; ?>
  <div class="m360-account-info">
    <h2 class="display-name"><?php echo $account->displayName; ?></h2>
    <h3 class="account-number"><?php echo $account->externalAccountNumber; ?></h3>
  </div>
</div>
<?php $account->html = ob_get_clean();
                }
            }

            return array_values($accounts);
        }
    }

    // Get the Stripe details for the M360 Account using Account Number, ID, and Secret
    public static function get_stripe_details($client_id, $client_secret, $account)
    {
        $token = self::id_secret_get_access_token($client_id, $client_secret);

        if (is_wp_error($token)) {
            http_response_code($token->get_error_code());
            die($token->get_error_message());
        } else {
            $response = wp_remote_post(
                self::get_payments_url(). '/' . self::VER . '/api/account',
                [
                    'method'      => 'GET',
                    'timeout'     => 45,
                    'headers'     => self::get_m360_payments_request_headers($token, $account),
                ]
            );

            $response_code = $response['response']['code'];

            if ($response_code !== 200) {
                return new WP_Error($response_code, $response['response']['message']);
            } else {
                $result = json_decode($response['body']);
                return $result;
            }
        }
    }

    // Callback to add the Stripe details to the M360 Account details after clicking "Save Changes" in the Payment Gateway Settings Screen
    public static function add_stripe_details_callback($settings)
    {
        if (array_key_exists('account_details', $settings)) {
            $str = substr($settings['account_details'], 1, -1);
            $u_settings = json_decode($settings['account_details']);
            error_log($settings['account_details']);
            if (!is_null($u_settings)) {
                $stripe_details = self::get_stripe_details(
                    $u_settings->client_id,
                    $u_settings->client_secret,
                    $u_settings->accountNumber
                );

                $u_settings->stripeAccountId = $stripe_details->stripeAccountId;
                $u_settings->stripeKey = $stripe_details->stripeKey;

                $settings['account_details'] = json_encode($u_settings);
            }
        }
        error_log(json_encode($settings));

        return $settings;
    }
}