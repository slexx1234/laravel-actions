<?php

namespace Slexx\LaravelActions;

abstract class Action
{
    /**
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array([$this, 'execute'], func_get_args());
    }
}
