<?php
function sendNotification(){
    $url ="https://fcm.googleapis.com/fcm/send";

    $fields=array(
        "to"=>'d7EuwP09to8N3I7OiCn_Zb:APA91bHChscugL3JzGLnuyrJvkZDejE3RAqRVIhqnTI-TWrkO6cPshwgEu4Dn-E-b2OCBMF-fWPTOhcECcu7lwqjWvLbPe3xcP_jkHT2GdnlWuWucGb9AmfoVg64qXycdVSPfX8oDaO4',//$_REQUEST['token'],
        "notification"=>array(
            "body"=>'HELLO',//$_REQUEST['message'],
            "title"=>'HI',//$_REQUEST['title'],
            "icon"=>$_REQUEST['icon'],
            "click_action"=>"https://google.com"
        )
    );

    $headers=array(
        'Authorization: key=AAAAHhonnwA:APA91bFeYVEaK3bI2Fp9iPYHeZIXg7G3QdjOGlpK9nURi_473bw6ZnIG0hqW5JYEoCwWX_mHGO4jy_8CBsgEWOTvGC0zlnC7F5Fk0tVc5sdtYiSTVgzZ4DSv68WK4cgCMNwQSubUN1lT',
        'Content-Type:application/json'
    );

    $ch=curl_init();
    curl_setopt($ch,CURLOPT_URL,$url);
    curl_setopt($ch,CURLOPT_POST,true);
    curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
    curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
    curl_setopt($ch,CURLOPT_POSTFIELDS,json_encode($fields));
    $result=curl_exec($ch);
    print_r($result);
    curl_close($ch);
}

sendNotification();