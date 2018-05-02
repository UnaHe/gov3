<?php

namespace app\Library;

class CryptModel
{
    const CIPHER = 'AES-256-ECB';
	const KEY = '43ad4680da98dec7c5b179ff63d11488';

	/**
	 * 加密
	 *
	 * @param string $plainText 未加密字符串
	 * @param string $key        密钥
	 * @return mixed
	 */
	public static function encrypt($plainText,$key)
	{
        $encryptedText = openssl_encrypt($plainText, self::CIPHER, $key);

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

        $decryptedText = openssl_decrypt($encryptedText, self::CIPHER, $key);

        return trim($decryptedText);
    }
}