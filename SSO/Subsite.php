<?php
namespace SSO;
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 16/9/18
 * Time: 上午8:44
 */
class Subsite
{

    private $ssoPrivateKey = "-----BEGIN RSA PRIVATE KEY-----
MIICXgIBAAKBgQDaF/H+DpdIlWeequcxgfGzsjpegAYpPfFGiODPiYshJDx63aAQ
QcXAUyhea8cNLvcwKlYWTbjB9kNy3dmYy8ZI4NmcwtIZHWYqAwT89QVFJb8GwxK+
SR9Ffp+ulSUT2nk0/Sx0tweLQkvy4I8zXYTtdFMNLxisQLup1pNPTdvWlQIDAQAB
AoGBALVPuusPjk3Vh7OtOU87TImr3kK6BGU9Dd0p5lwjaPtAeNdccLmTNKfTengk
+fLH3NC6IZ+hNbxq02And8NDha9/ikfI7NC3/1SmtmjCtEZI1NcUxrh48ZAp3i4J
VkHJt8UPYjWhntvwpqX5C5AfGpo31jXAniSvrIT8SSrQPJ0BAkEA9991lp7lwzS0
VPEZg3HW0eL0Ewq68abaSuEXwLN6YQAfQ0SwBVAKnboxZJxzq7lLudhypJiYHvRr
Ba3xqU/JkQJBAOE+h9Yn6onf6EKwtlmztx0PGbpcHNQf8qNV1DvZiYlw5W2kAmW6
cieS3wMEZDC4gvNK7u8R+WqZ8pAZ5pKtGsUCQQC9xWBie67O38f8jEYLQ5nDQS26
cmmj3ymbUG/+Aar9HrnQp4LX3mryTP3J2JoabBfU5ikHaSh18o0JYR32kXZxAkEA
w8FxkpicSHk1RQiJYkpDQVkHViSJ1X1yhaupSN6VnsJkUrZwcvLoFSaa9OdTH9ir
zj/4igPIDXKAEebAsgcJtQJAGwzL0kb4MDoJEf8Gh6EiBhR7OBfubiQ4m4hAdlSc
nos52Sbf8QBZweQG4cmtrZtoRl4E6BsrsAjS2T9yMB0jFw==
-----END RSA PRIVATE KEY-----";

    /**
     * @param string $base64encrypted
     * @return array
     */
    public function decryptData($base64encrypted)
    {
        $dase64decoded = base64_decode($base64encrypted);
        $decrypted="";
        openssl_private_decrypt($dase64decoded,$decrypted,$this->ssoPrivateKey);
        $data = json_decode($decrypted,true);
        if ( (!isset($data['timestamp'])) || (!is_numeric($data['timestamp'])) ) {
            throw new \RuntimeException('Not Valid data');
        }
        if (time()-60 > $data['timestamp']) {
            throw new \RuntimeException('Timestamp over');
        }
        return $data;
    }

    /**
     * @param string $response
     * @return bool|mixed
     */
    public function handleFromPassport($response)
    {
        $response = json_decode($response,true);
        if ($response['status'] == "NO_AUTH") {
            return false;
        }
        if (!isset($response['data'])||(strlen($response['data']) == 0)) {
            return false;
        }
        return $this->decryptData($response['data']);
    }
}