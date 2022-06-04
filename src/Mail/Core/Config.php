<?php

namespace Chanyu\Mail\Core;

class Config
{
    protected $config = [
        'auth'     => true,                               //Enable SMTP authentication
        'port'     => '465',                              //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`
        'host'     => '',                                 //Set the SMTP server to send through
        'username' => '',                                 //SMTP username
        'password' => '',                                 //SMTP password
        'from'     => '',                                 //send address
        'name'     => '',                                 //send name
        'charset'  => 'UTF-8',                            //The character set of the message.
    ];
}