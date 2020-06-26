<?php

require_once("app.php");

$anon_ok = 1;

pstart ();

$arg_room_name = trim(@$_REQUEST['room_name']);
$arg_days = intval(@$_REQUEST['days']);
$arg_secret = trim(@$_REQUEST['secret']);

$arg_days = 10;



$body .= "<h1>Welcome</h1>\n";

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

    $body .= "<p>Here is your meeting link, written two ways (depending"
          ." on what you're doing, one or the other will be easier"
          ." to copy).</p>\n";

    $body .= "<p>This link can be used any number of times during"
          ." the next 10 days.  If you mistyped the password,"
          ." you'll still get a link here, but it won't work on the"
          ." video server, so you should click it now and make"
          ." sure it works.  Have a good meeting!</p>\n";

    $body .= "<div class='meeting_link'>\n";
    $body .= mklink ($t, $t);
    $body .= "</div>\n";
    
    $body .= "<div class='meeting_box'>\n";
    $body .= sprintf ("<input type='text' readonly='readonly' size='50'"
        ." value='%s' />\n", h($t));
    $body .= "</div>\n";

} else {
    $body .= "<p>This is a private jitsi video conference server"
          ." that I'm sharing with friends.  Please let me know if"
          ." you use it and how it preforms.</p>\n";
    $body .= "<p>Fill in the form to get a link for a meeting room,"
          ." then distribute it by email or whatever.</p>\n";
    $body .= "<p>I will have given you the Server passcode when I told"
          ." you about this site.</p>\n";
}


$body .= "<form action='index.php'>\n";
$body .= "<table class='twocol'>\n";
$body .= "<tr><th>Room name (no need to be secret or long)</th><td>";
$body .= "<input type='text' name='room_name' />\n";
$body .= "</td></tr>\n";
$body .= "<tr><th>Server passcode</th><td>";
$body .= "<input type='text' name='secret' />\n";
$body .= "</td></tr>\n";
$body .= "<tr><th></th><td><input type='submit' value='Create meeting' />\n"
      ."</td></tr>\n";

$body .= "</table>\n";
$body .= "</form>\n";



pfinish ();
