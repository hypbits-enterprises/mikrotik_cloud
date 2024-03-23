<?php

namespace App\Http\Controllers;

use App\Classes\reports\PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


date_default_timezone_set('Africa/Nairobi');
class Expenses extends Controller
{
    // check json structure
    function isJson_report($string) {
        return ((is_string($string) &&
                (is_object(json_decode($string)) ||
                is_array(json_decode($string))))) ? true : false;
    }
    //manages expenses
    function getExpenses(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return redirect("/Dashboard");
        $expenses = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' ORDER BY `id` DESC");
        $first_year = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' ORDER BY `id` ASC");
        $year = count($first_year) > 0 ? date("Y",strtotime($first_year[0]->date_recorded)) : date("Y");
        $exp_category = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'expenses'");
        $exp_cat_data = count($exp_category) > 0 ? ($this->isJson_report($exp_category[0]->value) ? json_decode($exp_category[0]->value) : []) : [];
        // proceed with the data
        return view("expenses",["expenses" => $expenses,"exp_category" => $exp_cat_data, "year" => $year]);
    }

    function viewExpense($expense_index){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `id` = ? ",[$expense_index]);
        if (count($select) > 0) {
            $exp_category = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'expenses'");
            $exp_cat_data = count($exp_category) > 0 ? ($this->isJson_report($exp_category[0]->value) ? json_decode($exp_category[0]->value) : []) : [];
            return view("expensesInfo",["expense_data" => $select[0],"exp_category" => $exp_cat_data]);
        }else {
            session()->flash("expense_error","The expense you are trying to view cannot be found!");
            return redirect("/Expenses");
        }
    }

    function updateExpense(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $expense_id = $req->input("expense_id");
        $expense_name = $req->input("expense_name");
        $expense_category = $req->input("expense_category");
        $expense_date = date("Ymd",strtotime($req->input("expense_date"))).date("His");
        $expense_quantity = $req->input("expense_quantity");
        $expense_unit_price = $req->input("expense_unit_price");
        $expense_total_price = $req->input("expense_total_price");
        $expense_unit = $req->input("expense_unit");
        $expense_description = $req->input("expense_description");

        // update the database
        $now = date("YmdHis");
        $update = DB::connection("mysql2")->update("UPDATE `Expenses` SET `name` = ?, `category` = ?, `unit_of_measure` = ?, `unit_price` = ?, `unit_amount` = ?, `total_price` = ?, `date_recorded` = ?, `date_changed` = ?, `description` = ? WHERE `id` = ?",[$expense_name,$expense_category,$expense_unit,$expense_unit_price,$expense_quantity,$expense_total_price,$expense_date,$now,$expense_description,$expense_id]);
        session()->flash("expense_success","Expense record \"".$expense_name."\" updated successfully!");
        return redirect("/Expense/View/".$expense_id."");
    }

    function generateReports(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $expense_date_option = $req->input("expense_date_option");
        $single_date = $req->input("single_date");
        $from_date = $req->input("from_date");
        $to_date = $req->input("to_date");
        $expense_categories = ucwords(strtolower($req->input("expense_categories")));

        // generate reports
        $expense_data = [];
        $title = "No data to display!";
        if ($expense_categories == "All") {
            if ($expense_date_option == "all") {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' ORDER BY `id` DESC");
                $expense_data = $select;
                $title = "Expense Data List";
            }elseif ($expense_date_option == "select date") {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0'  AND `date_recorded` LIKE '".date("Ymd",strtotime($single_date))."%' ORDER BY `id` DESC");
                $expense_data = $select;
                $title = "All Expense recorded on ".date("D dS M Y",strtotime($single_date));
            }elseif ($expense_date_option == "select between date") {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` BETWEEN '".date("Ymd",strtotime($from_date))."000000' AND '".date("Ymd",strtotime($to_date))."235959' ORDER BY `id` DESC");
                $expense_data = $select;
                $title = "All Expense recorded between ".date("D dS M Y",strtotime($from_date))." and ".date("D dS M Y",strtotime($to_date))."";
            }else {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0'  ORDER BY `id` DESC");
                $expense_data = $select;
                $title = "Expense Data List";
            }
        }else {
            if ($expense_date_option == "all") {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `category` = ? ORDER BY `id` DESC",[$expense_categories]);
                $expense_data = $select;
                $title = "Showing all Expense recorded under \"".$expense_categories."\" Category.";
            }elseif ($expense_date_option == "select date") {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `category` = ? AND `date_recorded` LIKE '".date("Ymd",strtotime($single_date))."%' ORDER BY `id` DESC",[$expense_categories]);
                $expense_data = $select;
                $title = "Showing all Expense recorded on ".date("D dS M Y",strtotime($single_date))." under \"".$expense_categories."\" Category.";
            }elseif ($expense_date_option == "select between date") {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `category` = ? AND `date_recorded` BETWEEN '".date("Ymd",strtotime($from_date))."000000' AND '".date("Ymd",strtotime($to_date))."235959' ORDER BY `id` DESC",[$expense_categories]);
                $expense_data = $select;
                $title = "All Expense recorded between ".date("D dS M Y",strtotime($from_date))." and ".date("D dS M Y",strtotime($to_date)).""." under \"".$expense_categories."\" Category.";
            }else {
                $select = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0'  AND `category` = ? ORDER BY `id` DESC",[$expense_categories]);
                $expense_data = $select;
                $title = "All Expense recorded under \"".$expense_categories."\" Category.";
            }
        }
        // return $expense_data;
        // make the data an array so that it can be passed while making table
        $table_data = [];
        $total_expenses = 0;
        for ($index=0; $index < count($expense_data); $index++) { 
            $array_data = [
                $expense_data[$index]->name,
                $expense_data[$index]->category,
                $expense_data[$index]->unit_of_measure,
                $expense_data[$index]->unit_price,
                $expense_data[$index]->unit_amount,
                $expense_data[$index]->total_price,
                date("D dS M Y",strtotime($expense_data[$index]->date_recorded))
            ];
            $total_expenses+=$expense_data[$index]->total_price;
            array_push($table_data,$array_data);
        }
        if (count($table_data) > 0) {
            // return $new_client_data;
            $pdf = new PDF("P","mm","A4");
            if (session("organization_logo")) {
                $pdf->setCompayLogo("../../../../../../../../..".public_path(session("organization_logo")));
                $pdf->set_company_name(session("organization")->organization_name);
                $pdf->set_school_contact(session("organization")->organization_main_contact);
            }
            $pdf->set_document_title($title);
            $pdf->AddPage();
            $pdf->SetFont('Times', 'B', 10);
            $pdf->SetMargins(5,5);
            $pdf->Cell(40, 10, "Statistics", 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Times', 'I', 9);
            $pdf->Cell(40, 5, "Expense Records :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, number_format(count($table_data)) . " Record(s)", 0, 1, 'L', false);
            $pdf->Cell(40, 5, "Total Expenses :", 0, 0, 'L', false);
            $pdf->Cell(20, 5, "Kes ".number_format(round($total_expenses,2)), 0, 0, 'L', false);
            $pdf->Ln();
            $pdf->SetFont('Helvetica', 'BU', 9);
            $pdf->Cell(200,8,"Client(s) Table",0,1,"C",false);
            $pdf->SetFont('Helvetica', 'B', 7);
            $width = array(8,40,40,20,30,20,40);
            $header = array('No', 'Expense Name', 'Expense Category', 'Unit Price','Unit Amount','Total Price','Date Recorded');
            $pdf->ExpenseTable($header,$table_data,$width);
            $pdf->Output("I","$title.pdf",false);
        }else{
            echo "<p style='color:red;'>No data to display</p>";
        }
    }

    function expenseStatistics(){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // get the data for weeks months and years
        $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
        $months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        
        // date today
        $date_today = date("D");
        
        // get how many days we are after the week starts
        $date_index = 0;
        for ($index=0; $index < count($days); $index++) { 

            if ($date_today == $days[$index]) {
                break;
            }
            $date_index++;
        }

        // substract today with the date index value to get when the week starts
        $last_week_start = date("YmdHis",strtotime(-$date_index." days"));
        $last_end_week = $this->addDays($last_week_start,6);
        

        // get when the first expense was recorded
        $first_payment = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' ORDER BY `date_recorded` ASC LIMIT 1");
        $first_payment_date = count($first_payment) > 0 ? $first_payment[0]->date_recorded : date("YmdHis");
        // return $first_payment_date;

        // get when the week started when the first payment was made
        $first_pay_day = date("D",strtotime($first_payment_date));
        // return $first_pay_day;

        $date_index = 0;
        for ($i=0; $i < count($days); $i++) { 
            if ($first_pay_day == $days[$i]) {
                break;
            }
            $date_index++;
        }
        // return $date_index;

        // get when the week start date
        $first_pay_week_start = $this->addDays($first_payment_date,-$date_index);
        $day_1 = $first_pay_week_start;
        
        // get the transaction data

        $expense_stats_weekly = [];
        $expense_records_weekly = [];
        $break = false;
        $counter = 0;
        while (true) {
            $exp_stats = [];
            $exp_records = [];
            for ($index=0; $index < 7; $index++) {
                $get_amount_per_day = DB::connection("mysql2")->select("SELECT SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND  `date_recorded` LIKE '".date("Ymd",strtotime($day_1))."%'");
                $daily_expense_records = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".date("Ymd",strtotime($day_1))."%' ORDER BY `date_recorded` DESC");
                $exp_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;
                

                $transaction_data = array("date" => date("D dS M",strtotime($day_1)),"expense_amount" => $exp_amount);
                // echo date("D dS M Y",strtotime($day_1))." Amounts".$exp_amount."<br>";
                array_push($exp_stats,$transaction_data);
                array_push($exp_records,$daily_expense_records);
                
                if (date("Ymd",strtotime($last_end_week)) == date("Ymd",strtotime($day_1))) {
                    $break = true;
                }
                $day_1 = $this->addDays($day_1,1);
            }
            $counter++;
            // echo $counter." Weeks <hr>";
            array_push($expense_stats_weekly,$exp_stats);
            array_push($expense_records_weekly,$exp_records);
            if ($break) {
                break;
            }
        }
        // weekly data is sorted
        // return $expense_stats_weekly;


        // get the transaction data for monthly
         // date today
         $month_today = date("M");
        
         // get how many days we are after the week starts
         $months_index = 0;
         for ($index=0; $index < count($months); $index++) { 
 
             if ($month_today == $months[$index]) {
                 break;
             }
             $months_index++;
         }
        //  return $months_index;

         // substract today with the date index value to get when the week starts
         $last_month_start = date("YmdHis",strtotime(-$months_index." months"));
         $last_end_month = $this->addMonths($last_month_start,11);
        //  return $last_end_month;
 
         // get when the first client made their payment
         $first_payment = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' ORDER BY `date_recorded` ASC LIMIT 1");
         $first_payment_date = count($first_payment) > 0 ? $first_payment[0]->date_recorded : date("YmdHis");
        //  return $first_payment_date;
 
         // get when the week started when the first payment was made
         $first_pay_month = date("M",strtotime($first_payment_date));

         $months_index = 0;
         for ($i=0; $i < count($months); $i++) { 
             if ($first_pay_month == $months[$i]) {
                 break;
             }
             $months_index++;
         }
        //  return $months_index;
 
         // get when the week start date
         $first_pay_month_start = $this->addMonths($first_payment_date,-$months_index);
         $day_1 = $first_pay_month_start;
 
         $expense_stats_monthly = [];
         $expense_records_monthly = [];
         $break = false;
         $counter = 0;
         while (true) {
             $expense_stats = [];
             $expense_records = [];
             for ($index=0; $index < 12; $index++) {
                 $get_amount_per_day = DB::connection("mysql2")->select("SELECT SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".date("Ym",strtotime($day_1))."%'");
                 $daily_expense_records = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".date("Ym",strtotime($day_1))."%' ORDER BY `date_recorded` DESC");
                 $exp_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;
                 

 
                 $transaction_data = array("date" => date("M Y",strtotime($day_1)),"expense_amount" => $exp_amount);
                 // echo date("D dS M Y",strtotime($day_1))." Amounts".$exp_amount."<br>";
                 array_push($expense_stats,$transaction_data);
                 array_push($expense_records,$daily_expense_records);
                 
                 if (date("Ym",strtotime($last_end_month)) == date("Ym",strtotime($day_1))) {
                     $break = true;
                 }
                 $day_1 = $this->addMonths($day_1,1);
             }
             $counter++;
             // echo $counter." Weeks <hr>";
             array_push($expense_stats_monthly,$expense_stats);
             array_push($expense_records_monthly,$expense_records);
             if ($break) {
                 break;
             }
         }
        // return $expense_stats_monthly;

        // get the yearly data
        $first_payment = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' ORDER BY `date_recorded` ASC LIMIT 1");
        $first_payment_year = date("YmdHis",strtotime(count($first_payment) > 0 ? $first_payment[0]->date_recorded : date("YmdHis")));
        // return $first_payment_year;

        $expense_yearly_stats = [];
        $expense_yearly_records = [];

        for ($index=(date("Y",strtotime($first_payment_year))*1); $index <= (date("Y")*1); $index++) {
            $get_amount_per_day = DB::connection("mysql2")->select("SELECT SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".$index."%'");
            $daily_exp_records = DB::connection("mysql2")->select("SELECT * FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".$index."%' ORDER BY `date_recorded` DESC");
            $exp_amount = $get_amount_per_day[0]->Total == null ? 0 : $get_amount_per_day[0]->Total;
            
            $transaction_data = array("date" => $index,"expense_amount" => $exp_amount);
            array_push($expense_yearly_stats,$transaction_data);
            array_push($expense_yearly_records,$daily_exp_records);
        }

        // return $expense_stats_monthly;

        return view("expense-stats",["expense_yearly_records" => $expense_yearly_records,"expense_yearly_stats" => $expense_yearly_stats,"expense_records_monthly" => $expense_records_monthly,"expense_stats_monthly" => $expense_stats_monthly,"expense_stats_weekly" => $expense_stats_weekly,"expense_records_weekly" => $expense_records_weekly]);
    }

    function deleteExpenseRecords($expense_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $date_today = date("YmdHis");
        $delete = DB::connection("mysql2")->update("UPDATE `Expenses` SET `date_changed` = ?, `deleted` = '1' WHERE `id` = ?",[$date_today,$expense_id]);
        session()->flash("expense_success","Expense record deleted successfully!");
        return redirect("/Expenses");
    }

    function deleteExpense($expense_id){
        // change db
        $change_db = new login();
        $change_db->change_db();

        $exp_category = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND `keyword` = 'expenses'");
        if (count($exp_category) > 0) {
            $values = $exp_category[0]->value;
            if ($this->isJson_report($values)) {
                $values = json_decode($values);
                $new_value = [];
                $expense_name = "";
                for ($index=0; $index < count($values); $index++) { 
                    if ($values[$index]->index != $expense_id) {
                        array_push($new_value,$values[$index]);
                    }
                    if ($values[$index]->index == $expense_id) {
                        $expense_name = $values[$index]->name;
                    }
                }
                $new_value = json_encode($new_value);
                $update = DB::connection("mysql2")->update("UPDATE `settings` SET `value` = ?, `date_changed` = ? WHERE `keyword` = 'expenses'",[$new_value,date("YmdHis")]);
                session()->flash("expense_success","Expense category \"".$expense_name."\" deleted successfully!");
                return redirect("/Expenses");
            }
        }
        session()->flash("expense_error","An error occured Un-Expectedly!");
        return redirect("/Expenses");
    }

    function financeStats(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $select_income_statement_period = $req->input("select_income_statement_period");
        $select_report_date = $req->input("select_report_date");
        $select_report_from_date = date("Ymd",strtotime($req->input("select_report_from_date")))."000000";
        $select_report_to_date = date("Ymd",strtotime($req->input("select_report_to_date")))."235959";
        $select_mon_option = $req->input("select_mon_option");
        $select_year_option = $req->input("select_year_option");

        // get total income and expense then get the profit after deducting the expenses
        $net_income = 0;
        $expenses = [];
        $title = "No title set";
        if ($select_income_statement_period == "All") {
            // get income
            $select = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted` = '0'");
            $net_income = count($select) > 0 ? $select[0]->Total : 0;

            // get expense grouped by category
            $expense = DB::connection("mysql2")->select("SELECT `category`, SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' GROUP BY `category`");
            $expenses = count($expense) > 0 ? $expense : [];
            $title = "Full Income Statement For HypBits Enterprises";
        }elseif ($select_income_statement_period == "Daily") {
            $title = "Income Statement For HypBits Enterprises on \"".date("D dS M Y",strtotime($select_report_date))."\"";
            // get income
            $select = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted` = '0' AND `transaction_date` LIKE '".date("Ymd",strtotime($select_report_date))."%'");
            $net_income = count($select) > 0 ? ($select[0]->Total == null ? 0 : $select[0]->Total)  : 0;
            // return $select;

            // get expense grouped by category
            $expense = DB::connection("mysql2")->select("SELECT `category`, SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".date("Ymd",strtotime($select_report_date))."%' GROUP BY `category`");
            $expenses = count($expense) > 0 ? $expense : [];
        }elseif ($select_income_statement_period == "Between Dates") {
            $title = "Income Statement For HypBits Enterprises between \"".date("D dS M Y",strtotime($select_report_from_date))."\" and \"".date("D dS M Y",strtotime($select_report_to_date))."\"";
            // get income
            $select = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted` = '0' AND `transaction_date` BETWEEN ? AND ? ",[$select_report_from_date,$select_report_to_date]);
            $net_income = count($select) > 0 ? ($select[0]->Total == null ? 0 : $select[0]->Total) : 0;

            // get expense grouped by category
            $expense = DB::connection("mysql2")->select("SELECT `category`, SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` BETWEEN ? AND ?  GROUP BY `category`",[$select_report_from_date,$select_report_to_date]);
            $expenses = count($expense) > 0 ? $expense : [];
        }elseif ($select_income_statement_period == "Monthly") {
            $monthly = date("Ym",strtotime($select_year_option."-".$select_mon_option."-01"));
            $title = "Income Statement For HypBits Enterprises on the Month of : ".date("F Y",strtotime($select_year_option."-".$select_mon_option."-01"));
            // return $monthly;
            // get income
            $select = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted` = '0' AND `transaction_date` LIKE '".$monthly."%' ");
            $net_income = count($select) > 0 ? ($select[0]->Total == null ? 0 : $select[0]->Total) : 0;

            // get expense grouped by category
            $expense = DB::connection("mysql2")->select("SELECT `category`, SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".$monthly."%'  GROUP BY `category`");
            $expenses = count($expense) > 0 ? $expense : [];
        }elseif ($select_income_statement_period == "Yearly") {
            $title = "Income Statement For HypBits Enterprises of the year : ".$select_year_option;
            // get income
            $select = DB::connection("mysql2")->select("SELECT SUM(`transacion_amount`) AS 'Total' FROM `transaction_tables` WHERE `deleted` = '0' AND `transaction_date` LIKE '".$select_year_option."%' ");
            $net_income = count($select) > 0 ? ($select[0]->Total == null ? 0 : $select[0]->Total) : 0;

            // get expense grouped by category
            $expense = DB::connection("mysql2")->select("SELECT `category`, SUM(`total_price`) AS 'Total' FROM `Expenses` WHERE `deleted` = '0' AND `date_recorded` LIKE '".$select_year_option."%'  GROUP BY `category`");
            $expenses = count($expense) > 0 ? $expense : [];
        }

        // process the data
        $data = [];
        $pdf = new PDF('P','mm','A4');
        $pdf->set_document_title($title);
        $pdf->AddPage();
        $pdf->SetFont('Times', 'B', 10);
        $pdf->SetMargins(5,5);
        $pdf->Ln();
        $pdf->Cell(60,6,$title,0,1,'L');
        $pdf->SetFont('Times', '', 10);
        $pdf->SetFillColor(195,195,195);
        $pdf->Cell(180,5,"Revenue:",'B',1,"L",true);
        $pdf->Cell(120,5,"Primary Income :",0,0,"L");
        $pdf->Cell(60,5,"Kes ".number_format($net_income),0,1,"L");
        $pdf->Ln(10);
        $pdf->SetDrawColor(195,195,195);
        $pdf->SetDrawColor(0,0,0);
        $pdf->Cell(180,5,"Expenses:",'B',1,"L",true);

        $total = 0;
        if (count($expenses) > 0) {
            for ($index=0; $index < count($expenses); $index++) {
                $pdf->Cell(120,5,$expenses[$index]->category,0,0,"L");
                $pdf->Cell(60,5,"Kes ".number_format($expenses[$index]->Total),0,1,"L");
                $total+=$expenses[$index]->Total;
            }
            $pdf->Cell(180,1,"","BT",1,"L");
            $pdf->Cell(120,5,"Total Expenses",0,0,"L");
            $pdf->Cell(60,5,"Kes ".number_format($total),0,1,"L");
        }else{
            $pdf->Cell(60,5,"No Expenses Recorded",0,0,"L");
        }

        // PROFITS
        $pdf->Ln(10);
        $pdf->Cell(180,5,"Profit",'B',1,"L",true);
        $pdf->Cell(120,5,"Profit: ",0,0,"L");
        $pdf->Cell(60,5,"Kes ".number_format($net_income - $total),0,1,"L");
        $pdf->Output("I","income statement.pdf");
    }

    // 
    function addExpenseCategory(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $expense_category = $req->input("expense_category");
        // get the existing
        $expense_categories = DB::connection("mysql2")->select("SELECT * FROM `settings` WHERE `deleted` = '0' AND  `keyword` = 'expenses'");
        $expense_category_saved = count($expense_categories) > 0 ? $expense_categories[0]->value : "[]";

        // get the expense categories
        $expense_category_saved = json_decode($expense_category_saved);
        if (count($expense_category_saved) > 0) {
            $i_count = 0;
            for ($index=0; $index < count($expense_category_saved); $index++) {
                $ind = $expense_category_saved[$index]->index;
                if ($ind > $i_count) {
                    $i_count = $ind;
                }
            }

            // add the new category
            $i_count = count($expense_category_saved) > 0 ? $i_count+1 : $i_count;
            $exp_category = array("name" => $expense_category, "index" => $i_count);
            array_push($expense_category_saved,$exp_category);

            // encode the data to string and update
            $expense_category_saved = json_encode($expense_category_saved);
            $update = DB::connection("mysql2")->update("UPDATE `settings` SET `value` = ?, `date_changed` = ? WHERE `keyword` = 'expenses'",[$expense_category_saved,date("YmdHis")]);
            
            session()->flash("expense_success","Expense category added successfully!");
            return redirect("/Expenses");
        }else {
            // add the new category
            $i_count = 0;
            $exp_category = array("name" => $expense_category, "index" => $i_count);
            array_push($expense_category_saved,$exp_category);

            // encode the data to string and update
            $expense_category_saved = json_encode($expense_category_saved);
            $insert = DB::connection("mysql2")->insert("INSERT INTO `settings` (`keyword`,`value`,`status`) VALUES ('expenses',?,'1')",[$expense_category_saved]);
            
            session()->flash("expense_success","Expense category added successfully!");
            return redirect("/Expenses");
        }
    }

    // add expense
    function addExpense(Request $req){
        // change db
        $change_db = new login();
        $change_db->change_db();

        // return $req;
        $expense_name = $req->input("expense_name");
        $expense_category = $req->input("expense_category");
        $expense_date = date("Ymd",strtotime($req->input("expense_date"))).date("His");
        $expense_quantity = $req->input("expense_quantity");
        $expense_unit_price = $req->input("expense_unit_price");
        $expense_total_price = $req->input("expense_total_price");
        $expense_unit = $req->input("expense_unit");
        $expense_description = $req->input("expense_description");

        // insert the record in the database
        $insert = DB::connection("mysql2")->insert("INSERT INTO `Expenses` (`name`,`category`,`unit_of_measure`,`unit_price`,`unit_amount`,`total_price`,`date_recorded`,`date_changed`,`description`) VALUES (?,?,?,?,?,?,?,?,?)",[$expense_name,$expense_category,$expense_unit,$expense_unit_price,$expense_quantity,$expense_total_price,$expense_date,$expense_date,$expense_description]);
        // return to the default page of the expenses
        session()->flash("expense_success","Expense added successfully!");
        return redirect("/Expenses");
    }
    function addDays($date,$days){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($days." day"));
        return date_format($date,"YmdHis");
    }

    function addMonths($date,$months){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($months." Month"));
        return date_format($date,"YmdHis");
    }
    function addYear($date,$years){
        $date = date_create($date);
        date_add($date,date_interval_create_from_date_string($years." Year"));
        return date_format($date,"YmdHis");
    }
}
