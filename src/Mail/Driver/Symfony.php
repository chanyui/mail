<?php

namespace Chanyu\Mail\Driver;

use Chanyu\Mail\Contract\Contract;
use Chanyu\Mail\Core\Config;
use Chanyu\Mail\Core\Utils;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

class Symfony extends Config implements Contract
{
    protected $mail = null;

    /**
     * Symfony constructor.
     * @param array $option
     */
    public function __construct($option = [])
    {
        // $this->config['debug'] = SMTP::DEBUG_OFF;                   //Enable verbose debug output
        // $this->config['type'] = 'smtp';                             //Send using SMTP
        // $this->config['secure'] = PHPMailer::ENCRYPTION_SMTPS;      //Enable implicit TLS encryption

        if ($option && is_array($option)) {
            $this->config = array_merge($this->config, $option);
        }
        if (!$this->mail) {
            // Generate connection configuration
            $host = $this->config['type'] . '://' . $this->config['username'] . ':' . urlencode($this->config['password']) . '@' . $this->config['host'] . ':' . $this->config['port'];
            $transport = Transport::fromDsn($host);

            $this->mail = new \Symfony\Component\Mailer\Mailer($transport);
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

        $mail = (new Email());

        $mail->from(new Address($this->config['from'], $this->config['name']));

        // to
        $to_deep = Utils::deep_array($tomail);
        $toAddressArr = [];
        if ($to_deep == 1) {
            $name = isset($tomail['name']) ? $tomail['name'] : '';
            $toAddressArr[] = new Address($tomail['address'], $name);
        } else {
            foreach ($tomail as $value) {
                $name = isset($value['name']) ? $value['name'] : '';
                $toAddressArr[] = new Address($value['address'], $name);
            }
        }
        $mail->to(...$toAddressArr);

        // reply
        if ($reply) {
            $rname = isset($reply['name']) ? $reply['name'] : '';
            $mail->addReplyTo(new Address($reply['address'], $rname));
        }

        // cc
        if ($cc) {
            $cc_deep = Utils::deep_array($cc);
            $ccAddressArr = [];
            if ($cc_deep == 1) {
                $cname = isset($cc['name']) ? $cc['name'] : '';
                $ccAddressArr[] = new Address($cc['address'], $cname);
            } else {
                foreach ($cc as $value) {
                    $cname = isset($value['name']) ? $value['name'] : '';
                    $ccAddressArr[] = new Address($value['address'], $cname);
                }
            }
            $mail->cc(...$ccAddressArr);
        }

        // $mail->->bcc('bcc@example.com')

        // Attachments
        if ($attachment) {                                              //Add attachments Optional name
            $att_deep = Utils::deep_array($attachment);
            if ($att_deep == 1) {
                $aname = isset($attachment['name']) ? $attachment['name'] : '';
                $mail->attachFromPath($attachment['path'], $aname);
            } else {
                foreach ($attachment as $value) {
                    $aname = isset($value['name']) ? $value['name'] : '';
                    $mail->attachFromPath($attachment['path'], $aname);
                }
            }
        }

        $mail->subject($payload['subject']);

        // content
        if (isset($payload['type']) && $payload['type'] == 'html') {
            $mail->html($payload['body']);                              //Set email format to HTML
        } else {
            $mail->text($payload['body']);
        }

        try {
            $res = $this->mail->send($mail);
            $msg = is_null($res) ? true : false;
        } catch (\Exception $exception) {
            $msg = $exception->getMessage();
        }

        $this->returnMail($msg);
    }
}
