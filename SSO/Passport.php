<?php
namespace SSO;

/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 16/9/18
 * Time: 上午8:44
 */
class Passport
{


    /**
     * @var array 域名与该子项目的对应公钥
     */
    public $ssoPublicKeyMap = [
        'http://sso.dev' => "-----BEGIN PUBLIC KEY-----
MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDaF/H+DpdIlWeequcxgfGzsjpe
gAYpPfFGiODPiYshJDx63aAQQcXAUyhea8cNLvcwKlYWTbjB9kNy3dmYy8ZI4Nmc
wtIZHWYqAwT89QVFJb8GwxK+SR9Ffp+ulSUT2nk0/Sx0tweLQkvy4I8zXYTtdFMN
LxisQLup1pNPTdvWlQIDAQAB
-----END PUBLIC KEY-----",

    ];



    public $passportUserId = null;
    public $status = 'NO_AUTH';

    public $jsCallbackFunctionName = 'callback';

    /**
     * @param $currentLoginInfo
     */
    public function __construct($currentLoginInfo = null)
    {
        foreach (['passportUserId','status','jsCallbackFunctionName'] as $key) {
            if (isset($currentLoginInfo[$key])) {
                $this->{$key} = $currentLoginInfo[$key];
            }
        }
    }

    public function setPassportUserId($puid)
    {
        $this->passportUserId = $puid;
        return $this;
    }

    public function setStatus($s)
    {
        $this->status = $s;
        return $this;
    }

    /**
     * 依赖 $_GET['callback'] and $_SERVER['HTTP_REFERER']
     * @return string
     */
    public function handleJsonp()
    {
        if (isset($_GET['callback'])&&(strlen($_GET['callback']))) {
            $this->jsCallbackFunctionName = $_GET['callback'];
        }

        if (!isset($_SERVER['HTTP_REFERER'])) {
            throw new \RuntimeException('NOT VALID HTTP REFERER');
        }
        $httpReferer = $_SERVER['HTTP_REFERER'];
        $host = null;
        $publicKey = null;
        foreach ($this->ssoPublicKeyMap as $h => $k) {
            if (strpos($httpReferer,$h) === 0) {
                $host = $h;
                $publicKey = $k;
                break;
            }
        }
        if (!$host) {
            throw new \RuntimeException('NOT VALID REQUEST HOST: '.$httpReferer);
        }

        if ($this->passportUserId && ($this->status == 'NO_AUTH')) {
            $this->status = 'AUTH_MEMBER';
        }

        $data = [
            'passport_user_id' => $this->passportUserId,
            'timestamp' => time(),
        ];
        $dataStr = "";
        openssl_public_encrypt(json_encode($data),$dataStr, $publicKey );
        $data = base64_encode($dataStr);
        return sprintf("%s(%s);",$this->jsCallbackFunctionName,json_encode(['status' => $this->status,'data' => $data]));
    }
}
