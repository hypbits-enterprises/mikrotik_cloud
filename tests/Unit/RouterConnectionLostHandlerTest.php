<?php

namespace Tests\Unit;

use App\Exceptions\Handler;
use App\Exceptions\RouterConnectionLostException;
use Illuminate\Http\Request;
use Tests\TestCase;

class RouterConnectionLostHandlerTest extends TestCase
{
    private function makeHandler(): Handler
    {
        return $this->app->make(Handler::class);
    }

    private function makePageRequest(string $url = '/test'): Request
    {
        return Request::create($url, 'POST');
    }

    private function makeAjaxRequest(string $url = '/test'): Request
    {
        $request = Request::create($url, 'POST');
        $request->headers->set('X-Requested-With', 'XMLHttpRequest');
        return $request;
    }

    // --- AJAX responses ---

    public function test_ajax_request_receives_html_error_snippet()
    {
        $handler = $this->makeHandler();
        $response = $handler->render($this->makeAjaxRequest(), new RouterConnectionLostException());

        $this->assertEquals(200, $response->getStatusCode());
        $content = $response->getContent();
        $this->assertStringContainsString('text-danger', $content);
        $this->assertStringContainsString('Connection to the router was lost', $content);
        $this->assertStringContainsString('The operation was not completed', $content);
        $this->assertStringContainsString('Please try again', $content);
    }

    public function test_ajax_response_contains_warning_icon()
    {
        $handler = $this->makeHandler();
        $response = $handler->render($this->makeAjaxRequest(), new RouterConnectionLostException());

        $this->assertStringContainsString('fa-exclamation-triangle', $response->getContent());
    }

    public function test_ajax_response_is_html_not_json()
    {
        $handler = $this->makeHandler();
        $response = $handler->render($this->makeAjaxRequest(), new RouterConnectionLostException());

        $this->assertStringContainsString('<p', $response->getContent());
    }

    // --- Page-load (redirect) responses ---

    public function test_page_request_flashes_error_router_message()
    {
        $handler = $this->makeHandler();
        $handler->render($this->makePageRequest(), new RouterConnectionLostException());

        $this->assertEquals(
            'Connection to the router was lost while processing your request. The operation was not completed. Please try again.',
            session('error_router')
        );
    }

    public function test_page_request_returns_redirect()
    {
        $handler = $this->makeHandler();
        $response = $handler->render($this->makePageRequest(), new RouterConnectionLostException());

        $this->assertEquals(302, $response->getStatusCode());
    }

    // --- Other exceptions are not swallowed ---

    public function test_unrelated_exception_is_not_treated_as_connection_lost()
    {
        $handler = $this->makeHandler();
        $response = $handler->render($this->makePageRequest(), new \InvalidArgumentException('unrelated'));

        // Must NOT flash the connection-lost message
        $this->assertNotEquals(
            'Connection to the router was lost while processing your request. The operation was not completed. Please try again.',
            session('error_router')
        );

        // Must NOT be a redirect (parent handler returns an error page, not a redirect)
        $this->assertNotEquals(302, $response->getStatusCode());
    }

    // --- dontReport check ---

    public function test_exception_is_in_dont_report_list()
    {
        $handler = $this->makeHandler();
        $dontReport = $this->getDontReportList($handler);

        $this->assertContains(
            RouterConnectionLostException::class,
            $dontReport,
            'RouterConnectionLostException should be in $dontReport to avoid log spam'
        );
    }

    private function getDontReportList(Handler $handler): array
    {
        $reflection = new \ReflectionClass($handler);
        $property = $reflection->getProperty('dontReport');
        $property->setAccessible(true);
        return $property->getValue($handler);
    }
}
