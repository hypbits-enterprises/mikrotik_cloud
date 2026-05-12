<?php

namespace Tests\Unit;

use App\Classes\routeros_api;
use App\Exceptions\RouterConnectionLostException;
use Tests\TestCase;

class RouterosApiConnectionLostTest extends TestCase
{
    private function makePair(): array
    {
        $pair = stream_socket_pair(STREAM_PF_UNIX, STREAM_SOCK_STREAM, STREAM_IPPROTO_IP);
        $this->assertNotFalse($pair, 'stream_socket_pair() is not supported on this platform');
        return $pair;
    }

    // --- read() tests ---

    public function test_read_throws_when_remote_end_closes_during_active_session()
    {
        [$clientSocket, $serverSocket] = $this->makePair();

        $api = new routeros_api();
        $api->socket = $clientSocket;
        $api->connected = true;

        fclose($serverSocket); // simulate router dropping the connection

        $this->expectException(RouterConnectionLostException::class);
        $api->read(false);
    }

    public function test_read_does_not_throw_during_login_handshake()
    {
        [$clientSocket, $serverSocket] = $this->makePair();

        $api = new routeros_api();
        $api->socket = $clientSocket;
        $api->connected = false; // still in the login phase

        fclose($serverSocket);

        // Should return an empty array without throwing
        $result = $api->read(false);
        $this->assertIsArray($result);

        fclose($clientSocket);
    }

    public function test_read_sets_connected_false_before_throwing()
    {
        [$clientSocket, $serverSocket] = $this->makePair();

        $api = new routeros_api();
        $api->socket = $clientSocket;
        $api->connected = true;

        fclose($serverSocket);

        try {
            $api->read(false);
            $this->fail('Expected RouterConnectionLostException was not thrown');
        } catch (RouterConnectionLostException $e) {
            $this->assertFalse($api->connected, 'connected flag must be cleared after a drop');
        }

        fclose($clientSocket);
    }

    public function test_read_throws_only_once_on_repeated_calls_after_drop()
    {
        [$clientSocket, $serverSocket] = $this->makePair();

        $api = new routeros_api();
        $api->socket = $clientSocket;
        $api->connected = true;

        fclose($serverSocket);

        // First call should throw
        try {
            $api->read(false);
            $this->fail('Expected RouterConnectionLostException on first call');
        } catch (RouterConnectionLostException $e) {
            $this->assertFalse($api->connected);
        }

        // Second call should NOT throw because connected is now false
        $result = $api->read(false);
        $this->assertIsArray($result);

        fclose($clientSocket);
    }

    // --- write() tests ---

    public function test_write_throws_when_socket_is_not_writable_during_active_session()
    {
        // A read-only stream causes fwrite() to return false
        $readOnlyStream = fopen('/dev/null', 'r');

        $api = new routeros_api();
        $api->socket = $readOnlyStream;
        $api->connected = true;

        $this->expectException(RouterConnectionLostException::class);
        $api->write('/ppp/secret/add');
    }

    public function test_write_does_not_throw_during_login_handshake_on_bad_socket()
    {
        $readOnlyStream = fopen('/dev/null', 'r');

        $api = new routeros_api();
        $api->socket = $readOnlyStream;
        $api->connected = false; // login phase

        // Should not throw, should just return false
        $result = $api->write('/login', false);
        $this->assertFalse($result);
    }

    public function test_write_sets_connected_false_before_throwing()
    {
        $readOnlyStream = fopen('/dev/null', 'r');

        $api = new routeros_api();
        $api->socket = $readOnlyStream;
        $api->connected = true;

        try {
            $api->write('/ppp/secret/add');
            $this->fail('Expected RouterConnectionLostException was not thrown');
        } catch (RouterConnectionLostException $e) {
            $this->assertFalse($api->connected);
        }
    }

    // --- comm() tests ---

    public function test_comm_propagates_exception_from_write()
    {
        $readOnlyStream = fopen('/dev/null', 'r');

        $api = new routeros_api();
        $api->socket = $readOnlyStream;
        $api->connected = true;

        $this->expectException(RouterConnectionLostException::class);
        $api->comm('/ppp/secret/add', ['name' => 'test', 'password' => '1234']);
    }

    public function test_comm_propagates_exception_from_read()
    {
        [$clientSocket, $serverSocket] = $this->makePair();

        $api = new routeros_api();
        $api->socket = $clientSocket;
        $api->connected = true;

        fclose($serverSocket);

        $this->expectException(RouterConnectionLostException::class);
        $api->comm('/ppp/secret/print');
    }
}
