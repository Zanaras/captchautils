# CAPTCHAUtils
Set of PHP classes to reduce duplication of code used to verify various CAPTCHA-esque things (Cloudflare Turnstile and hCAPTCHA) that don't have their own maintained packages. Google's ReCAPTCHA is deliberately omitted since they publish their own PHP package.

ALTCHA support may be added when I can verify that their own libraries don't support their open source implementation of CATPCHA. 

## Requirements

* PHP 8.0+
* PHP JSON and CURL extensions.

## Installation

```composer install kagurati/captchautils```

## Usage
Both the Turnstile and hCaptcha classes work nearly identically, in that they both expect the client response string and optionally accept the user's IP for additional validation. Turnstile additonally also accepts an [idempotency key](https://developers.cloudflare.com/turnstile/get-started/server-side-validation/) which can be used to allow reuse of the response string while hCaptcha has support for sending the Site Key during server validation to prevent cross-site scripting attacks.

Both have verify functions that return a multidimensional array of the response pass/fail or, in case of a curl failure, 'error' as the first value. The entire validation response included as the second value if your application needs to analyze it further.

Psuedocode examples are below:
### Turnstile
```php
...
use CAPTCHAUtils\Turnstile;
...
private string $yourTunrstileSecretKey = '1234abcd';
...
public function checkThisUser($clientResponseString, $userIP = null, $idempotencyKey = null) {
        $check = new Turnstile($this->yourTurnstileSecretKey)->verify($clientResponseString, $userIP, $idempotencyKey);
        if ($check[0]) {
                # User is a real human!
        } else {
                # User probably isn't a real human.
                $errors = $check[1]['error-codes'];
        }
}
```
### hCaptcha
```php
...
use CAPTCHAUtils\hCaptcha;
...
private string $yourhCaptchaSecretKey = '1234abcd';
...
public function checkThisUser($clientResponseString, $userIP = null, $expectedSiteKey = null) {
        $check = new hCaptcha($this->yourhCaptchaSecretKey)->verify($clientResponseString, $userIP, $expectedSiteKey);
        if ($check[0]) {
                # User is a real human!
        } else {
                # User probably isn't a real human.
        }
}
```