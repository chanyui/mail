<?php

namespace Chanyu\Mail\Contract;

interface Contract
{
    public function send($tomail, $payload, $reply = [], $cc = [], $attachment = []);
}