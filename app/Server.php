<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/4/18
 * Time: 23:02
 */

use App\Manager\DataCenter;
use App\Manager\Logic;

require_once __DIR__ . '/../vendor/autoload.php';

class Server
{
    const CLIENT_CODE_MATCH_PLAYER = 600;

    const HOST = '0.0.0.0';
    const PORT = 8811;
    const FRONT_PORT = 8812;
    const CONFIG = [
        'worker_num' => 4,
        'task_worker_num' => 4,
        'enable_static_handler' => true,
        'document_root' =>
            '/mnt/htdocs/HideAndSeek_teach/frontend',
    ];

    private $ws;
    private $logic;

    public function __construct()
    {
        $this->logic = new Logic();
        $this->ws = new \Swoole\WebSocket\Server(self::HOST, self::PORT);
        $this->ws->listen(self::HOST, self::FRONT_PORT, SWOOLE_SOCK_TCP);
        $this->ws->set(self::CONFIG);
        $this->ws->on('start', [$this, 'onStart']);
        $this->ws->on('workerStart', [$this, 'onWorkerStart']);
        $this->ws->on('open', [$this, 'onOpen']);
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('close', [$this, 'onClose']);
        $this->ws->on('task', [$this, 'onTask']);
        $this->ws->on('finish', [$this, 'onFinish']);
        $this->ws->start();
    }

    public function onStart($server)
    {
        swoole_set_process_name('hide-and-seek');
        echo sprintf("master start (listening on %s:%d)\n",
            self::HOST, self::PORT);
    }

    public function onWorkerStart($server, $workerId)
    {
        echo "server: onWorkStart,worker_id:{$server->worker_id}\n";
        DataCenter::$server = $server;
    }

    public function onOpen($server, $request)
    {
        DataCenter::log(sprintf('client open fd：%d', $request->fd));

        $playerId = $request->get['player_id'];
        DataCenter::setPlayerInfo($playerId, $request->fd);
    }

    public function onMessage($server, $request)
    {
        DataCenter::log(sprintf('client open fd：%d，message：%s', $request->fd, $request->data));

        $data = json_decode($request->data, true);
        $playerId = DataCenter::getPlayerId($request->fd);
        switch ($data['code']) {
            case self::CLIENT_CODE_MATCH_PLAYER:
                $this->logic->matchPlayer($playerId);
                break;
        }
    }

    public function onClose($server, $fd)
    {
        DataCenter::log(sprintf('client close fd：%d', $fd));
    }

    public function onTask($server, $taskId, $srcWorkerId, $data)
    {
        DataCenter::log("onTask", $data);
    }

    public function onFinish($server, $taskId, $data)
    {
        DataCenter::log("onFinish", $data);
    }
}
new Server();