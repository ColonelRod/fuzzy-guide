<?php

header("Content-Type: application/json");

if (!isset($_POST['request'])) {
    die(json_encode(['error' => '请求失败111']));
}

$encodedPublicKey = $_POST['request'];
$decodedPublicKey = base64_decode(urldecode($encodedPublicKey));
file_put_contents('decodedPublicKey.txt', $decodedPublicKey);

// // 假设客户端发送的公钥已经是完整的 PEM 格式
// $publicKeyPem = $decodedPublicKey;

// 格式化为 PEM
$publicKeyPem = "-----BEGIN PUBLIC KEY-----\n" . chunk_split($decodedPublicKey, 64, "\n") . "-----END PUBLIC KEY-----";
file_put_contents('publicKeyPem.txt', $publicKeyPem);

$publicKeyPems = $publicKeyPem;


$publicKeyResource = openssl_pkey_get_public($publicKeyPems);
if (!$publicKeyResource) {
    while ($msg = openssl_error_string()) {
        error_log($msg);  // 记录错误到日志
    }
    die(json_encode(['error' => '请求失败111']));
}

$url = "http://baidu.com/";    //主域名必须大于10个字母

// 加密数据
if (!openssl_public_encrypt($url, $encrypted, $publicKeyResource)) {
    die("加密失败");
}

// 将加密后的数据进行Base64编码
$encodedEncrypted = base64_encode($encrypted);

// 对Base64编码后的数据进行URL编码
$encodedUrl = urlencode($encodedEncrypted);

// 返回加密并编码后的数据给客户端
echo json_encode([
    'encrypted_url' => $encodedUrl
]);

?>
