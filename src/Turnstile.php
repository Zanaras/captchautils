<?php

namespace CAPTCHAUtils;

class Turnstile {

	public $apiEndpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

	public function __construct(private string $secretKey) {
	}

	public function verify($response, $remoteIP = null): array {
		$curl = curl_init($this->apiEndpoint);
		$data = ['secret' => $this->secretKey, 'response' => $response];
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
