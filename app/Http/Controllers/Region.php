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

        // count clients currently assigned to each region
        $counts = DB::connection("mysql2")->select("SELECT `region`, COUNT(*) AS total FROM `client_tables` WHERE `deleted` = '0' AND `region` IS NOT NULL AND `region` != '' GROUP BY `region`");
        $region_counts = [];
        foreach ($counts as $count) {
            $region_counts[$count->region] = $count->total;
        }

        return view("regions", ["regions" => $regions, "region_counts" => $region_counts]);
    }

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

        foreach ($regions as $region) {
            if (strtolower($region->name) == strtolower($region_name)) {
                session()->flash("region_error", "A region with that name already exists!");
                return redirect("/Regions");
            }
        }

        $existing = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'Regions'");

        $next_index = 0;
        foreach ($regions as $region) {
            if ($region->index >= $next_index) {
                $next_index = $region->index + 1;
            }
        }

        $regions[] = (object) ["name" => $region_name, "index" => $next_index];

        if (count($existing) > 0) {
            DB::connection("mysql2")->update("UPDATE `settings` SET `value` = ?, `date_changed` = ? WHERE `keyword` = 'Regions'", [json_encode($regions), date("YmdHis")]);
        } else {
            DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`,`value`,`status`) VALUES ('Regions', ?, '1')", [json_encode($regions)]);
        }

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
        $old_name = null;
        foreach ($regions as $region) {
            if ((string) $region->index === (string) $region_index) {
                $old_name = $region->name;
            } elseif (strtolower($region->name) == strtolower($region_name)) {
                session()->flash("region_error", "A region with that name already exists!");
                return redirect("/Regions");
            }
        }

        if ($old_name === null) {
            session()->flash("region_error", "The region you are trying to edit cannot be found!");
            return redirect("/Regions");
        }

        foreach ($regions as $region) {
            if ((string) $region->index === (string) $region_index) {
                $region->name = $region_name;
            }
        }

        DB::connection("mysql2")->update("UPDATE `settings` SET `value` = ?, `date_changed` = ? WHERE `keyword` = 'Regions'", [json_encode($regions), date("YmdHis")]);

        // keep clients already assigned to this region pointed at the new name
        if (strtolower($old_name) != strtolower($region_name)) {
            DB::connection("mysql2")->update("UPDATE `client_tables` SET `region` = ? WHERE `region` = ?", [$region_name, $old_name]);
        }

        session()->flash("region_success", "Region renamed to \"" . $region_name . "\" successfully!");
        return redirect("/Regions");
    }

    function deleteRegion($region_index){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $regions = $this->getRegionsList();
        $new_regions = [];
        $removed_name = null;
        foreach ($regions as $region) {
            if ((string) $region->index === (string) $region_index) {
                $removed_name = $region->name;
                continue;
            }
            $new_regions[] = $region;
        }

        if ($removed_name === null) {
            session()->flash("region_error", "The region you are trying to delete cannot be found!");
            return redirect("/Regions");
        }

        DB::connection("mysql2")->update("UPDATE `settings` SET `value` = ?, `date_changed` = ? WHERE `keyword` = 'Regions'", [json_encode($new_regions), date("YmdHis")]);

        // unassign clients that were grouped under the deleted region
        DB::connection("mysql2")->update("UPDATE `client_tables` SET `region` = NULL WHERE `region` = ?", [$removed_name]);

        session()->flash("region_success", "Region \"" . $removed_name . "\" deleted successfully!");
        return redirect("/Regions");
    }
}
