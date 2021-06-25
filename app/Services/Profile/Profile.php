<?php
namespace App\Services\Profile;
use Illuminate\Support\Str;
class Profile {
	public $gender;
	public $firstName;
	public $middleName;
	public $lastName;
	public $fullName;

	function __construct( $data ) {
		$this->gender     = $data['g'];
		$this->firstName  = $data['f'];
		$this->middleName = $data['m'];
		$this->lastName   = $data['l'];
	}

	function getFullName() {
		if ( ! $this->fullName ) {
			$this->fullName = sprintf( '%s %s %s', $this->lastName, $this->middleName, $this->firstName );
		}

		return $this->fullName;
	}
	function getDisplayName(){
		return $this->getFullName();
	}

	function getGender(){
		return $this->gender;
	}
	function getUserName() {
		$username = Str::ascii( $this->firstName . mb_substr( $this->lastName, 0, 1 ) . mb_substr( $this->middleName, 0, 1 ) );
		$username = strtolower( $username );
		$username .= rand( 100, 999 );

		return $username;
	}

	function getFirstName($no_accents=false) {
		return $no_accents?Str::ascii($this->firstName):$this->firstName;
	}

	function getLastName($no_accents=false) {
		$return=$this->lastName . ' ' . $this->middleName;
		return $no_accents?Str::ascii($return):$return;
	}

	function getPassword( $length = 12, $special_chars = true, $extra_special_chars = false ) {

		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
		if ( $special_chars ) {
			$chars .= '!@#$%^&*()';
		}
		if ( $extra_special_chars ) {
			$chars .= '-_ []{}<>~`+=,.;:/?|';
		}

		$password = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$password .= substr( $chars, mt_rand( 0, strlen( $chars ) - 1 ), 1 );
		}

		/**
		 * Filters the randomly-generated password.
		 *
		 * @since 3.0.0
		 *
		 * @param string $password The generated password.
		 */
		return $password;
	}

	function __toString() {
		return $this->getFullName();
	}

	function getKey() {
		return md5( $this->getFullName() );
	}

	function valid() {
		return $this->middleName != $this->firstName;
	}
}

