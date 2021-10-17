<?php
require "../vendor/autoload.php";

use validate\Request;
use validate\Exception;
use validate\Validate;
use validate\Config;
use validate\Loader;
class Test
{
    public function mood(Request $request)
    {
        $data=$request->only(['user_name']);
        $make= Loader::validate('Normal');
        var_dump($make->check($data));
        var_dump($make->getError());
    }
}
$request = new Request();
$test = new Test();
var_dump($test->mood($request));

//var_dump($request->index($request));