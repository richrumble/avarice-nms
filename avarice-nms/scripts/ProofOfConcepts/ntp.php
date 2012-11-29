<?php
// -------------- TIME -------------- (RFC-868)
//based off of http://www.kloth.net/software/timesrv1.php
  $time_servers = array("nist1-chi.ustiming.org",
                        "time.nist.gov");
  // a flag and number of servers
  $valid_response = false;
  $ts_count = sizeof($time_servers);
  $time_adjustment = 0;

  for ($i=0; $i<$ts_count; $i++) {
    $time_server = $time_servers[$i];
    $fp = fsockopen($time_server, 37, $errno, $errstr, 30);
    if (!$fp) {
      echo "$time_server: $errstr ($errno)\n";
      echo "Trying next available server...\n\n";
    } else {
      $data = NULL;
      while (!feof($fp)) {
        $data .= fgets($fp, 128);
      }
      fclose($fp);
      // we have a response...is it valid? (4 char string -> 32 bits)
      if (strlen($data) != 4) {
        echo "NTP Server {$time_server} returned an invalid response.\n";
        if ($i != ($ts_count - 1)) {
          echo "Trying next available server...\n\n";
        } else {
          echo "Time server list exhausted\n";
        }
      } else {
        $valid_response = true;
        break;
      }
    }
  }

  if ($valid_response) {
    // time server response is a string - convert to numeric
    $NTPtime = ord($data{0})*pow(256, 3) + ord($data{1})*pow(256, 2) + ord($data{2})*256 + ord($data{3});

    // convert the seconds to the present date & time
    // 2840140800 = Thu, 1 Jan 2060 00:00:00 UTC
    // 631152000  = Mon, 1 Jan 1990 00:00:00 UTC
    $TimeFrom1990 = $NTPtime - 2840140800;
    $TimeNow = $TimeFrom1990 + 631152000;

    // set the system time
    $TheDate = date("m/d/Y H:i:s", $TimeNow + $time_adjustment);

    // set the hardware clock (optional) - you may want to comment this out
    echo "The server's date and time was set to" . $TheDate . "\n";
  } else {
    echo "The system time could not be updated. No time servers available.\n";
  }
  

// -------------- NTP -------------- (RFC 1305)
$ntp_srvr = "pool.ntp.org";

function ntp_time($host) {
   
  // Create a socket and connect to NTP server
  $sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
  socket_connect($sock, $host, 123);
   
  // Send request
  $msg = "\010" . str_repeat("\0", 47);
  socket_send($sock, $msg, strlen($msg), 0);
   
  // Receive response and close socket
  socket_recv($sock, $recv, 48, MSG_WAITALL);
  socket_close($sock);
 
  // Interpret response
  $data = unpack('N12', $recv);
  $timestamp = sprintf('%u', $data[9]);
   
  // NTP is number of seconds since 0000 UT on 1 January 1900
  // Unix time is seconds since 0000 UT on 1 January 1970
  $timestamp -= 2208988800;
   
  return $timestamp;
}
$wtit = ntp_time($ntp_srvr);
print "\r\n" . $wtit;
?>