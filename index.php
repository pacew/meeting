<?php

require_once("app.php");

$anon_ok = 1;

pstart ();

$arg_room_name = trim(@$_REQUEST['room_name']);
$arg_days = intval(@$_REQUEST['days']);
$arg_secret = trim(@$_REQUEST['secret']);

if ($arg_room_name) {
    $base_name = preg_replace("/[^a-zA-Z0-9]/", '', $arg_room_name);

    if (($days = $arg_days) == 0)
        $days = 10;
    if ($days < 1)
        $days = 1;
    if ($days > 10)
        $days = 10;

    $date = strftime ("%Y%m%d");

    $full_name = sprintf ("%s%sx%02dx", $base_name, $date, $days);

    $msg = strtolower($full_name);
    $want = "hello20200625x10x";
    $signed = hash_hmac('sha1', $msg, $arg_secret);
    $signed = substr ($signed, 0, 8);

    $result = $full_name . $signed;

    $t = sprintf ("https://jitsi.pacew.org/%s", rawurlencode($result));

    $body .= "<div class='meeting_link'>\n";
    $body .= mklink ($t, $t);
    $body .= "</div>\n";
    
    $body .= "<div class='meeting_box'>\n";
    $body .= sprintf ("<input type='text' readonly='readonly' size='50'"
        ." value='%s' />\n", h($t));
    $body .= "</div>\n";

}


$body .= "<h1>create a meeting room</h2>\n";

$body .= "<form action='index.php'>\n";
$body .= "<table class='twocol'>\n";
$body .= "<tr><th>Room name (no need to be secret or long)</th><td>";
$body .= "<input type='text' name='room_name' />\n";
$body .= "</td></tr>\n";
$body .= "<tr><th>Number of days for the link to be valid (1-10, default 10)"
      ." </th><td>";
$body .= "<input type='text' name='days' />\n";
$body .= "</td></tr>\n";
$body .= "<tr><th>Meeting server secret</th><td>";
$body .= "<input type='text' name='secret' />\n";
$body .= "</td></tr>\n";
$body .= "<tr><th></th><td><input type='submit' value='Create meeting' />\n"
      ."</td></tr>\n";

$body .= "</table>\n";
$body .= "</form>\n";



pfinish ();
