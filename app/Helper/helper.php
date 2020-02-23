<?php

function result($status, $res = '')
{
    return array(
        'status' => $status,
        'res' => $res ? $res : config('result.' . $status, $res)
    );
}
