<?php

require_once __DIR__ . '/../autoload.php';

use Chanyu\Mail\Mail;
use Chanyu\Mail\Exception\MailException;


try {
    $mail = Mail::Swift(['host' => '', 'username' => '', 'password' => '', 'from' => '', 'name' => '系统邮件']);
    $mail->send([['address' => ''], 'address' => ''], ['subject' => '测试', 'type' => 'html', 'body' => '<b>测试</b>']);
} catch (MailException $exception) {
    var_dump($exception->getMessage());
}
