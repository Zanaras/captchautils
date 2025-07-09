<?php

namespace CAPTCHAUtils;

class hCaptcha {

	public $apiEndpoint = 'https://api.hcaptcha.com/siteverify';

	public function __construct(private string $secretKey) {
	}

	/**
	 * Takes a user submitted response and validates it against the hCaptcha API.
	 * @param $response
	 * @param $remoteIP
	 *
	 * @return array|string[]
	 */
	public function verify($response, $remoteIP = null, $siteKey = null): array {
		$curl = curl_init($this->apiEndpoint);
		$data = ['secret' => $this->secretKey, 'response' => $response];
		if ($remoteIP) {
			$data['remoteip'] = $remoteIP;
		}
		if ($siteKey) {
			$data['sitekey'] = $siteKey;
		}
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($curl);
		curl_close($curl);
		if ($response) {
			$json = json_decode($response, true);
			if ($json['success']) {
				return [true, $json];
			} else {
				return [false, $json];
			}
		} else {
			return ['error'];
		}
	}
}
