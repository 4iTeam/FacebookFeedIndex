<?php
namespace App\Model\Token;
class JWT
{
	/**
	 * @param string      $jwt    The JWT
	 * @param string|null $key    The secret key
	 * @param bool        $verify Don't skip verification process
	 *
	 * @return object|bool The JWT's payload as a PHP object
	 */
	public static function decode($jwt, $key = null, $verify = true)
	{
		$tks = explode('.', $jwt);
		if (count($tks) != 3) {
			return false;
		}
		list($headb64, $payloadb64, $cryptob64) = $tks;
		if (null === ($header = JWT::jsonDecode(JWT::urlsafeB64Decode($headb64)))
		) {
			return false;
		}
		if (null === $payload = JWT::jsonDecode(JWT::urlsafeB64Decode($payloadb64))
		) {
			return false;
		}

		return $payload;
	}

	/**
	 * @param string $input JSON string
	 *
	 * @return object Object representation of JSON string
	 */
	public static function jsonDecode($input)
	{
		$obj = json_decode($input);
		if (function_exists('json_last_error') && $errno = json_last_error()) {
			return null;
		}
		else if ($obj === null && $input !== 'null') {
			return null;
		}
		return $obj;
	}

	/**
	 * @param string $input A base64 encoded string
	 *
	 * @return string A decoded string
	 */
	public static function urlsafeB64Decode($input)
	{
		$remainder = strlen($input) % 4;
		if ($remainder) {
			$padlen = 4 - $remainder;
			$input .= str_repeat('=', $padlen);
		}
		return base64_decode(strtr($input, '-_', '+/'));
	}
}