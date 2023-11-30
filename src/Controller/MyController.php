<?php

// src/Controller/MyController.php

namespace App\Controller;

use App\Mailer\MyEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class MyController extends AbstractController
{
    public function sendEmail(\Symfony\Component\Mailer\MailerInterface $mailer)
    {
        $email = new MyEmail();
        $mailer->send($email);

        return $this->render('email/sent.html.twig');
    }
}

