<?php
define('IPs', ['18.237.182.120','52.89.132.202']);
#$_GET['domain'] = "www.makalaccesorios.com";
if(isset($_GET['domain'])) {

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://cloudflare-dns.com/dns-query?name=".$_GET['domain']."&type=A");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/dns-json'));
    curl_setopt($ch, CURLOPT_HEADER,false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    $data = curl_exec($ch);

    if(curl_exec($ch) === false){
        echo 'Curl error: ' . curl_error($ch);
    } else {
        $json = json_decode($data);

        if (isset($json->Answer))
        {
            $ips = array_column($json->Answer, 'data');

            if( array_intersect(IPs, $ips) !== false){
                header("HTTP/1.1 200 Todo bien");
                echo "todo bien";
                die();
            }
        }
        header("HTTP/1.1 404 Todo mal");
        echo "no está delegado";
    }
    curl_close($ch);
} else {
        header("HTTP/1.1 404 Todo mal");
        echo "todo mal";
}