<?php

namespace App\Http\Controllers\Hotspot;

use App\Classes\HotspotAccess;
use App\Classes\HotspotPlans;
use App\Classes\HotspotPortal;
use App\Http\Controllers\Controller;
use App\Http\Controllers\login;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

/**
 * Hotspot Branding: what the captive portal looks like. One org default row (router_id NULL) in
 * hotspot_login_pages and optional per-router rows; a router row's filled-in fields override the
 * default. The portal renders branding live, so saving needs no router change.
 */
class Branding extends Controller
{
    // uploads up to this size; the portal gets a small copy (HotspotPortal::smallLogo)
    const LOGO_MAX_KB = 2048;
    const FIELDS = ["primary_color", "secondary_color", "title", "welcome_text", "support_phone", "redirect_url"];

    private function useOrgDb()
    {
        $change_db = new login();
        $change_db->change_db();
    }

    private function readOnly()
    {
        return HotspotAccess::readOnly(session("priviledges"), "Hotspot Branding");
    }

    private function organization()
    {
        return DB::table("organizations")->where("organization_id", session("organization_id"))->first();
    }

    // router_id from the request: null = org default; only managed, non-deleted routers
    private function scope(Request $request)
    {
        $router_id = $request->input("router_id");
        if ($router_id === null || $router_id === "") {
            return [null, null];
        }
        $router = DB::connection("mysql2")->table("remote_routers")->where("router_id", $router_id)->where("deleted", 0)->where("hotspot_enabled", 1)->first();
        return $router ? [(int) $router->router_id, $router] : [false, null];
    }

    function index(Request $request)
    {
        $this->useOrgDb();
        [$router_id, $router] = $this->scope($request);
        if ($router_id === false) {
            session()->flash("error_hotspot", "Choose a router that Hypbits manages the hotspot on.");
            return redirect(route("hotspot.branding"));
        }
        $routers = DB::connection("mysql2")->table("remote_routers")->where("deleted", 0)->where("hotspot_enabled", 1)->orderBy("router_name")->get();
        $page = DB::connection("mysql2")->table("hotspot_login_pages")->where("deleted", 0)
            ->when($router_id, function ($query) use ($router_id) {
                $query->where("router_id", $router_id);
            }, function ($query) {
                $query->whereNull("router_id");
            })->first();
        $overrides = DB::connection("mysql2")->table("hotspot_login_pages")->where("deleted", 0)->whereNotNull("router_id")->pluck("router_id")->all();
        $preview_router = $router ?: $routers->first();
        return view("hotspot.branding", [
            "router_id" => $router_id,
            "router" => $router,
            "routers" => $routers,
            "page" => $page,
            "overrides" => $overrides,
            // what applies to a field left empty: the org default for a router, else the built-in text
            "defaults" => $router_id ? HotspotPortal::branding($this->organization(), null) : HotspotPortal::builtIn($this->organization()),
            "effective" => HotspotPortal::branding($this->organization(), $router_id),
            "preview_router" => $preview_router,
            "readonly" => $this->readOnly(),
            "logo_max_kb" => self::LOGO_MAX_KB,
        ]);
    }

    function save(Request $request)
    {
        $this->useOrgDb();
        [$router_id, $router] = $this->scope($request);
        $back = redirect(route("hotspot.branding", $router_id ? ["router_id" => $router_id] : []))->withInput();
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot branding.");
            return $back;
        }
        if ($router_id === false) {
            session()->flash("error_hotspot", "Choose a router that Hypbits manages the hotspot on.");
            return redirect(route("hotspot.branding"));
        }
        [$values, $error] = self::validated($request);
        if ($error) {
            session()->flash("error_hotspot", $error);
            return $back;
        }
        $logo = $request->file("logo");
        if ($logo) {
            if (!$logo->isValid() || !in_array(strtolower($logo->getClientOriginalExtension()), ["png", "jpg", "jpeg", "webp"], true)
                || !in_array($logo->getMimeType(), ["image/png", "image/jpeg", "image/webp"], true)) {
                session()->flash("error_hotspot", "The logo must be a PNG, JPG or WEBP image.");
                return $back;
            }
            if ($logo->getSize() > self::LOGO_MAX_KB * 1024) {
                session()->flash("error_hotspot", "The logo must be 2 MB or smaller.");
                return $back;
            }
        }

        $now = now()->format('YmdHis');
        $table = DB::connection("mysql2")->table("hotspot_login_pages");
        $page = (clone $table)->when($router_id, function ($query) use ($router_id) {
            $query->where("router_id", $router_id);
        }, function ($query) {
            $query->whereNull("router_id");
        })->first();
        if ($page) {
            (clone $table)->where("page_id", $page->page_id)->update($values + ["deleted" => 0, "date_changed" => $now]);
            $page_id = $page->page_id;
        } else {
            $page_id = (clone $table)->insertGetId($values + ["router_id" => $router_id, "date_created" => $now, "date_changed" => $now, "deleted" => 0]);
        }

        $old_logo = $page ? $page->logo_path : null;
        if ($logo) {
            $name = preg_replace('/[^a-z0-9_]/i', '', session("database_name")) . "-" . $page_id . "-" . $now . ".png";
            $path = HotspotPortal::smallLogo($logo->getRealPath(), $name);
            if (!$path) {
                session()->flash("error_hotspot", "The other settings were saved, but the logo could not be read as an image.");
                return $back;
            }
            (clone $table)->where("page_id", $page_id)->update(["logo_path" => $path]);
            self::deleteLogo($old_logo);
        } elseif ($request->boolean("remove_logo")) {
            (clone $table)->where("page_id", $page_id)->update(["logo_path" => null]);
            self::deleteLogo($old_logo);
        }

        session()->flash("success_hotspot", ($router ? "Branding for " . $router->router_name : "Default branding") . " saved. The portal shows it straight away.");
        return redirect(route("hotspot.branding", $router_id ? ["router_id" => $router_id] : []));
    }

    // a router goes back to the org default branding
    function destroy(Request $request)
    {
        $this->useOrgDb();
        if ($this->readOnly()) {
            session()->flash("error_hotspot", "You have read-only access to hotspot branding.");
            return redirect(route("hotspot.branding"));
        }
        [$router_id, $router] = $this->scope($request);
        if (!$router_id) {
            session()->flash("error_hotspot", "Only a router's own branding can be removed.");
            return redirect(route("hotspot.branding"));
        }
        $page = DB::connection("mysql2")->table("hotspot_login_pages")->where("router_id", $router_id)->where("deleted", 0)->first();
        if ($page) {
            DB::connection("mysql2")->table("hotspot_login_pages")->where("page_id", $page->page_id)
                ->update(["deleted" => 1, "logo_path" => null, "date_changed" => now()->format('YmdHis')]);
            self::deleteLogo($page->logo_path);
        }
        session()->flash("success_hotspot", $router->router_name . " now uses the default branding.");
        return redirect(route("hotspot.branding"));
    }

    // the portal page with the form's current values, for the live preview (never logs in). An
    // empty field shows what applies when it is left empty; a newly chosen logo file is shown by
    // the page itself (postMessage), before it is uploaded.
    function preview(Request $request)
    {
        $this->useOrgDb();
        [$router_id, $router] = $this->scope($request);
        $organization = $this->organization();
        $branding = $router_id ? HotspotPortal::branding($organization, null) : HotspotPortal::builtIn($organization);
        foreach (["primary_color", "secondary_color", "title", "welcome_text", "support_phone"] as $field) {
            $value = trim((string) $request->query($field, ""));
            if ($value !== "" && (!str_ends_with($field, "_color") || preg_match('/^#[0-9a-fA-F]{6}$/', $value))) {
                $branding[$field] = mb_substr($value, 0, 500);
            }
        }
        $own_logo = DB::connection("mysql2")->table("hotspot_login_pages")->where("deleted", 0)
            ->when($router_id, function ($query) use ($router_id) {
                $query->where("router_id", $router_id);
            }, function ($query) {
                $query->whereNull("router_id");
            })->value("logo_path");
        if ($own_logo && !$request->boolean("remove_logo")) {
            $branding['logo_path'] = $own_logo;
        }
        return view("hotspot.portal.login", [
            "key" => $router && $router->hotspot_key ? $router->hotspot_key : "preview",
            "branding" => $branding,
            "plans" => HotspotPlans::forPortal(),
            "login_url" => null,
            "mac" => null,
            "dst" => null,
            "error" => "",
            "logged_out" => false,
            "code" => null,
            "preview" => true,
        ]);
    }

    /**
     * @return array [values for hotspot_login_pages, error message or null]
     */
    private static function validated(Request $request)
    {
        $values = [];
        foreach (self::FIELDS as $field) {
            $value = trim((string) $request->input($field, ""));
            $values[$field] = $value === "" ? null : $value;
        }
        foreach (["primary_color", "secondary_color"] as $field) {
            if ($values[$field] !== null && !preg_match('/^#[0-9a-fA-F]{6}$/', $values[$field])) {
                return [null, "Colours must be hex values like #1e9ff2."];
            }
        }
        if ($values['title'] !== null && mb_strlen($values['title']) > 150) {
            return [null, "The title can be at most 150 characters."];
        }
        if ($values['welcome_text'] !== null && mb_strlen($values['welcome_text']) > 500) {
            return [null, "The welcome text can be at most 500 characters."];
        }
        if ($values['support_phone'] !== null && !preg_match('/^\+?[0-9 ]{7,20}$/', $values['support_phone'])) {
            return [null, "Enter the support phone as digits, e.g. 0712345678."];
        }
        if ($values['redirect_url'] !== null && (mb_strlen($values['redirect_url']) > 255 || !HotspotPortal::safeLink($values['redirect_url']))) {
            return [null, "The page after login must be a full link starting with http:// or https://, or be left empty."];
        }
        return [$values, null];
    }

    private static function deleteLogo($path)
    {
        if ($path && str_starts_with($path, HotspotPortal::LOGO_DIR . "/") && !str_contains($path, "..")) {
            File::delete(public_path($path));
        }
    }
}
