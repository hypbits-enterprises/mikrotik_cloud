<?php

namespace Tests\Feature;

use Tests\TestCase;

class RouterConnectionLostViewTest extends TestCase
{
    private function viewContent(string $path): string
    {
        return file_get_contents(resource_path("views/{$path}"));
    }

    private function assertViewDisplaysErrorRouter(string $template): void
    {
        $content = $this->viewContent($template);
        $this->assertStringContainsString(
            "session('error_router')",
            $content,
            "{$template} must check the error_router session key"
        );
    }

    // --- Registration views (these use network_presence, must also show error_router) ---

    public function test_new_pppoe_client_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('newPPOEclient.blade.php');
    }

    public function test_new_static_client_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('new_client_static.blade.php');
    }

    public function test_quick_register_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('quickregister.blade.php');
    }

    public function test_new_client_cloud_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('newClient.blade.php');
    }

    // --- Router views (already had error_router before this feature) ---

    public function test_router_info_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('router/infor.blade.php');
    }

    public function test_my_router_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('router/myRouter.blade.php');
    }

    public function test_client_profile_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('clients/client-profile.blade.php');
    }

    public function test_new_router_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('newrouter.blade.php');
    }

    public function test_router_setup_view_displays_error_router()
    {
        $this->assertViewDisplaysErrorRouter('RouterSetup.blade.php');
    }

    // --- Handler correctly sets the error_router flash key ---

    public function test_handler_flashes_error_router_key_not_network_presence()
    {
        $handler = $this->app->make(\App\Exceptions\Handler::class);
        $request = \Illuminate\Http\Request::create('/test', 'POST');

        $handler->render($request, new \App\Exceptions\RouterConnectionLostException());

        $this->assertNotNull(session('error_router'), 'Handler must flash error_router');
        $this->assertNull(session('network_presence'), 'Handler must not flash network_presence');
    }

    public function test_message_text_matches_what_views_will_display()
    {
        $handler = $this->app->make(\App\Exceptions\Handler::class);
        $request = \Illuminate\Http\Request::create('/test', 'POST');

        $handler->render($request, new \App\Exceptions\RouterConnectionLostException());

        $this->assertStringContainsString(
            'Connection to the router was lost',
            session('error_router')
        );
        $this->assertStringContainsString(
            'The operation was not completed',
            session('error_router')
        );
        $this->assertStringContainsString(
            'Please try again',
            session('error_router')
        );
    }
}
