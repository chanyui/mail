<?php

namespace Chanyu\Mail\Core;


class Utils
{
    /**
     * 自定义-验证参数
     *
     * @author yc
     * @param $tomail
     * @param $body
     * @param array $reply
     * @param array $cc
     * @param array $attachment
     * @return bool|string
     */
    public static function validateParam($tomail, $body, $reply = [], $cc = [], $attachment = [])
    {
        $addressArr = ['address'];
        // 验证格式
        if (!is_array($tomail)) {
            return 'Message could not be sent：tomail error!';
        }

        // 判断是一维数组还是二维数组
        $tomail_deep = self::deep_array($tomail);
        if ($tomail_deep == 1) {
            $keys = array_keys($tomail);
            if (array_diff($addressArr, $keys)) {
                return 'Message could not be sent：tomail name and address not empty';
            }
        } else {
            foreach ($tomail as $value) {
                $keys = array_keys($value);
                if (array_diff($addressArr, $keys)) {
                    return 'Message could not be sent：tomail name and address not empty!';
                }
            }
        }

        // 判断内容是否正确
        $bkeys = array_keys($body);
        if (array_diff(['subject', 'body'], $bkeys)) {
            return 'Message could not be sent：payload subject and body not empty';
        }

        // 回复
        if ($reply) {
            if (array_diff($addressArr, array_keys($reply))) {
                return 'Message could not be sent：reply name and address not empty';
            }
        }
        // 抄送
        if ($cc && is_array($cc)) {
            $cc_deep = self::deep_array($cc);
            $caddress = ['address'];
            if ($cc_deep == 1) {
                $keys = array_keys($cc);
                if (array_diff($caddress, $keys)) {
                    return 'Message could not be sent：cc name and address not empty';
                }
            } else {
                foreach ($cc as $value) {
                    $keys = array_keys($value);
                    if (array_diff($caddress, $keys)) {
                        return 'Message could not be sent：cc name and address not empty!';
                    }
                }
            }
        }

        // 附件
        if ($attachment && is_array($attachment)) {
            $aaddress = ['path'];
            $att_deep = self::deep_array($attachment);
            if ($att_deep == 1) {
                $keys = array_keys($attachment);
                if (array_diff($aaddress, $keys)) {
                    return 'Message could not be sent：attachment path not empty';
                }
            } else {
                foreach ($attachment as $value) {
                    $keys = array_keys($value);
                    if (array_diff($aaddress, $keys)) {
                        return 'Message could not be sent：attachment path not empty!';
                    }
                }
            }
        }
        return true;
    }

    /**
     * 判断是否为二维数组
     *
     * @author yc
     * @param $array
     * @return int 0-非数组，1-一维数组，2-二维数组
     */
    public static function deep_array($array)
    {
        if (!is_array($array)) {
            return 0;
        }
        if (count($array) == count($array, COUNT_RECURSIVE)) {
            return 1;
        } else {
            return 2;
        }
    }
}