<?php

namespace Chanyu\Mail\Driver;

use Chanyu\Mail\Contract\Contract;
use Chanyu\Mail\Core\Config;
use Chanyu\Mail\Core\Utils;

class Swift extends Config implements Contract
{
    protected $mail = null;

    /**
     * Mailer constructor.
     * @param array $option
     */
    public function __construct($option = [])
    {
        $this->config['secure'] = 'ssl';      //Enable implicit TLS encryption

        if ($option && is_array($option)) {
            $this->config = array_merge($this->config, $option);
        }
        if (!$this->mail) {

        }
    }

    /**
     * 发送邮件
     *
     * @author yc
     * @param $tomail
     * @param $payload
     * @param array $reply
     * @param array $cc
     * @param array $attachment
     * @return mixed true-发送成功，其他失败
     * @throws Exception
     */
    public function send($tomail, $payload, $reply = [], $cc = [], $attachment = [])
    {
        // 验证参数
        $validate = Utils::validateParam($tomail, $payload, $reply, $cc, $attachment);
        if ($validate !== true) {
            return $this->returnMail($validate);
        }

        //需要在 php.ini 里面配置
        //openssl.cafile = /usr/local/openssl/cacert.pem
        //openssl.capath = /usr/local/openssl/certs

        try {
            // 创建Transport对象，设置邮件服务器和端口号，并设置用户名和密码以供验证
            $transport = \Swift_SmtpTransport::newInstance($this->config['host'], $this->config['port'], $this->config['secure'])
                ->setUsername($this->config['username'])
                ->setPassword($this->config['password']);

            // 创建mailer对象
            $mailer = \Swift_Mailer::newInstance($transport);
            // $mailer->protocol = $config['mail_type'];

            // 创建message对象
            $message = \Swift_Message::newInstance();
            $message->setFrom($this->config['from'], $this->config['name']);

            // to
            $to_deep = Utils::deep_array($tomail);
            if ($to_deep == 1) {
                $name = isset($tomail['name']) ? $tomail['name'] : '';
                $message->setTo($tomail['address'], $name);
            } else {
                foreach ($tomail as $value) {
                    $name = isset($value['name']) ? $value['name'] : '';
                    $message->setTo($value['address'], $name);
                }
            }

            // reply
            if ($reply) {
                $rname = isset($reply['name']) ? $reply['name'] : '';
                $message->setReplyTo($reply['address'], $rname);
            }

            // cc - 添加抄送人
            if ($cc) {
                $cc_deep = Utils::deep_array($cc);
                if ($cc_deep == 1) {
                    $cname = isset($cc['name']) ? $cc['name'] : '';
                    $message->setCc($cc['address'], $cname);
                } else {
                    foreach ($cc as $value) {
                        $cname = isset($value['name']) ? $value['name'] : '';
                        $message->setCc($value['address'], $cname);
                    }
                }
            }
            // 添加密送人
            // $message->setBcc('Bcc@qq.com', 'Bcc')

            // Attachments
            if ($attachment) {                                              //Add attachments Optional name
                $att_deep = Utils::deep_array($attachment);
                if ($att_deep == 1) {
                    $aname = isset($attachment['name']) ? $attachment['name'] : '';

                    // 创建attachment对象，content-type这个参数可以省略
                    $attachment = \Swift_Attachment::fromPath($attachment['path'])
                        ->setFilename($aname);
                    // 添加附件
                    $message->attach($attachment);
                } else {
                    foreach ($attachment as $value) {
                        $aname = isset($value['name']) ? $value['name'] : '';
                        // 创建attachment对象，content-type这个参数可以省略
                        $attachment = Swift_Attachment::fromPath($value['path'])
                            ->setFilename($aname);
                        // 添加附件
                        $message->attach($attachment);
                    }
                }
            }

            // content
            $message->setSubject($payload['subject']);

            if (isset($payload['type']) && $payload['type'] == 'html') {
                $message->setContentType('text/html');                          //Set email format to HTML
            }
            $message->setBody($payload['body']);

            if (isset($payload['altBody']) && $payload['altBody']) {
                $this->mail->AltBody = $payload['altBody'];
            }

            // Send the message
            $msg = $mailer->send($message);
            if ($msg > 0) {
                $msg = true;
            } else {
                $msg = 'Send the message failed!';
            }
        } catch (\Exception $exception) {
            $msg = $exception->getMessage();
        }

        return $this->returnMail($msg);
    }
}
