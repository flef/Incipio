<?php

namespace mgate\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class mgateUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
