<?php
/**
 * Opulence
 *
 * @link      https://www.opulencephp.com
 * @copyright Copyright (C) 2016 David Young
 * @license   https://github.com/opulencephp/Opulence/blob/master/LICENSE.md
 */
namespace Opulence\Cryptography\Encryption;

use Exception;

/**
 * Defines an encrypter
 */
class Encrypter implements IEncrypter
{
    /** @var array The list of approved ciphers */
    protected static $approvedCiphers = [
        "AES-128-CBC",
        "AES-192-CBC",
        "AES-256-CBC",
        "AES-128-CTR",
        "AES-192-CTR",
        "AES-256-CTR"
    ];
    /** @var string The encryption key */
    private $key = "";
    /** @var string The encryption cipher */
    private $cipher = "AES-128-CTR";

    /**
     * @param string $key The encryption key
     * @param string $cipher The encryption cipher
     */
    public function __construct(string $key, string $cipher = "AES-128-CTR")
    {
        $this->setKey($key);
        $this->setCipher($cipher);
    }

    /**
     * @inheritdoc
     */
    public function decrypt(string $data) : string
    {
        $pieces = $this->getPieces($data);

        if (!$this->macIsValid($pieces)) {
            throw new EncryptionException("Invalid MAC");
        }

        $pieces["iv"] = base64_decode($pieces["iv"]);

        try {
            $decryptedData = openssl_decrypt($pieces["value"], $this->cipher, $this->key, 0, $pieces["iv"]);

            if ($decryptedData === false) {
                throw new EncryptionException("Failed to decrypt data");
            }
        } catch (Exception $ex) {
            throw new EncryptionException("Failed to decrypt data", 0, $ex);
        }

        // In case the data was not a primitive, unserialize it
        return unserialize($decryptedData);
    }

    /**
     * @inheritdoc
     */
    public function encrypt(string $data) : string
    {
        $iv = random_bytes(openssl_cipher_iv_length($this->cipher));
        $encryptedValue = openssl_encrypt(serialize($data), $this->cipher, $this->key, 0, $iv);

        if ($encryptedValue === false) {
            throw new EncryptionException("Failed to encrypt the data");
        }

        $iv = base64_encode($iv);
        $mac = $this->createHash($iv, $encryptedValue);
        $pieces = [
            "iv" => $iv,
            "value" => $encryptedValue,
            "mac" => $mac
        ];

        return base64_encode(json_encode($pieces));
    }

    /**
     * @inheritdoc
     */
    public function setCipher(string $cipher)
    {
        $cipher = strtoupper($cipher);

        if (!in_array($cipher, self::$approvedCiphers)) {
            throw new EncryptionException("Invalid cipher \"$cipher\"");
        }

        $this->cipher = $cipher;
    }

    /**
     * @inheritdoc
     */
    public function setKey(string $key)
    {
        $this->key = $key;
    }

    /**
     * Creates a hash
     *
     * @param string $iv The initialization vector
     * @param string $value The value to hash
     * @return string The hash string
     */
    private function createHash(string $iv, string $value) : string
    {
        return hash_hmac("sha256", $iv . $value, $this->key);
    }

    /**
     * Converts the input data to an array of pieces
     *
     * @param string $data The JSON data to convert
     * @return array The pieces
     * @throws EncryptionException Thrown if the data was not valid JSON
     */
    private function getPieces(string $data) : array
    {
        $pieces = json_decode(base64_decode($data), true);

        if ($pieces === false || !isset($pieces["mac"]) || !isset($pieces["value"]) || !isset($pieces["iv"])) {
            throw new EncryptionException("Data is not in correct format");
        }

        return $pieces;
    }

    /**
     * Gets whether or not a MAC is valid
     *
     * @param array $pieces The pieces to validate
     * @return bool True if the MAC is valid, otherwise false
     */
    private function macIsValid(array $pieces) : bool
    {
        $randomBytes = bin2hex(random_bytes(8));
        $correctHmac = hash_hmac("sha256", $pieces["mac"], $randomBytes, true);
        $generatedHmac = hash_hmac("sha256", $this->createHash($pieces["iv"], $pieces["value"]), $randomBytes, true);

        return hash_equals($correctHmac, $generatedHmac);
    }
}