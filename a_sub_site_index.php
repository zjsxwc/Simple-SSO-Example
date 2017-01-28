<?php
/**
 * Created by IntelliJ IDEA.
 * User: wangchao
 * Date: 1/3/17
 * Time: 4:52 PM
 */

//todo, Make sure this page is under host http://sso.dev


$currentUser = [];
if (isset($_SESSION['cu'])&&$_SESSION['cu']) {
    $currentUser = json_decode($_SESSION['cu'],true);
    if ($currentUser){
        echo "<pre>Current User name is {$currentUser['name']}</pre>";
    }
}


if (!$_POST) {
    echo <<<HTML
<script src="http://code.jquery.com/jquery-3.1.1.min.js"></script>

<script>

    $.ajax({
        url: "http://passport.dev?uri=sso_passport",
        jsonp: "callback",//the query key for the callback function name
        dataType: "jsonp",
        data: {},

        success: function( response ) {
            console.log( 'In jquery jsonp succ: ', response ); // server response from passport
            if (response.status == "NO_AUTH") {
                alert('Not login');
                return;
            }
            $.post("",{passportResponse: response},function(res) {
                if (res.logined) {
                    alert(res.user.name +' logined throgh sso');
                }

            });
        }
    });
</script>


HTML;

} else {

    $usersInDatabase = [
        "1234" => [
            'user_id' => "1234",
            'name' => 'ada',
            'user_type' => 'vip',
        ],
        "1235" => [
            'user_id' => "1235",
            'name' => 'peter',
            'user_type' => 'member',
        ],
    ];


    $ssoSubsiteObj = new \SSO\Subsite();
    $passportResponse = $ssoSubsiteObj->handleFromPassport($_POST['passportResponse']);
    if (isset($passportResponse['passport_user_id'])) {

        if (isset($usersInDatabase[$passportResponse['passport_user_id']])) {
            $isUserChanged = false;
            if (isset($currentUser['user_id'])&&(strlen($currentUser['user_id']))) {
                $isUserChanged = ($currentUser['user_id'] != $passportResponse['passport_user_id']);
            }

            $currentUser = $usersInDatabase[$passportResponse['passport_user_id']];
            $_SESSION['cu'] = json_encode($currentUser);


            header("content-type: application/json");
            echo json_encode([
                'logined'=> !!$currentUser,
                'user' => $currentUser?:null,
                'result' => $isUserChanged?'user_changed':null,
                'redirect' => false
            ]);
            exit;
        }

    }

}





