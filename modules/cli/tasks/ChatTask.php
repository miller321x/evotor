<?php


class ChatTask extends \Phalcon\Cli\Task
{

    public function mainAction() {

        $websocketPort = $this->getDI()->getConfig()->application->websocketPort;

        $server = \Ratchet\Server\IoServer::factory(

            new \Ratchet\Http\HttpServer(
                new \Ratchet\WebSocket\WsServer(
                    new \Chat()
                )
            ),
            $websocketPort


        );

/*
        $app = new Ratchet\App('localhost', 8080);
        $app->route('/chat', new Chat);
        $app->route('/echo', new Ratchet\Server\EchoServer, array('*'));
        $app->run(); */

        $server->run();
    }

}