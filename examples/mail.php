<?php

require_once __DIR__ . '/../autoload.php';

// use chanyu\Mail\Mailer;
//
// $mail = new Mailer(['host' => '', 'username' => '', 'password' => '', 'from' => '', 'name' => '系统邮件']);
// $res1 = $mail->send(['address' => ''], ['subject' => '测试', 'type' => 'html', 'body' => '<b>测试</b>']);
// var_dump($res1);
// $res2 = $mail->send([['address' => ''], 'address' => ''], ['subject' => '测试', 'type' => 'html', 'body' => '<b>测试</b>']);
// var_dump($res2);

try {
    $mail =  \Chanyu\Mail\Mail::mailer(['a','b']);
} catch (\Exception $exception) {
    var_dump($exception->getMessage().'1111111111111111111');die;
}
