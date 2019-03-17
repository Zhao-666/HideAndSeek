<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/17
 * Time: 16:59
 */

namespace App;


use App\Manager\DataCenter;

require_once __DIR__ . '/../vendor/autoload.php';

class Server
{
    const HOST = '0.0.0.0';
    const PORT = 8811;
    const FRONT_PORT = 8812;
    const CONFIG = [
        'worker_num' => 4,
        'dispatch_mode' => 5,
        'enable_static_handler' => true,
        'document_root' =>
            '/mnt/htdocs/HideAndSeek/frontend',
    ];

    private $ws;

    public function __construct()
    {
        $this->ws = new \Swoole\WebSocket\Server(self::HOST, self::PORT);
        $this->ws->listen(self::HOST, self::FRONT_PORT, SWOOLE_SOCK_TCP);
        $this->ws->set(self::CONFIG);
        $this->ws->on('start', [$this, 'onStart']);
        $this->ws->on('workerStart', [$this, 'onWorkerStart']);
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('close', [$this, 'onClose']);
        $this->ws->start();
    }

    public function onStart($server)
    {
        swoole_set_process_name('hide-and-seek');
        DataCenter::log(
            sprintf(
                'master start (listening on %s:%d)',
                self::HOST,
                self::PORT
            ));
    }

    public function onWorkerStart($server, $workerId)
    {
        echo "server: onWorkStart,worker_id:{$server->worker_id}\n";
    }

    public function onOpen($server, $request)
    {

    }

    public function onClose($server, $fd)
    {

    }

    public function onMessage($server, $request)
    {

    }
}

new Server();