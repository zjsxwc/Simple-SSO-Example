<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 1/3/17
 * Time: 4:52 PM
 */


//todo, Make sure this passport is under host http://passport.dev

if ($_GET['uri'] == "sso_passport") {

    $currentUser = [
        'user_id'   => "1234",
        'member_type'      => "vip"
    ];
    $ssoPassportObj = new \SSO\Passport;
    $ssoPassportObj->setPassportUserId($currentUser['user_id'])->setStatus("AUTH_".strtoupper($currentUser['member_type']));
    header("content-type: application/javascript");
    echo $ssoPassportObj->handleJsonp();
    exit;

}




