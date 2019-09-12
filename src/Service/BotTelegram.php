<?php
namespace App\Service;

use Symfony\Component\HttpClient\HttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Config\Definition\Exception\Exception;


class BotTelegram

{
    //Telegram Bot data
    private $token;
    private $urlstart;
    private $chatid;
    private $sendmessage;
    private $sendphoto;

    //LoggerInterface 
    private $logger;

    public function __construct($token, $urlstart, $chatid, $sendmessage, $sendphoto, LoggerInterface $logger)
    {
        $this->token = $token;
        $this->urlstart = $urlstart;
        $this->chatid = $chatid;
        $this->sendmessage = $sendmessage;
        $this->sendphoto = $sendphoto;
        $this->logger = $logger;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function getUrlstart()
    {
        return $this->urlstart;
    }

    public function getChatid()
    {
        return $this->chatid;
    }

    public function getSendMessage()
    {
        return $this->sendmessage;
    }

    public function getSendPhoto()
    {
        return $this->sendmessage;
    }

    public function getFullurl()
    {
        $token = $this->getToken();
        $urlstart = $this->getUrlstart();
        $sendmessage = $this->getSendMessage();

        return $urlstart.$token.$sendmessage;
    }

    public function getPhotoUrl()
    {
        $token = $this->getToken();
        $urlstart = $this->getUrlstart();
        $sendphoto = $this->getSendPhoto();

        return $urlstart.$token.$sendphoto;
    }

    public function pushprovider($telegramsg, $photo)
    {
        $one = "El nuevo proveedor ";
        $two = " se ha añadido a la Jamonjunta!.";
        $object = "https://lajamonjunta.online/uploads/documents/";
        $chatid = $this->getChatid();
        $content = array (
            'headers' => array("Content-Type" => "application/x-www-form-urlencoded"),
            "body"  => array("chat_id" => $chatid, "text" => $one.$telegramsg.$two),
        );
        $content2 = array (
            'headers' => array("Content-Type" => "application/x-www-form-urlencoded"),
            "body"  => array("chat_id" => $chatid, "text" => $object.$photo),
        );
        $url = $this->getFullurl();
        $photoUrl = $this->getPhotoUrl();
        $httpClient = HttpClient::create();
        try {
            $response = $httpClient->request('GET', $url, $content);
            $response = $httpClient->request('GET', $photoUrl, $content2);
            $content = $response->getContent();
            $this->logger->info($content);
        }
        catch (\Exception $e) {
            $this->logger->critical($e);
        }
        return $content;
    }
}