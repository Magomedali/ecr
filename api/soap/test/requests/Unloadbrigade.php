<?php

namespace api\soap\test\requests;
 
class Unloadbrigade extends BaseRequest
{
    public $msg;
 
    public function rules()
    {
        return [
            [['msg',], 'required'],
            ['msg', 'string'],
        ];
    }
}