<?php

namespace Netmex\Bundle;

use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class NetmexBundle extends AbstractBundle
{
    public function getPath(): string
    {
        return __DIR__;
    }
}