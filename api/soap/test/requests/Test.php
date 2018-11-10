<?php

namespace api\soap\test\requests;
 
class Test extends BaseRequest
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