<?php
define(CONNTIMEOUT,2);

if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),"curl") === 0) { $isCurl = true; } else { $isCurl = false; }

if (!$isCurl) {
    ?><html><body><pre><?php
}


if (isset($_REQUEST['host'])) {

    // disable scanning of local server
    if (substr($_REQUEST['host'],0,4) == "127.") { exit; }
    if ($_REQUEST['host'] == "localhost") { exit; }

    if (isset($_REQUEST['port']) and $_REQUEST['port'] != "") {
        $ports = array($_REQUEST['port']);
    } else {
        $ports = array(21, 25, 80, 81, 110, 443, 3306);
    }

    foreach ($ports as $port)
    {
        $connection = @fsockopen($_REQUEST['host'], $port, $errno, $errstr, CONNTIMEOUT);

        if ($connection !== false)
        {
            print $_REQUEST['host'] . ':' . $port . ' ' . '(' . getservbyport($port, 'tcp') . ") is open\n";
            fclose($connection);
        }

        else
        {
            print $_REQUEST['host'] . ':' . $port . " is not responding - ";
            // print "errno: ".$errno." errstr: ".$errstr."\n";
            if ($errno == 110) {
                print "dropped packet / timed out\n";
            } else if ($errno == 111) {
                print "port closed - connection rejected\n";
            }
        }

        ob_flush();
        flush();
    }

} else {

    print "Usage: curl http://".$_SERVER['HTTP_HOST']."/host/port\n";
    print "port is optional\n";
    print "Timeout: ".CONNTIMEOUT."s\n";

}


if (!$isCurl) {
    ?></pre>
    <form method="get">
    <input type="text" name="host" placeholder="host">
    <input type="text" name="port" placeholder="port (optional)">
    <input type="submit">
    </form></body></html><?php
}

ob_end_flush();
