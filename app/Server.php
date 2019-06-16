<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/17
 * Time: 16:59
 */

use App\Manager\DataCenter;
use App\Manager\Logic;
use App\Manager\TaskManager;

require_once __DIR__ . '/../vendor/autoload.php';

class Server
{
    const CLIENT_CODE_MATCH_PLAYER = 600;
    const CLIENT_CODE_START_ROOM = 601;
    const CLIENT_CODE_MOVE_PLAYER = 602;

    const HOST = '0.0.0.0';
    const PORT = 8811;
    const FRONT_PORT = 8812;
    const CONFIG = [
        'worker_num' => 4,
        'task_worker_num' => 4,
        'dispatch_mode' => 5,
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
        $this->ws->on('request', [$this, 'onRequest']);
        $this->ws->start();
    }

    public function onStart($server)
    {
        swoole_set_process_name('hide-and-seek');
        echo sprintf("master start (listening on %s:%d)\n",
            self::HOST, self::PORT);
        DataCenter::initDataCenter();
    }

    public function onWorkerStart($server, $workerId)
    {
        echo "server: onWorkStart,worker_id:{$server->worker_id}\n";
        DataCenter::$server = $server;
    }

    /**
     * @param $server \swoole_websocket_server
     * @param $request
     */
    public function onOpen($server, $request)
    {
        DataCenter::log(sprintf('client open fd：%d', $request->fd));

        $playerId = $request->get['player_id'];
        if (empty(DataCenter::getOnlinePlayer($playerId))) {
            DataCenter::setPlayerInfo($playerId, $request->fd);
        } else {
            $server->disconnect($request->fd, 4000, '该player_id已在线');
        }
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
            case self::CLIENT_CODE_START_ROOM:
                $this->logic->startRoom($data['room_id'], $playerId);
                break;
            case self::CLIENT_CODE_MOVE_PLAYER:
                $this->logic->movePlayer($data['direction'], $playerId);
                break;
        }
    }

    public function onClose($server, $fd)
    {
        DataCenter::log(sprintf('client close fd：%d', $fd));
        $this->logic->closeRoom(DataCenter::getPlayerId($fd));
        DataCenter::delPlayerInfo($fd);
    }

    public function onTask($server, $taskId, $srcWorkerId, $data)
    {
        DataCenter::log("onTask", $data);
        $result = [];
        switch ($data['code']) {
            case TaskManager::TASK_CODE_FIND_PLAYER:
                $ret = TaskManager::findPlayer();
                if (!empty($ret)) {
                    $result['data'] = $ret;
                }
                break;
        }
        if (!empty($result)) {
            $result['code'] = $data['code'];
            return $result;
        }
    }

    public function onFinish($server, $taskId, $data)
    {
        DataCenter::log("onFinish", $data);
        switch ($data['code']) {
            case TaskManager::TASK_CODE_FIND_PLAYER:
                $this->logic->createRoom($data['data']['red_player'], $data['data']['blue_player']);
                break;
        }
    }

    public function onRequest($request, $response)
    {
        DataCenter::log("onRequest");
        $action = $request->get['a'];
        if ($action == 'get_online_player') {
            $data = [
                'online_player' => DataCenter::lenOnlinePlayer()
            ];
            $response->end(json_encode($data));
        } elseif ($action == 'get_player_rank') {
            $data = [
                'players_rank' => DataCenter::getPlayersRank()
            ];
            $response->end(json_encode($data));
        }
    }
}

new Server();