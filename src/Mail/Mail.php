<?php

namespace Chanyu\Mail;

use Chanyu\Mail\Exception\MailException;

class Mail
{
    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MailException
     */
    public static function __callStatic($name, $arguments)
    {
        return (new static())->__call($name, $arguments);
    }

    /**
     * @param $name
     * @param $arguments
     * @return mixed
     * @throws MailException
     */
    public function __call($name, $arguments)
    {
        $name = ucfirst($name);

        $product = $this->getProductName();

        $class = "Chanyu\\{$product}\\Driver\\{$name}";

        if (class_exists($class)) {
            return new $class(...$arguments);
        }

        throw new MailException("Driver Name Of {$name} Not Found!");
    }

    /**
     * @return mixed|string
     * @throws MailException
     */
    protected function getProductName()
    {
        $array = explode('\\', get_class($this));

        if (is_array($array) && isset($array[1])) {
            return $array[1];
        }

        throw new MailException('Service Name Not Found!');
    }
}
