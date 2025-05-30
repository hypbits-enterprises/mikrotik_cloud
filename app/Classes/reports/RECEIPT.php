<?php

namespace App\Classes\reports;

require('fpdf.php');
// require('sector.php');
session_start();
date_default_timezone_set('Africa/Nairobi');

class RECEIPT extends FPDF
{
    protected $B = 0;
    public $company_logo = "/logo2.jpeg";
    protected $company_name = "Hypbits Enterprises";
    public $company_contact = "0720268519 / 0717748569";
    public $company_document_title = "Hypbits Enterprises";
    public $website_name = "billing.hypbits.com";
    public $company_email = "mail@hypbits.com";
    public $company_address = "Mombasa Kenya";
    protected $company_header_position = 200;
    protected $client_data = null;
    protected $payment_data = null;


    // SET CLIENT DATA
    function set_client_data($client_data){
        $this->client_data = $client_data;
    }

    // SET INVOICE DATA
    function set_payment_data($payment_data){
        $this->payment_data = $payment_data;
    }

    // set company_logo
    function setCompayLogo($logo)
    {
        $this->company_logo = $logo == null ? dirname(__FILE__) . $this->company_logo : public_path($logo);
    }
    // set website name
    // set company_logo
    function setWebsiteName($web_name)
    {
        $this->website_name = $web_name;
    }
    // set company_name
    function set_company_name($sch_name)
    {
        $this->company_name = $sch_name;
    }
    // set school_box_code
    function set_company_contact($sch_contacts)
    {
        $this->company_contact = $sch_contacts;
    }
    // set school_box_code
    function set_document_title($title)
    {
        $this->company_document_title = $title;
    }

    function set_company_address($physical_address){
        $this->company_address = $physical_address;
    }

    function set_company_email($email){
        $this->company_email = $email;
    }
    // Load data
    function LoadData($file)
    {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line)
            $data[] = explode(';', trim($line));
        return $data;
    }

    // Page header
    function Header()
    {
        // Logo
        $this->Image($this->company_logo, 15, 40, 30);
        // SET XY
        $this->SetMargins(15,15,15);
        $this->SetXY(15, 75);
        // Arial  15
        $this->SetFont('century_gothic', 'B', 10);
        // Company Title
        $this->Cell($this->company_header_position, 5, strtoupper($this->company_name), 0, 1, 'L');
        $this->SetFont('century_gothic', '', 9);
        $this->Cell($this->company_header_position, 5, "Phone: " . $this->company_contact, 0, 1, 'L');
        $this->Cell($this->company_header_position, 5, "Email: " . $this->company_email, 0, 1, 'L');
        $this->Cell($this->company_header_position, 5, "Address: " . $this->company_address, 0, 1, 'L');
        
        // Client Details
        $this->SetFont('century_gothic', '', 30);
        $this->SetXY(100, 50);
        $this->Cell(100,5,"RECEIPT",0,1,"R");
        $this->SetXY(100, 75);
        $this->SetFont('century_gothic', '', 9);
        $this->Cell(100,5,"Date of Payment : ". ($this->payment_data->transaction_date ? date("dS M Y",strtotime($this->payment_data->transaction_date)) : date("dS M Y")),0,1,"R");
        $this->SetXY(100,85);
        $this->Cell(25,5,"By:",0,0,"R");
        $this->SetFont('century_gothic', 'B', 9);
        $this->Cell(75,5,ucwords(strtolower($this->client_data->client_name ?? "Client Name")),0,1,"R");
        $this->SetFont('century_gothic', '', 9);
        $this->SetX(100);
        $this->Cell(100,5,ucwords(strtolower($this->client_data->client_address ?? "Client Address")),0,1,"R");
        $this->SetX(100);
        $this->Cell(100,5,ucwords(strtolower($this->client_data->clients_contacts ?? "Client Phone")),0,1,"R");

        $this->SetTitle($this->company_document_title);
        $this->SetFont('', '');
        $this->SetAuthor(session('Usernames'));
        $this->Ln(10);
    }

    // Page footer
    function Footer()
    {
        // Position at 1.5 cm from bottom
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('century_gothic', 'I', 8);
        // Page number
        $this->Cell(0, 5, 'Page ' . $this->PageNo() . '', 0, 0, 'C');
        $this->Ln();
        $this->SetFont('century_gothic', 'I', 8);
        $this->Cell(180, 7, "This is a computer generated document. If found please return to " . ucwords(strtolower(trim($this->company_name))) . " or contact " . $this->company_contact . "",0,0,'C');
    }

    function setHeaderPos($pos)
    {
        $this->company_header_position = $pos;
    }

    // Colored table
    function FancyTable($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', $fill);
            $this->Cell($w[1], 5, ucwords(strtolower($row[0])), 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, strtoupper($row[1]), 1, 0, 'L', $fill);
            $this->SetFont('Helvetica', '', 4);
            $this->Cell($w[3], 5, date("dS M Y @ H:i:s",strtotime($row[2])), 1, 0, 'C', $fill);
            $this->Cell($w[4], 5, date("dS M Y @ H:i:s",strtotime($row[3])), 1, 0, 'C', $fill);
            $this->SetFont('Helvetica', '', 6);
            $this->Cell($w[5], 5, "Kes ".number_format($row[8]), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, ($row[5]), 1, 0, 'L', $fill);
            $this->Cell($w[7], 5, ($row[6]), 1, 0, 'L', $fill);
            $this->Cell($w[8], 5, ($row[7]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 5, ucwords(strtolower($row[9])), 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function clientInformation($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            // rgb(255, 199, 199)rgb(170, 223, 170)
            if ($row[10] == "0") {
                $this->SetFillColor(255, 199, 199);
            }else{
                $this->SetFillColor(170, 223, 170);
            }
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[1], 5, ucwords(strtolower($row[0])), 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, strtoupper($row[1]), 1, 0, 'L', $fill);
            $this->SetFont('Helvetica', '', 4);
            $this->Cell($w[3], 5, date("dS M Y @ H:i:s",strtotime($row[2])), 1, 0, 'C', $fill);
            $this->Cell($w[4], 5, date("dS M Y @ H:i:s",strtotime($row[3])), 1, 0, 'C', $fill);
            $this->SetFont('Helvetica', '', 6);
            $this->Cell($w[5], 5, "Kes ".number_format($row[3]), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, ($row[2]), 1, 0, 'L', $fill);
            // fill color for static and pppoe assigned
            if ($row[13] == "static") {//rgb(201, 186, 181)rgb(204, 199, 228)
                $this->SetFillColor(201, 186, 181);
            }else{
                $this->SetFillColor(204, 199, 228);
            }
            $this->Cell($w[7], 5, ($row[13]), 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[8], 5, ($row[5]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 5, ucwords(strtolower($row[8])), 1, 0, 'L', $fill);
            $this->Cell($w[10], 5, ucwords(strtolower($row[9])), 1, 0, 'L', $fill);
            if ($row[11] == "In-Active") {
                $this->Cell($w[11], 5, ucwords(strtolower($row[11])), 1, 0, 'L', $fill);
            }else{
                $this->SetFont('Helvetica', '', 4);
                $this->Cell($w[11], 5, ucwords(strtolower($row[11])), 1, 0, 'L', $fill);
                $this->SetFont('Helvetica', '', 6);
            }
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function clientRouterInformation($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            // rgb(255, 199, 199)rgb(170, 223, 170)
            if ($row[10] == "0") {
                $this->SetFillColor(255, 199, 199);
            }else{
                $this->SetFillColor(170, 223, 170);
            }
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[1], 5, ucwords(strtolower($row[0])), 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, strtoupper($row[1]), 1, 0, 'L', $fill);
            $this->SetFont('Helvetica', '', 4);
            $this->Cell($w[3], 5, date("dS M Y @ H:i:s",strtotime($row[6])), 1, 0, 'C', $fill);
            $this->Cell($w[4], 5, date("dS M Y @ H:i:s",strtotime($row[7])), 1, 0, 'C', $fill);
            $this->SetFont('Helvetica', '', 6);
            $this->Cell($w[5], 5, ($row[3]), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, ($row[2]), 1, 0, 'L', $fill);
            // fill color for static and pppoe assigned
            if ($row[13] == "static") {//rgb(201, 186, 181)rgb(204, 199, 228)
                $this->SetFillColor(201, 186, 181);
            }else{
                $this->SetFillColor(204, 199, 228);
            }
            $this->Cell($w[7], 5, ($row[13]), 1, 0, 'L', true);
            $this->SetFillColor(204, 230, 244);
            $this->Cell($w[8], 5, ($row[5]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 5, ucwords(strtolower($row[8])), 1, 0, 'L', $fill);
            $this->Cell($w[10], 5, ucwords(strtolower($row[9])), 1, 0, 'L', $fill);
            $this->Cell($w[11], 5, ucwords(strtolower($row[11])), 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function transactionReports($header,$data,$width){
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', $fill);
            $this->Cell($w[1], 5, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[3], 5, "Kes ".number_format($row[3]), 1, 0, 'L', $fill);
            $this->Cell($w[4], 5, date("D dS M Y @ h:i:s A",strtotime($row[4])), 1, 0, 'L', $fill);
            $this->Cell($w[5], 5, $row[5], 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, $row[6], 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function smsTable($header,$data,$width){
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $row[0], 1, 0,'J', $fill);
            $this->Cell($w[1], 5, $row[1], 1, 0,'J', $fill);
            $this->Cell($w[2], 5, date("dS M Y @ H:i:s",strtotime($row[2])), 1, 0,'J', $fill);
            $this->Cell($w[3], 5, $row[3], 1, 0,'J', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    
    function ExpenseTable($header, $data, $width)
    {
        // Colors, line width and bold fontrgb(82, 170, 216)
        $this->SetFillColor(82, 170, 216);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restorationrgb(204, 230, 244)
        $this->SetFillColor(204, 230, 244);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 6);
        // Data
        $fill = false;
        $counter = 1;
        foreach ($data as $row) {
            $this->Cell($w[0], 5, $counter, 1, 0, 'L', $fill);
            $this->Cell($w[1], 5, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[2], 5, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[3], 5, "Kes ".number_format(round($row[3],2)), 1, 0, 'L', $fill);
            $this->Cell($w[4], 5, $row[4]." ".($row[2] != null ? $row[2] : ""), 1, 0, 'L', $fill);
            $this->Cell($w[5], 5, "Kes ".number_format(round($row[5],2)), 1, 0, 'L', $fill);
            $this->Cell($w[6], 5, $row[6], 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
            $counter++;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
}

function receiptNo($no){
    if (strlen($no) < 3) {
        if(strlen($no) == 2){
            return "0".$no;
        }else{
            return "00".$no;
        }
    }
    return $no;
}
?>