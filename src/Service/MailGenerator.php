<?php

namespace App\Service;

class MailGenerator
{
    protected $mailer;
    protected $template;
    public function registration($email, $url)
    {
        
        $message = (new \Swift_Message('Bienvenido a Lajamonjunta'))
        ->setFrom('no-reply@sysadm.es')
        ->setTo($email)
        ->setBody($this->template->render(
            'emails/token.html.twig',array('url' => $url,)
        ),
        'text/html'
    );
        return $this->mailer->send($message);
    }

    public function lost($email, $url)
    {
        
        $message = (new \Swift_Message('Lajamonjunta'))
        ->setFrom('no-reply@sysadm.es')
        ->setTo($email)
        ->setBody($this->template->render(
            'emails/lost.html.twig',array('url' => $url,)
        ),
        'text/html'
    );
        return $this->mailer->send($message);
    }

    public function username($email, $url)
    {
        
        $message = (new \Swift_Message('Lajamonjunta'))
        ->setFrom('no-reply@sysadm.es')
        ->setTo($email)
        ->setBody($this->template->render(
            'emails/username.html.twig',array('url' => $url,)
        ),
        'text/html'
    );
        return $this->mailer->send($message);
    }
    public function restore($email, $url)
    {
        
        $message = (new \Swift_Message('Lajamonjunta'))
        ->setFrom('no-reply@sysadm.es')
        ->setTo($email)
        ->setBody($this->template->render(
            'emails/password.html.twig',array('url' => $url,)
        ),
        'text/html'
    );
        return $this->mailer->send($message);
    }
    public function __construct(\Swift_Mailer $mailer, \Twig\Environment $template)
    {
        $this->mailer = $mailer;
        $this->template = $template;
    }
    
    public function generateRandomString($length = 8) {
        $characters = 'abcdefghijklmnsopkrstuvwxyz1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return strval($randomString);
    }
}