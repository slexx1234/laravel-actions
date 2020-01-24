<?php

namespace Slexx\LaravelActions;

abstract class Action
{
    /**
     * @return mixed
     */
    public function __invoke()
    {
        if (method_exists($this, 'authorize') && call_user_func_array([$this, 'authorize'], func_get_args()) === false) {
            abort(403);
        }

        return call_user_func_array([$this, 'execute'], func_get_args());
    }
}
