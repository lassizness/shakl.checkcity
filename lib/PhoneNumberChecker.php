<?php

namespace Shakl\Checkcity;
class PhoneNumberChecker {
    private $url;
    private $token;

    public function __construct($url, $token) {
        $this->url = $url;
        $this->token = $token;
    }

    public function checkPhoneNumber($userPhone) {
        $params = array(
            "phone" => $userPhone,
            "token" => $this->token
        );

        // Запрос
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_URL, $this->url . '?' . http_build_query($params));

        if (!$output = curl_exec($ch)) {
            CustomAddMessage2Log("Ошибка cURL: " . curl_error($ch), "shakl.checkcity");
            curl_close($ch);
            return false;
        }

        curl_close($ch);

        $data = json_decode($output, true);

        if ($data['answer'] !== 'ok') {
            CustomAddMessage2Log("Ошибка API: " . $data["answer"] . "\r\n", "shakl.checkcity");
            return false;
        }

        return $data;
    }
}
