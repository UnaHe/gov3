<?php

namespace app\Library;

class CryptModel
{
    const CIPHER = 'AES-256-CBC';
	const KEY = 'XQXzF/tWHah6glemo1FMTLDl9zUw0KMnALPhqQLqAfU=';
	const IV = '9BtQ9sF4VZfet+NAwrX4EA==';

	/**
	 * 加密
	 *
	 * @param string $plainText 未加密字符串
	 * @param string $key        密钥
	 * @return mixed
	 */
	public static function encrypt($plainText,$key)
	{
        $encryptedText = openssl_encrypt($plainText, self::CIPHER, base64_decode($key), OPENSSL_RAW_DATA, base64_decode(self::IV));

        return trim(base64_encode($encryptedText));
	}
	
    /**
     * 解密
     * 
     * @param string $encryptedText 已加密字符串
     * @param string $key  密钥
     * @return string
     */
    public static function decrypt($encryptedText,$key)
    {
        $encryptedText = base64_decode($encryptedText);

        $decryptedText = openssl_decrypt($encryptedText, self::CIPHER, base64_decode($key), OPENSSL_RAW_DATA, base64_decode(self::IV));

        return trim($decryptedText);
    }
}