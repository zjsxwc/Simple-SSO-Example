# Simple SSO Example

## PREPARES

一个专门用来登录的项目PASSPORT， 参考 passport_site_index.php

独立子项目A， 参考a_sub_site_index.php

独立子项目B， 参考a_sub_site_index.php



## FEATURES

PASSPORT项目能够在登录后，转跳到指定的地址

各个独立子项目都有自己的用户系统帐号，也就是都有自己的user_id，且存在一个表关联自己用户的user_id与PASSPORT系统的用户passport_user_id

各个独立子项目能够查询 PASSPORT项目 当前 是否已经有登录，获得passport_user_id，进而登录本系统



## REALIZATION

PASSPORT需要提供一个JSONP接口用于返回是否登录状态以及passport_user_id，同时该接口必须要校验请求的Referer是否是被允许的独立子项目域名，同时该JSONP返回的passport_user_id数据必须是通过对应子项目的RSA公钥加密后的结果，

一个返回格式例子：
```
{
  "status":"AUTH_MEMBER",
  "data" : "公钥加密后的{'passport_user_id':123,'timestamp':1234567890}"
}
```
独立子项目在其所有页面上，存在一段js，用于用户在页面加载后就查询passport_user_id，然后刷新本项目用户登录状态为 未登录或已登录，所以独立子项目需要一个处理该js转发过来的PASSPORT JSONP数据然后处理修改本项目用户状态SESSION值，然后返回给js是否需要转跳刷新页面，

一个返回格式例子：
```
{
   "result": "user_changed",
   "redirect": "https://xxx.com"
}
```
同时子项目即使是在有RSA加密的情况下，为了防止被恶意伪装的JSONP数据，我们需要校验timestamp是否在一分钟以内的请求，否则无效。



## 跑例子要注意

需要分别把2个开发域名``http://passport.dev``与``http://sso.dev``给passport_site_index.php与a_sub_site_index.php。

然后到SSO/Passport.php里把子站的公钥填到\SSO\Passport::$ssoPublicKeyMap里，子站的私钥填到SSO/Subsite.php的\SSO\Subsite::$ssoPrivateKey里。









