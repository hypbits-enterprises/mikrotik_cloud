<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use phpDocumentor\Reflection\Element;
use stdClass;

date_default_timezone_set('Africa/Nairobi');
class SharedTables extends Controller
{
    //SHARED TABLES
    function openSharedTables(){
        // read the shared table data
        $tables_data = [];

        $jsonFile = public_path('sharedTables/default.json');

        // check if the file exists
        if (!File::exists($jsonFile)){
            // check if the folder exists
            $folder_path = public_path('sharedTables');
            if (!File::isDirectory($folder_path)){
                // create the folder then the file
                File::makeDirectory($folder_path);
            }

            // create the file
            File::put($jsonFile, '[]');
        }
        $jsonData = file_get_contents($jsonFile);

        if ($this->isJson_report($jsonData)) {
            $tables_data = json_decode($jsonData);
        }
        // return $tables_data;
        
        return view("sharedTables",["tables_data" => $tables_data]);
    }


    function isJson_report($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }

    // SaveTable
    function SaveTable(Request $request){
        // return $request;
        $table_carry_data = $request->input("table_carry_data");
        $table_comments = $request->input("table_comments");
        
        if ($this->isJson_report($table_carry_data)) {
            $table_carry_data = json_decode($table_carry_data);
            // return $table_carry_data;

            // set the data
            $table_name = $table_carry_data->table_name;
            $columns = $table_carry_data->columns;

            // user data
            $Userid = session("Userid");

            // get the user data
            $admin_data = DB::select("SELECT * FROM `admin_tables` WHERE `admin_id` = ?",[$Userid]);
            $admin_name = count($admin_data) > 0 ? $admin_data[0]->admin_fullname : $Userid;
            
            // process the data to save in the default file
            $table_Data = new stdClass();
            $table_Data->name = $table_name;
            $table_Data->date_created = date("YmdHis");
            $table_Data->date_modified = date("YmdHis");
            $table_Data->column_count = count($columns);
            $table_Data->columns = $columns;
            $table_Data->creator = $admin_name;
            $table_Data->comment = $table_comments;
            

            // Get the path to the JSON file
            $jsonFile = public_path('sharedTables/default.json');

            // check if the file exists
            if (!File::exists($jsonFile)){
                // check if the folder exists
                $folder_path = public_path('sharedTables');
                if (!File::isDirectory($folder_path)){
                    // create the folder then the file
                    File::makeDirectory($folder_path);
                }

                // create the file
                File::put($jsonFile, '[]');
            }

            $table_id = 0;
            // Read the contents of the file and turn it into json and get the latest id
            $jsonData = file_get_contents($jsonFile);

            if (strlen(trim($jsonData)) == 0) {
                File::put($jsonFile, '[]');
                $jsonData = file_get_contents($jsonFile);
            }
            
            if ($this->isJson_report($jsonData)) {
                $jsonData = json_decode($jsonData);
                for ($index=0; $index < count($jsonData); $index++) { 
                    if ($jsonData[$index]->id >= $table_id) {
                        $table_id = $jsonData[$index]->id;
                    }
                }

                // with the table id lets now add the new table in the list
                $table_id*=1;
                $table_id += 1;
                $table_Data->id = $table_id;

                // add the table
                array_push($jsonData,$table_Data);
            }
            // return $jsonData;

            // turn the json data to string and put it in that file
            $jsonData = json_encode($jsonData);

            File::put($jsonFile, $jsonData);

            // create the table file name with the name
            $full_table_name = $table_name."".$table_id;
            $full_table_name = $this->replacePunctuationWithUnderscore($full_table_name).".json";
            $full_table_name = preg_replace("/\s/i","_",$full_table_name);
            
            // create the table metadata
            $new_table_metadata = new stdClass();
            $new_table_metadata->table_name = $table_name;
            $new_table_metadata->table_id = $table_id;
            $new_table_metadata->date_created = date("YmdHis");
            $new_table_metadata->date_modified = date("YmdHis");
            $new_table_metadata->columns = $columns;
            $new_table_metadata->row_data = [];
            $new_table_metadata->admin_working = [];

            // create the file
            $new_table_file_path = public_path('sharedTables/'.$full_table_name);
            $new_table_metadata = json_encode($new_table_metadata);
            File::put($new_table_file_path, $new_table_metadata);

            // return to the table lists
            session()->flash("shared_table_success","Table \"".$table_name."\" created successfully!");
            return redirect("/SharedTables");
        }
        session()->flash("shared_table_error","An error occured!");
        return redirect("/SharedTables");
    }

    function getTable($table_id,$table_name){
        // get the table data from the json file
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        // return $file_locale;

        // if the file does not exist return to the main file
        if (!File::exists($file_locale)) {
            session()->flash("shared_table_error","The file does not exists. Click from the list below to select the table you want to edit or create a new table!");
            return redirect("/SharedTables");
        }

        $jsonData = file_get_contents($file_locale);

        $jsonData = json_decode($jsonData);

        return view("viewTable",["tables_data"=>$jsonData,"table_id"=>$table_id,"table_name"=>$table_name]);
    }

    function editTable($table_id,$table_name){
        // return $table_id;
        // get the table data from the json file
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        // return $file_locale;

        // if the file does not exist return to the main file
        if (!File::exists($file_locale)) {
            session()->flash("shared_table_error","The file does not exists. Click from the list below to select the table you want to edit or create a new table!");
            return redirect("/SharedTables");
        }

        // get the default file value
        $table_details = json_decode(file_get_contents($file_locale));
        // return $table_details;


        // get the defaults
        $default_locale = public_path("sharedTables/default.json");
        $defaults = json_decode(file_get_contents($default_locale));
        // return $defaults;
        $comments = "";
        for ($index=0; $index < count($defaults); $index++) { 
            if ($defaults[$index]->id == $table_id) {
                $comments = $defaults[$index]->comment;
            }
        }
        return view("editTable",["table_details"=>$table_details,"default"=>$defaults,"comments" => $comments,"table_name" => $table_name,"table_id" => $table_id]);
    }
    function stringInArray($needle, $haystack) {
        foreach ($haystack as $value) {
          if ($needle == $value) {
            return true;
          }
        }
        return false;
      }
    function UpdateTableCreated(Request $request){
        // return $request;
        $table_carry_data = $request->input("table_carry_data");
        $table_comments = $request->input("table_comments");
        $table_id = $request->input("table_id");
        $table_name = $request->input("table_name");


        // get the data of the table
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        $default_locale = public_path("sharedTables/default.json");
        // if the file does not exist return to the main file
        if (!File::exists($file_locale) || !File::exists($default_locale)) {
            session()->flash("shared_table_error","The file does not exists. Click from the list below to select the table you want to edit or create a new table!");
            return redirect("/SharedTables");
        }

        // decode the changes
        $table_carry_data = json_decode($table_carry_data);
        // return $table_carry_data;
        $tableNames = $table_carry_data->table_name;
        $columns = $table_carry_data->columns;
        // get the default file value
        $table_details = json_decode(file_get_contents($file_locale));
        $original_details_columns = $table_details->columns;
        
        // delete the columns
        $columns_to_delete = [];
        for ($index=0; $index < count($original_details_columns); $index++) { 
            array_push($columns_to_delete,$original_details_columns[$index]->id);
        }
        $new_columns = [];
        for ($index=0; $index < count($columns); $index++) { 
            array_push($new_columns,$columns[$index]->id);
        }
        // return $columns_to_delete;

        $delete_this = [];
        for ($index=0; $index < count($columns_to_delete); $index++) {
            if (!$this->stringInArray($columns_to_delete[$index], $new_columns)) {
                array_push($delete_this,$columns_to_delete[$index]);
            }
        }

        $columns_to_add = [];
        for ($index=0; $index < count($new_columns); $index++) {
            if (!$this->stringInArray($new_columns[$index], $columns_to_delete)) {
                array_push($columns_to_add,$new_columns[$index]);
            }
        }
        // return $table_details;

        // go through the row data and delete the column data
        $new_row_data = [];
        for ($index=0; $index < count($table_details->row_data); $index++) { 
            $element = $table_details->row_data[$index];
            $row_data = [];
            $row_ids = 0;
            for ($ind=0; $ind < count($element); $ind++) {
                if ($ind+1 == count($element)) {
                    $row_ids = $element[$ind];
                    continue;
                }
                // return $element[$ind]->col_id;
                if ($this->stringInArray($element[$ind]->col_id,$delete_this)) {
                    continue;
                }
                array_push($row_data,$element[$ind]);
            }
            // add rows that are new
            for ($ind=0; $ind < count($columns_to_add); $ind++) { 
                $new_col = new stdClass();
                $new_col->col_id = $columns_to_add[$ind];
                $new_col->col_value = "Null";
                array_push($row_data,$new_col);
            }
            // add row id
            array_push($row_data,$row_ids);

            // save row to the row list
            array_push($new_row_data,$row_data);
        }
        // update the data
        $table_details->row_data = $new_row_data;
        $table_details->table_name = $tableNames;
        $table_details->columns = $columns;
        $table_details->date_modified = date("YmdHis");

        // return $table_details;
        
        $table_details = json_encode($table_details);
        File::put($file_locale,$table_details);

        // update the default file

        // get the data first
        $default_data = json_decode(file_get_contents($default_locale));

        // look for the table with the id
        for ($index=0; $index < count($default_data); $index++) { 
            if ($default_data[$index]->id == $table_id) {
                $default_data[$index]->name = $table_carry_data->table_name;
                $default_data[$index]->comment = $table_comments;
                $default_data[$index]->date_modified = date("YmdHis");
                $default_data[$index]->columns = $columns;
                $default_data[$index]->column_count = count($columns);
            }
        }
        // return $default_data;

        $default_data = json_encode($default_data);
        File::put($default_locale,$default_data);

        // rename the file
        // table_carry_data
        if (File::exists($file_locale)) {
            File::move($file_locale, public_path("sharedTables/".$this->replacePunctuationWithUnderscore($table_carry_data->table_name).$table_id.".json"));
        } else {
        }

        // return "/SharedTables/Edit/".$table_id."/Name/".$this->replacePunctuationWithUnderscore($table_carry_data->table_name).$table_id.".json";
        return redirect("/SharedTables/Edit/".$table_id."/Name/".$this->replacePunctuationWithUnderscore($table_carry_data->table_name));
    }
    function addRecords($table_id,$table_name){
        // return $table_name;
        $link_table_name = $table_name;

        // get the default configuration of that table so we can take values
        $default_locale = public_path("sharedTables/default.json");
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        if (!File::exists($default_locale) && !File::exists($file_locale)) {
            session()->flash("shared_table_error","The table data cannot be found!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
        }

        // read the file data to get the table configuration and return it to the page
        $jsonData = json_decode(file_get_contents($default_locale));
        
        $table_columns = [];
        $table_name = "Null Table";
        // return $jsonData;

        for ($index=0; $index < count($jsonData); $index++) {
            if ($jsonData[$index]->id == $table_id) {
                $table_columns = $jsonData[$index]->columns;
                $table_name = $jsonData[$index]->name;
                break;
            }
        }

        // check if the json file has elements
        if (empty($table_columns)) {
            // find the file if present

            if (!File::exists($file_locale)) {
                session()->flash("shared_table_error","The table data cannot be found!");
                return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
            }
            // read the file data
            $jsonData = json_decode(file_get_contents($file_locale));

            // return $jsonData;
            $table_name = $jsonData->table_name;
            $table_columns = $jsonData->columns;
        }
        // return $table_columns;
        return view("addRecordTable",["table_name" => $table_name,"table_columns" => $table_columns,"table_id" => $table_id,"link_table_name" => $link_table_name]);
    }

    function saveRecord(Request $request){
        // return $request->input();
        $table_name = $request->input("table_name");
        $table_id = $request->input("table_id");
        $data_to_display = "";
        $counter = 1;

        $row_data = [];
        foreach ($request->input() as $key => $value) {
            if ($counter < 4) {
                $counter++;
                continue;
            }
            $column_data = new stdClass();
            $column_data->col_id = substr($key,(strlen($key)-1));
            $column_data->col_value = $value;
            array_push($row_data,$column_data);
            // $data_to_display.=json_encode($key)." - ". json_encode($value)." $counter<br>";
            $counter++;
        }
        // return $row_data;

        // try getting the id of the latest entry
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        if (!File::exists($file_locale)) {
            session()->flash("shared_table_error","The table data cannot be found!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
        }

        $jsonData = json_decode(file_get_contents($file_locale));

        // return $jsonData;
        $data_row = $jsonData->row_data;
        $jsonData->date_modified = date("YmdHis");
        $highest_index = 0;
        for ($index=0; $index < count($data_row); $index++) { 
            $element = $data_row[$index];
            if ($element[(count($element)-1)]->row_id > $highest_index ) {
                $highest_index = $element[(count($element)-1)]->row_id;
            }
        }
        // return $counter;
        $highest_index+=1;
        $row_index = new stdClass();
        $row_index->row_id = $highest_index;

        // add it to the column data
        array_push($row_data,$row_index);
        array_push($jsonData->row_data,$row_data);
        // return $jsonData;

        // save the data in the file
        $jsonData = json_encode($jsonData);
        File::put($file_locale,$jsonData);

        session()->flash("shared_table_success","Record added successfully!");
        return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");

    }

    function editRecord($table_id,$table_name,$record_no){
        // get the row data and the rows details
        // return $table_name;
        $link_table_name = $table_name;

        // get the default configuration of that table so we can take values
        $default_locale = public_path("sharedTables/default.json");
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        if (!File::exists($file_locale) && !File::exists($default_locale)) {
            session()->flash("shared_table_error","The table data cannot be found!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
        }

        // read the file data to get the table configuration and return it to the page
        $jsonData = json_decode(file_get_contents($default_locale));
        
        $table_columns = [];
        $table_name = "Null Table";
        // return $jsonData;

        for ($index=0; $index < count($jsonData); $index++) {
            if ($jsonData[$index]->id == $table_id) {
                $table_columns = $jsonData[$index]->columns;
                $table_name = $jsonData[$index]->name;
                break;
            }
        }

        // check if the json file has elements
        if (empty($table_columns)) {
            session()->flash("shared_table_error","The table columns are not defined. Kindly define your columns to proceed!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
        }

        // get the row data
        // read the file data
        $jsonData = json_decode(file_get_contents($file_locale));
        // return $jsonData;
        $json_row = $jsonData->row_data;
        $this_row_data = [];
        for ($index=0; $index < count($json_row); $index++) {
            $element = $json_row[$index];
            $row_id = $element[(count($element) - 1)]->row_id;
            if ($row_id == $record_no) {
                $this_row_data = $element;
            }
        }

        // return $this_row_data;
        $rows_id = $this_row_data[count($this_row_data)-1]->row_id;
        return view("editRecordTable",["table_name" => $table_name,"table_columns" => $table_columns,"table_id" => $table_id,"link_table_name" => $link_table_name,"this_row_data" => $this_row_data,"rows_id" => $rows_id]);
    }

    function UpdateRecords(Request $request){
        // return $request;
        $table_name = $request->input("table_name");
        $table_id = $request->input("table_id");
        $row_id = $request->input("row_id");

        // get the file data and update it
        $counter = 1;
        $row_data = [];
        foreach ($request->input() as $key => $value) {
            if ($counter < 5) {
                $counter++;
                continue;
            }
            $column_data = new stdClass();
            $column_data->col_id = substr($key,(strlen($key)-1));
            $column_data->col_value = $value;
            array_push($row_data,$column_data);
            // $data_to_display.=json_encode($key)." - ". json_encode($value)." $counter<br>";
            $counter++;
        }
        $row_index = new stdClass();
        $row_index->row_id = $row_id;
        array_push($row_data,$row_index);
        
        // get the file data and update it
        // get the default configuration of that table so we can take values
        $default_locale = public_path("sharedTables/default.json");
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        if (!File::exists($file_locale) && !File::exists($default_locale)) {
            session()->flash("shared_table_error","The table data cannot be found!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
        }

        // get the file data
        $jsonData = json_decode(file_get_contents($file_locale));

        // loop through the array and update the file
        $jsonData->date_modified = date("YmdHis");
        for ($index=0; $index < count($jsonData->row_data); $index++) {
            $element = $jsonData->row_data[$index];
            if ($element[count($element)-1]->row_id == $row_id) {
                $jsonData->row_data[$index] = $row_data;
            }
        }
        // return $jsonData;
        // update the local file
        File::put($file_locale,json_encode($jsonData));

        // update the table update status
        $jsonData = json_decode(file_get_contents($default_locale));
        for ($index=0; $index < count($jsonData); $index++) { 
            if ($jsonData[$index]->id == $table_id) {
                $jsonData[$index]->date_modified = date("YmdHis");
            }
        }

        // update the default file
        File::put($default_locale,json_encode($jsonData));
        session()->flash("shared_table_success","Row has been successfully updates");
        return redirect("/SharedTables/Edit/".$table_id."/Name/".$table_name."/Record/".$row_id);
    }

    function deleteTable($table_id,$table_name){

        // get the data
        $default_locale = public_path("sharedTables/default.json");
        $file_locale = public_path("sharedTables/".$table_name.$table_id.".json");
        if (!File::exists($file_locale) && !File::exists($default_locale)) {
            session()->flash("shared_table_error","The table data cannot be found!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$table_name."");
        }

        $new_table_data = [];

        // remove if from the default location
        $table_data = json_decode(file_get_contents($default_locale));
        for ($index=0; $index < count($table_data); $index++) { 
            if ($table_data[$index]->id == $table_id) {
                continue;
            }
            array_push($new_table_data,$table_data[$index]);
        }
        // return $new_table_data;

        // save the file
        File::put($default_locale,json_encode($new_table_data));

        // delete the file physically
        if (File::exists($file_locale)) {
            
            // delete file
            File::delete($file_locale);
            
            // store a falsh message
            session()->flash("shared_table_success","Table has been delete successfully!");
            return redirect("/SharedTables");
        }

        session()->flash("shared_table_error","An error has occured!");
        return redirect("/SharedTables");
    }

    function deleteRecord($table_id,$link_table_name,$rows_id){
        // get the data
        $default_locale = public_path("sharedTables/default.json");
        $file_locale = public_path("sharedTables/".$link_table_name.$table_id.".json");
        if (!File::exists($file_locale) && !File::exists($default_locale)) {
            session()->flash("shared_table_error","The record data cannot be found!");
            return redirect("/SharedTables/View/".$table_id."/Name/".$link_table_name."");
        }

        // delete the record and update the table
        $jsonData = json_decode(file_get_contents($file_locale));
        // return $jsonData;
        $row_data = $jsonData->row_data;
        $new_row_data = [];
        for ($index=0; $index < count($row_data); $index++) { 
            if ($row_data[$index][count($row_data[$index])-1]->row_id == $rows_id) {
                continue;
            }
            array_push($new_row_data,$row_data[$index]);
        }
        $jsonData->row_data = $new_row_data;
        
        // after deleting change the time and update the file
        $jsonData->date_modified = date("YmdHis");
        File::put($file_locale,json_encode($jsonData));

        // change the default file
        $jsonData = json_decode(file_get_contents($default_locale));
        // return $jsonData;
        for ($index=0; $index < count($jsonData); $index++) { 
            if ($jsonData[$index]->id == $table_id) {
                $jsonData[$index]->date_modified = date("YmdHis");
            }
        }

        // update the file
        File::put($default_locale,json_encode($jsonData));

        session()->flash("shared_table_success","The record has been deleted successfully!");
        return redirect("/SharedTables/View/".$table_id."/Name/".$link_table_name."");
    }

    function replacePunctuationWithUnderscore($string) {
        $pattern = '/[^\w\s]/';
        $replacement = '_';
        return preg_replace("/\s/i","_",preg_replace($pattern, $replacement, $string));
    }
}
