<?php
class Mailchimp4U
{
    public $apiKey;
    public $listId;

    public function __construct($apiKey, $listId)
    {
        $this->apiKey = $apiKey;
        $this->listId = $listId;
    }

    public function addSubscriber($email)
    {
        $memberId = md5(strtolower($email));
        $dataCenter = substr($this->apiKey,strpos($this->apiKey,'-')+1);

        $url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $this->listId . '/members/' . $memberId;

        $json = json_encode(array(
            'email_address' => $email,
            'status'        => 'subscribed',
        ));

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $this->apiKey);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $json);

        $result = json_decode(curl_exec($ch));
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if($httpCode == 200){
            return $result->status;
        }

        return null;
    }
}