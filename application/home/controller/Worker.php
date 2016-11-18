<?php
namespace app\home\controller;

use think\worker\Server;

class Worker extends Server
{

    
    protected $socket = 'http://0.0.0.0:2346';
    
    public function onMessage($connection,$data)
    {
        $connection->send(json_encode($data));
       //$connection->send("hello");
    }
   
   
}
