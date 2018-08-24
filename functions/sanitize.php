<?php
/**
 * Created by PhpStorm.
 * User: Mario Hugo CF
 * Date: 2017-07-20
 * Time: 1:29 AM
 */

function escape($string)
{
    return htmlentities($string, ENT_QUOTES, 'UTF-8');
}