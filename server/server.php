<?php

namespace bopdev;

$functions = __DIR__ . "/app/model/functions.php";
$sqlpool = __DIR__ . "/app/model/sqlpool.php";
$maps = __DIR__ . "/app/model/maps.php";
$weather = __DIR__ . "/app/model/weather.php";
$localenv = __DIR__ . "/config/env.php";
foreach ([$sqlpool, $functions, $maps, $weather] as $value) {
    require_once $value;
    unset($value);
};
if (getenv('ISLOCAL')) {
    require_once $localenv;
    unset($localenv);
}


use Swoole\Http\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;
// use Swoole\Table;
// use Swoole\WebSocket\Frame;
use bopdev\SQLPool;
use \bopdev\Maps;
use \bopdev\Weather;


class FWServer
{
    private $appname;
    private $mime = [
        "css" => "text/css",
        "js" => "text/javascript",
        "map" => "application/json",
        "ico" => "image/x-icon",
        "png" => "image/png",
        "gif" => "image/gif",
        "jpg" => "image/jpg",
        "jpeg" => "image/jpg",
        "mp4" => "video/mp4",
        "svg" => "image/svg+xml",
        "woff" => "font/woff",
        "woff2" => "font/woff2",
    ];

    public function __construct(
        private $db = new SQLPool(),
        private $maps = new Maps(),
        private $weather = new Weather(),
        private $serv = new Server("0.0.0.0", 8080),
    ) {
        $this->appname = getenv('APP_NAME');
        $this->serv->set([
            "dispatch_mode" => 1, // not compatible with onClose, for stateless server
            // 'dispatch_mode' => 7, // not compatible with onClose, for stateless server
            'worker_num' => 4, // Open 4 Worker Process
            'open_cpu_affinity' => true,
            // "open_http2_protocol" => true // not compatible with stateless, only dispatch_modes 2 & 4
            //  'max_request' => 4, // Each worker process max_request is set to 4 times
            //  'document_root'   => '',
            //  'enable_static_handler' => true,
            //  'daemonize' => false, // daems (TRUE / FALSE)
        ]);
        $this->serv->on("Start", [$this, "onStart"]);
        $this->serv->on("WorkerStart", [$this, "onWorkStart"]);
        $this->serv->on("ManagerStart", [$this, "onManagerStart"]);
        $this->serv->on("Request", [$this, "onRequest"]);
        $this->serv->on("Open", [$this, "onOpen"]);
        // $this->serv->on("Message", [$this, "onMessage"]); // websocket message, requires Swoole\WebSocket\Frame
        $this->serv->on("Close", [$this, "onClose"]);
        $this->serv->start();
    }

    /**
     * What to do on connexion closed.
     * @param Server $server
     * @param int $fd
     * @param int $reactorId
     */
    public function onClose(
        Server $server,
        int $fd,
        int $reactorId
    ) {
    }

    /**
     * What to do on manager start.
     * @param Server $server
     */
    public function onManagerStart(Server $server)
    {
        echo "#### Manager started ####" . PHP_EOL;
        swoole_set_process_name("swoole_process_server_manager");
    }

    /**
     * What to do on websocket message. Requires Swoole\WebSocket\Frame.
     * @param Server $server
     * @param Frame $frame
     */
    // public function onMessage(
    //     Server $server,
    //     Frame $frame
    // ) {
    // }

    /**
     * What to do on connexion established.
     * @param Server $server
     * @param Request $request
     */
    public function onOpen(
        Server $server,
        Request $request
    ) {
    }

    /**
     * What to do on HTTP request.
     * @param Request $request
     * @param Response $response
     */
    public function onRequest(
        Request $request,
        Response $response
    ) {
        $response->header("Server", $this->appname);
        $open_basedir = __DIR__ . "/public";
        $server = $request->server;
        $path_info = $server["path_info"];
        $request_uri = $server["request_uri"];
        $type = pathinfo($path_info, PATHINFO_EXTENSION);
        $file = $open_basedir . $request_uri;

        // file serving
        if (isset($this->mime[$type])) {
            if (!file_exists($file)) {
                $response->status(404);
                $response->end();
                return;
            }
            $response->header("Content-Type", $this->mime[$type]);
            $response->sendfile($file);
            return;
        }
        // POST requests
        if ($server["request_method"] === "POST") {
            // $res = $this->httpTask($request->post);
            // $response->header("Content-Type", $res["type"] ?? "");
            // $response->end(json_encode($res["content"]) ?? "");
            return;
        }
        if ($request_uri === "/" || $request_uri === "/index.php") {
            require __DIR__ . "/public/index.php";
        }
    }

    /**
     * What to do on server start.
     * @param Server $server
     */
    public function onStart($server)
    {
        echo "#### onStart ####" . PHP_EOL;
        swoole_set_process_name("swoole_process_server_master");
        echo "Swoole Service has started" . PHP_EOL;
        echo "master_pid: {$server->master_pid}" . PHP_EOL;
        echo "manager_pid: {$server->manager_pid}" . PHP_EOL;
        echo "########" . PHP_EOL . PHP_EOL;

        if ($this->db->test() === true) {
            print('#### Db connected. ####' . PHP_EOL);
        } else print('!!!! No db connection. !!!!' . PHP_EOL);
    }

    /**
     * What to do on worker start.
     * @param Server $server
     * @param int $worker_id
     */
    public function onWorkStart($server, $worker_id)
    {
        echo "#### Worker#$worker_id started ####" . PHP_EOL;
        swoole_set_process_name("swoole_process_server_worker");
        // spl_autoload_register(function ($className) {
        //     $classPath = __DIR__ . "/public/" . $className . ".php";
        //     if (is_file($classPath)) {
        //         require "{$classPath}";
        //         return;
        //     }
        // });
    }
}

$server = new FWServer();
