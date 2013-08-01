<?php

function returnValueMap($valueMap, $parameters)
{
    $parameterCount = count($parameters);

    foreach ($valueMap as $map) {
        if (!is_array($map) || $parameterCount != count($map) - 1) {
            continue;
        }

        $return = array_pop($map);
        if ($parameters === $map) {
            return $return;
        }
    }
    return NULL;
}