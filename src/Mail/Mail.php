<?php

namespace Chanyu\Mail;

class Mail
{

    public static function __callStatic($name, $arguments)
    {
        return (new static())->__call($name, $arguments);
    }

    public function __call($name, $arguments)
    {
        $name = ucfirst($name);

        $product = $this->getProductName();

        $class = "Chanyu\\{$product}\\Driver\\{$name}";

        if (class_exists($class)) {
            return new $class($arguments);
        }

        // throw new ClientException(
        //     "$product contains no {$product}",
        //     'SDK.VersionNotFound'
        // );
    }

    protected function getProductName()
    {
        $array = explode('\\', get_class($this));

        if (is_array($array) && isset($array[1])) {
            return $array[1];
        }

        // throw new ClientException(
        //     'Service name not found.',
        //     'SDK.ServiceNotFound'
        // );
    }
}
