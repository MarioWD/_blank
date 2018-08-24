<?php
namespace classes;
Class Config{
    static function get($path = null)
    {
        if($path)
        {
            $out = null;
            $path = explode("/", $path);
            foreach($path as $bit)
            {
                if(isset($GLOBALS['conf'][$bit]) && !$out)
                {
                    $out = $GLOBALS['conf'][$bit];
                }
                elseif($out && $out[$bit])
                {
                    $out = $out[$bit];
                }
            }
            return $out;
        }
    }

    static function set($key, $data)
    {
        $GLOBALS['conf'][$key] = $data;
    }
}
?>
