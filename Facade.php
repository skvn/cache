<?php

namespace Skvn\Cache;

use Skvn\Base\Facade as BaseFacade;

class Facade extends BaseFacade
{
    protected static function getFacadeTarget()
    {
        return 'cache';
    }
}