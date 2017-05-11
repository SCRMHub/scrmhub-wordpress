<?php
/**
 * This class is built on top of the Crypto php-encryption library found here:
 * https://github.com/defuse/php-encryption 
 * We like it because of how well tested it is
 */
namespace SCRMHub\Framework\Utility;

use Defuse\Crypto\Key;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Encoding;
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException;
use Defuse\Crypto\Exception as Ex;

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
		$rawKey,
		$key;

	/**
	 * 
	 * @param string $key 	The encryption key. It might be null as this could be generating the key
	 */
	function __construct(App $app, $key = null) {
		$this->app 	= $app;
		$this->rawKey = $key;
	}

	/**
	 * Check that the Crypto Library can run
	 */
	public function installTest() {
		$key  = Key::createNewRandomKey();
        $data = "EnCrYpT EvErYThInG\x00\x00";

        try {
        	// Make sure encrypting then decrypting doesn't change the message.
        	$ciphertext = Crypto::encrypt($data, $key, true);

        	//Did it come back
        	$decrypted = Crypto::decrypt($ciphertext, $key, true);
        } catch (Ex\WrongKeyOrModifiedCiphertextException $ex) {
            // It's important to catch this and change it into a
            // Ex\EnvironmentIsBrokenException, otherwise a test failure could trick
            // the user into thinking it's just an invalid ciphertext!
            return $ex;
        } catch(Exception $e) {
        	return $ex;
        }

        //Keys didn't match
        if ($decrypted !== $data) {
            return 'Keys could not be decoded properly';
        }
        
        return false;
	}


	/**
	 * Get the encryption key
	 */
	private function getKey() {
		if(empty($this->key)) {
	        $this->key = Key::loadFromAsciiSafeString(Encoding::saveBytesToChecksummedAsciiSafeString(
	            Key::KEY_CURRENT_VERSION,
	            substr($this->rawKey, 0, 32)
	        ));
		}

		return $this->key;
	}

	/**
	 * encrypt a cipher string
	 * @param string $message 	The string to be encoded
	 * @return $ciphertext 		The encoded string
	 */
	function encrypt($message) {
		//Encrypt it
		$ciphertext = Crypto::encrypt($message, $this->getKey());

		//
		return $ciphertext;
	}

	/**
	 * Decrypt a cipher string
	 * @param string $ciphertext 	The string to be decoded
	 * @return $decrypted 			The decoded string
	 */
	function decrypt($ciphertext) {
		try {
			//decrypt it
			$decrypted = Crypto::Decrypt($ciphertext, $this->getKey());

			return $decrypted;
		} catch (WrongKeyOrModifiedCiphertextException $ex) {
		    $this->app->logger->error('Crypto decrypt: Invalid cipher error. Has it been tampered with?'."\n".print_r($ex, true));
			return false;
		}
	}
}