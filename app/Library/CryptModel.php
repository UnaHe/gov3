<?php

namespace app\Library;

class CryptModel
{
    const CIPHER = MCRYPT_RIJNDAEL_256;
    const MODE = MCRYPT_MODE_ECB;
    const POINT_KEY = '43ad4680da98dec7c5b179ff63d11488';

    /**
     * 加密
     *
     * @param string $plainText 未加密字符串
     * @param string $key        密钥
     * @return mixed
     */
    public static function encrypt($plainText,$key)
    {
        $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $encryptText = mcrypt_encrypt(self::CIPHER, $key, $plainText, self::MODE, $iv);
        return trim(base64_encode($encryptText));
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
        $cryptText = base64_decode($encryptedText);
        $ivSize = mcrypt_get_iv_size(self::CIPHER, self::MODE);
        $iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);
        $decryptText = mcrypt_decrypt(self::CIPHER, $key, $cryptText, self::MODE, $iv);
        return trim($decryptText);
    }
}