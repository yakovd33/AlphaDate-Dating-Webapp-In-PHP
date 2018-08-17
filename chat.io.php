<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    use Workerman\Worker;
    use PHPSocketIO\SocketIO;

    class user {
        public $id = null;
        public $socket = null;

        function __construct ($id, $socket) {
            $this->id = $id;
            $this->socket = $socket;
        }
    }

    $users = [];

    function get_user_socket_by_id ($users, $id) {
        global $users;

        for ($i = 0; $i < count($users); $i++) {
            if ($users[$i]->id == $id) {
                return $users[$i]->socket;
            } else {
                return null;
            }
        }
    }

    // listen port 2021 for socket.io client
    $io = new SocketIO(2021);
    $io->on('connection', function($socket)use($io) {
        if (isset($socket->handshake['query']['userid'])) {
            global $users;

            $userid = $socket->handshake['query']['userid'];
            $users[$userid] = $socket;
            // array_push($users, new user($userid, $socket));
        }

        $socket->on('chat_connection', function ($msg) use ($io) {
            $userid = explode(';', $msg)[0];
            $to_id = explode(';', $msg)[1];
            // $io->emit('chat message', $msg);
        });

        $socket->on('typing', function ($msg) use ($io) {
            echo 'typing';
            global $users;

            $userid = explode(';', $msg)[0];
            $to_id = explode(';', $msg)[1];

            // $to_socket = get_user_socket_by_id($users, $to_id);
            $to_socket = $users[$to_id];
            if ($to_socket) {
                $to_socket->emit('typing', $userid);
            }
        });

        $socket->on('untyping', function ($msg) use ($io) {
            global $users;

            $userid = explode(';', $msg)[0];
            $to_id = explode(';', $msg)[1];

            $to_socket = $users[$to_id];
            if ($to_socket) {
                $to_socket->emit('untyping', $userid);
            }
        });

        $socket->on('read', function ($msg) use ($io) {
            global $users;

            $userid = explode(';', $msg)[0];
            $to_id = explode(';', $msg)[1];

            $to_socket = $users[$to_id];
            if ($to_socket) {
                $to_socket->emit('read', $userid);
            }
        });
    });

    Worker::runAll();
?>