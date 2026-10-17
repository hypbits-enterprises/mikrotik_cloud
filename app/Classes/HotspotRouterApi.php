<?php

namespace App\Classes;

/**
 * RouterOS API client for hotspot code. Same as routeros_api, but read() waits for the whole reply:
 * it returns when the sentence after "!done" / "!fatal" ends, so a slow router (e.g. a 600 MHz
 * RB951) can't make a reply spill into the next command, and an "add" reply ("!done" then
 * "=ret=*id") can't hang it. A router that stops answering ends the read after two socket timeouts.
 * The crontab agent (hotspot_router_agent.php) has the same reader. routeros_api.php itself is not
 * changed because the existing router pages use it.
 */
class HotspotRouterApi extends routeros_api
{
    function read($parse = true)
    {
        $RESPONSE = array();
        $seen_done = false;
        $timeouts = 0;
        while (true) {
            $first = fread($this->socket, 1);
            if ($first === false || $first === '') {
                $info = stream_get_meta_data($this->socket);
                if (!empty($info['eof']) || ++$timeouts >= 2) {
                    $this->connected = false;
                    break;
                }
                continue;
            }
            $timeouts = 0;
            $BYTE = ord($first);
            if ($BYTE & 128) {
                if (($BYTE & 192) == 128) {
                    $LENGTH = (($BYTE & 63) << 8) + ord(fread($this->socket, 1));
                } elseif (($BYTE & 224) == 192) {
                    $LENGTH = (($BYTE & 31) << 8) + ord(fread($this->socket, 1));
                    $LENGTH = ($LENGTH << 8) + ord(fread($this->socket, 1));
                } elseif (($BYTE & 240) == 224) {
                    $LENGTH = (($BYTE & 15) << 8) + ord(fread($this->socket, 1));
                    $LENGTH = ($LENGTH << 8) + ord(fread($this->socket, 1));
                    $LENGTH = ($LENGTH << 8) + ord(fread($this->socket, 1));
                } else {
                    $LENGTH = ord(fread($this->socket, 1));
                    $LENGTH = ($LENGTH << 8) + ord(fread($this->socket, 1));
                    $LENGTH = ($LENGTH << 8) + ord(fread($this->socket, 1));
                    $LENGTH = ($LENGTH << 8) + ord(fread($this->socket, 1));
                }
            } else {
                $LENGTH = $BYTE;
            }

            if ($LENGTH == 0) {
                // end of a sentence: the reply is complete once "!done" or "!fatal" was seen
                if ($seen_done) {
                    break;
                }
                continue;
            }

            $word = '';
            while (strlen($word) < $LENGTH) {
                $chunk = fread($this->socket, $LENGTH - strlen($word));
                if ($chunk === false || $chunk === '') {
                    break;
                }
                $word .= $chunk;
            }
            $RESPONSE[] = $word;
            if ($word == "!done" || $word == "!fatal") {
                $seen_done = true;
            }
        }
        return $parse ? $this->parse_response($RESPONSE) : $RESPONSE;
    }
}
