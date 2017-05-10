<?php
/**
 * This class is built on top of the Crypto php-encryption library found here:
 * https://github.com/defuse/php-encryption 
 * We like it because of how well tested it is
 */
namespace SCRMHub\Framework\Utility;

use Crypto;
use Ex\CryptoTestFailedException;
use Ex\CannotPerformOperationException;
use InvalidCiphertextException;
use Exception;

use SCRMHub\Framework\Utility\App;


/**
 * Example use
 * $ciphertext 		= $this->app->encrypto->encrypt("Test message to encode");
 * $deciphertext 	= $this->app->encrypto->decrypt($ciphertext);
 */

class EncryptDecrypt {
	/**
	 * @var $app SCRM Hub app object
	 */
	private
		$app;

	/**
	 * @var $key The encrypton key
	 */
	private
		$key;


	/**
	 * @var $salt The salt added to strings for extra security
	 */
	private
		$salt;

	/**
	 * 
	 * @param string $salt 	The salt to append to the string for extra protection
	 * @param string $key 	The encryption key. It might be null as this could be generating the key
	 */
	function __construct(App $app, $salt = null, $key = null) {
		$this->app 	= $app;
		$this->salt = $salt;
		$this->key 	= substr($key, 0, 16);
	}


	/**
	 * Get the encryption key
	 * Needed for auto updates
	 */
	function getKey() {
		return $this->key;
	}

	/** 
	 * Gerneate a cipher key
	 * @return string A strong cipher key
	 */
	function generateKey() {
		try {
		    $encrypt_key = Crypto::createNewRandomKey();
		    // WARNING: Do NOT encode $key with bin2hex() or base64_encode(),
		    // they may leak the key to the attacker through side channels.
		} catch (CryptoTestFailedException $CryptoEx) {
			$this->app->logger->error('Cannot safely create a key');
		    die('Cannot safely create a key');
		} catch (CannotPerformOperationException $CryptoEx) {
			$this->app->logger->error('Cannot safely create a key operation');
		    die('Cannot safely create a operation');
		}

		if(empty($encrypt_key)) {
			$this->app->logger->error("The encryption key is empty");
			throw new Exception("The encryption key is empty");
			die();
		}

		return $encrypt_key;
	} 

	/**
	 * encrypt a cipher string
	 * @param string $message 	The string to be encoded
	 * @return $ciphertext 		The encoded string
	 */
	function encrypt($message) {
		try {
			$message .= $this->salt;
			$ciphertext = Crypto::Encrypt($message, $this->key);
		} catch (CryptoTestFailedException $ex) {
			$this->app->logger->error('Crypto test failed'."\n".print_r($ex, true));
			die('Cannot safely perform encryption');
		} catch (CannotPerformOperationException $ex) {
			$this->app->logger->error('Crypto cannot perform encryption operation'."\n".print_r($ex, true));
			die('Cannot safely perform decryption');
		} catch(Exception $ex) {			
			$this->app->logger->error('Crypto threw an unknown error'."\n".print_r($ex, true));
			die('An error has occured');
		}

		return $ciphertext;
	}

	/**
	 * Decrypt a cipher string
	 * @param string $ciphertext 	The string to be decoded
	 * @return $decrypted 			The decoded string
	 */
	function decrypt($ciphertext) {
		try {
			$decrypted = Crypto::Decrypt($ciphertext, $this->key);
		} catch (InvalidCiphertextException $ex) {
			// VERY IMPORTANT
			// Either:
			//   1. The ciphertext was modified by the attacker,
			//   2. The key is wrong, or
			//   3. $ciphertext is not a valid ciphertext or was corrupted.
			// Assume the worst.
			$this->app->logger->error('Crypto decrypt: Invalid cipher error. Has it been tampered with?'."\n".print_r($ex, true));
			return false;
		} catch (CryptoTestFailedException $ex) {
			$this->app->logger->error('Crypto decrypt: Test failed'."\n".print_r($ex, true));
			return false;
		} catch (CannotPerformOperationException $ex) {
			$this->app->logger->error('Crypto decrypt: Operation failed'."\n".print_r($ex, true));
			return false;
		} catch(Exception $ex) {
			$this->app->logger->error('Crypto decrypt: General error'."\n".print_r($ex, true));
			return false;
		}

		//strip the salt
		$decrypted = str_replace($this->salt, '', $decrypted);

		//Return the string
		return $decrypted;
	}
}