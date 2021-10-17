<?php
namespace validate\sence;
use validate\Validate;
class NormalValidate extends Validate{
    protected $rule=[
        'username'=>'require',
    ];
    protected $message=[
        'username.require'=>'用户名必须'
    ];
}