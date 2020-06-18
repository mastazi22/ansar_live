<?php $url = "http://sms.sslwireless.com/pushapi/dynamic/server.php"; 
$body = "You are embodied"; $param = 
"user=ansarapi&pass=75@5S01j&sms[0][0]=8801829616787&sms[0][1]=" . 
urlencode($body) . "&sid=ANSARVDP"; $crl = curl_init(); 
curl_setopt($crl, CURLOPT_SSL_VERIFYPEER, FALSE); curl_setopt($crl, 
CURLOPT_SSL_VERIFYHOST, 2); curl_setopt($crl, CURLOPT_URL, $url); 
curl_setopt($crl, CURLOPT_HEADER, 0); curl_setopt($crl, 
CURLOPT_RETURNTRANSFER, 1); curl_setopt($crl, CURLOPT_POST, 1); 
curl_setopt($crl, CURLOPT_POSTFIELDS, $param); echo $response = 
curl_exec($crl); curl_close($crl);
?>
