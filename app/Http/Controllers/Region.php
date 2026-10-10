<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

date_default_timezone_set('Africa/Nairobi');
class Region extends Controller
{
    function openRegions(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $regions = $this->getRegionsList();
        $subregions = $this->getSubregionsList();

        // count clients currently assigned to each subregion (`client_tables.region` holds the subregion name)
        $counts = DB::connection("mysql2")->select("SELECT `region`, COUNT(*) AS total FROM `client_tables` WHERE `deleted` = '0' AND `region` IS NOT NULL AND `region` != '' GROUP BY `region`");
        $subregion_counts = [];
        foreach ($counts as $count) {
            $subregion_counts[$count->region] = $count->total;
        }

        // roll the subregion figures up to their regions
        $region_subregion_counts = [];
        $region_client_counts = [];
        foreach ($subregions as $subregion) {
            $region_subregion_counts[$subregion->region] = ($region_subregion_counts[$subregion->region] ?? 0) + 1;
            $region_client_counts[$subregion->region] = ($region_client_counts[$subregion->region] ?? 0) + ($subregion_counts[$subregion->name] ?? 0);
        }

        return view("regions", [
            "regions" => $regions,
            "subregions" => $subregions,
            "subregion_counts" => $subregion_counts,
            "region_subregion_counts" => $region_subregion_counts,
            "region_client_counts" => $region_client_counts,
        ]);
    }

    // ─── Regions ─────────────────────────────────────────────────────────────

    function addRegion(Request $request){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $region_name = trim($request->input("region_name"));
        if (strlen($region_name) == 0) {
            session()->flash("region_error", "Kindly provide a region name!");
            return redirect("/Regions");
        }

        $regions = $this->getRegionsList();
        if ($this->findByName($regions, $region_name) !== null) {
            session()->flash("region_error", "A region with that name already exists!");
            return redirect("/Regions");
        }

        $regions[] = (object) ["name" => $region_name, "index" => $this->nextIndex($regions)];
        $this->saveSettingsList("Regions", $regions);

        session()->flash("region_success", "Region \"" . $region_name . "\" added successfully!");
        return redirect("/Regions");
    }

    function updateRegion(Request $request){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $region_index = $request->input("region_index");
        $region_name = trim($request->input("region_name"));

        if (strlen($region_name) == 0) {
            session()->flash("region_error", "Kindly provide a region name!");
            return redirect("/Regions");
        }

        $regions = $this->getRegionsList();
        $region = $this->findByIndex($regions, $region_index);
        if ($region === null) {
            session()->flash("region_error", "The region you are trying to edit cannot be found!");
            return redirect("/Regions");
        }

        $duplicate = $this->findByName($regions, $region_name);
        if ($duplicate !== null && $duplicate !== $region) {
            session()->flash("region_error", "A region with that name already exists!");
            return redirect("/Regions");
        }

        $old_name = $region->name;
        $region->name = $region_name;
        $this->saveSettingsList("Regions", $regions);

        // subregions point at their region by name; clients only hold the subregion so they need no change
        if ($old_name !== $region_name) {
            $subregions = $this->getSubregionsList();
            foreach ($subregions as $subregion) {
                if ($subregion->region === $old_name) {
                    $subregion->region = $region_name;
                }
            }
            $this->saveSettingsList("Subregions", $subregions);
        }

        session()->flash("region_success", "Region renamed to \"" . $region_name . "\" successfully!");
        return redirect("/Regions");
    }

    function deleteRegion($region_index){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $regions = $this->getRegionsList();
        $region = $this->findByIndex($regions, $region_index);
        if ($region === null) {
            session()->flash("region_error", "The region you are trying to delete cannot be found!");
            return redirect("/Regions");
        }

        $subregion_total = 0;
        foreach ($this->getSubregionsList() as $subregion) {
            if ($subregion->region === $region->name) {
                $subregion_total++;
            }
        }
        if ($subregion_total > 0) {
            session()->flash("region_error", "\"" . $region->name . "\" still has " . $subregion_total . " subregion(s). Delete or move them to another region first!");
            return redirect("/Regions");
        }

        $this->saveSettingsList("Regions", array_values(array_filter($regions, fn($r) => $r !== $region)));

        session()->flash("region_success", "Region \"" . $region->name . "\" deleted successfully!");
        return redirect("/Regions");
    }

    // ─── Subregions ──────────────────────────────────────────────────────────

    function addSubregion(Request $request){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $subregion_name = trim($request->input("subregion_name"));
        $region_name = trim($request->input("region_name"));

        if (strlen($subregion_name) == 0) {
            session()->flash("subregion_error", "Kindly provide a subregion name!");
            return redirect("/Regions");
        }
        if ($this->findByName($this->getRegionsList(), $region_name) === null) {
            session()->flash("subregion_error", "Kindly select the region this subregion belongs to!");
            return redirect("/Regions");
        }

        // subregion names are unique across all regions: clients store only the subregion name
        $subregions = $this->getSubregionsList();
        if ($this->findByName($subregions, $subregion_name) !== null) {
            session()->flash("subregion_error", "A subregion with that name already exists!");
            return redirect("/Regions");
        }

        $subregions[] = (object) ["name" => $subregion_name, "index" => $this->nextIndex($subregions), "region" => $region_name];
        $this->saveSettingsList("Subregions", $subregions);

        session()->flash("subregion_success", "Subregion \"" . $subregion_name . "\" added to \"" . $region_name . "\" successfully!");
        return redirect("/Regions");
    }

    function updateSubregion(Request $request){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $subregion_index = $request->input("subregion_index");
        $subregion_name = trim($request->input("subregion_name"));
        $region_name = trim($request->input("region_name"));

        if (strlen($subregion_name) == 0) {
            session()->flash("subregion_error", "Kindly provide a subregion name!");
            return redirect("/Regions");
        }
        if ($this->findByName($this->getRegionsList(), $region_name) === null) {
            session()->flash("subregion_error", "Kindly select the region this subregion belongs to!");
            return redirect("/Regions");
        }

        $subregions = $this->getSubregionsList();
        $subregion = $this->findByIndex($subregions, $subregion_index);
        if ($subregion === null) {
            session()->flash("subregion_error", "The subregion you are trying to edit cannot be found!");
            return redirect("/Regions");
        }

        $duplicate = $this->findByName($subregions, $subregion_name);
        if ($duplicate !== null && $duplicate !== $subregion) {
            session()->flash("subregion_error", "A subregion with that name already exists!");
            return redirect("/Regions");
        }

        $old_name = $subregion->name;
        $subregion->name = $subregion_name;
        $subregion->region = $region_name;
        $this->saveSettingsList("Subregions", $subregions);

        // keep clients already assigned to this subregion pointed at the new name
        if ($old_name !== $subregion_name) {
            DB::connection("mysql2")->update("UPDATE `client_tables` SET `region` = ? WHERE `region` = ?", [$subregion_name, $old_name]);
        }

        session()->flash("subregion_success", "Subregion \"" . $subregion_name . "\" updated successfully!");
        return redirect("/Regions");
    }

    function deleteSubregion($subregion_index){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $subregions = $this->getSubregionsList();
        $subregion = $this->findByIndex($subregions, $subregion_index);
        if ($subregion === null) {
            session()->flash("subregion_error", "The subregion you are trying to delete cannot be found!");
            return redirect("/Regions");
        }

        $client_total = DB::connection("mysql2")->select("SELECT COUNT(*) AS total FROM `client_tables` WHERE `deleted` = '0' AND `region` = ?", [$subregion->name])[0]->total;
        if ($client_total > 0) {
            session()->flash("subregion_error", "\"" . $subregion->name . "\" still has " . $client_total . " client(s). Move them to another subregion first!");
            return redirect("/Regions");
        }

        $this->saveSettingsList("Subregions", array_values(array_filter($subregions, fn($s) => $s !== $subregion)));

        // clear it from deleted clients too so no stale name is left behind
        DB::connection("mysql2")->update("UPDATE `client_tables` SET `region` = NULL WHERE `region` = ?", [$subregion->name]);

        session()->flash("subregion_success", "Subregion \"" . $subregion->name . "\" deleted successfully!");
        return redirect("/Regions");
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function saveSettingsList($keyword, $list){
        $existing = DB::connection("mysql2")->select("SELECT `id` FROM `settings` WHERE `deleted` = '0' AND `keyword` = ?", [$keyword]);
        if (count($existing) > 0) {
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = ?, `date_changed` = ? WHERE `keyword` = ?", [json_encode(array_values($list)), date("YmdHis"), $keyword]);
        } else {
            DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`,`value`,`status`) VALUES (?, ?, '1')", [$keyword, json_encode(array_values($list))]);
        }
    }

    private function findByName($list, $name){
        foreach ($list as $item) {
            if (strtolower($item->name) == strtolower($name)) {
                return $item;
            }
        }
        return null;
    }

    private function findByIndex($list, $index){
        foreach ($list as $item) {
            if ((string) $item->index === (string) $index) {
                return $item;
            }
        }
        return null;
    }

    private function nextIndex($list){
        $next_index = 0;
        foreach ($list as $item) {
            if ($item->index >= $next_index) {
                $next_index = $item->index + 1;
            }
        }
        return $next_index;
    }
}
