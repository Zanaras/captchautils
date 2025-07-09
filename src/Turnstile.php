<?php

namespace CAPTCHAUtils;

class Turnstile {

	public string $apiEndpoint = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

	public function __construct(private string $secretKey) {
	}

	/**
	 * Takes a user submitted response and validates it against CloudFlare Turnstile API.
	 * Optioanlly, accepts a remoteIP and idempotency token.
	 * @param $response
	 * @param $remoteIP
	 * @param $idempotency
	 *
	 * @return array
	 */
	public function verify($response, $remoteIP = null, $idempotency = null): array {
		$curl = curl_init($this->apiEndpoint);
		$data = ['secret' => $this->secretKey, 'response' => $response];
		if ($remoteIP) {
			$data['remoteip'] = $remoteIP;
		}
		if ($idempotency) {
			$data['idempotency_key'] = $idempotency;
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
