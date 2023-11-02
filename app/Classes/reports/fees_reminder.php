<?php
class PDF2 extends FPDF
{
    public $B = 0;
    public $school_logo = "../../.." . "/sims/assets/img/ladybird.png";
    public $school_name = "LADYBIRD PRIMARY SCHOOL";
    public $school_po = "552";
    public $school_BOX_CODE = "50400";
    public $school_contact = "0743551250";
    public $school_document_title = "Students List";
    public $school_header_position = 300;

    // set school_logo
    function setSchoolLogo($logo)
    {
        $this->school_logo = $logo;
    }
    // set school_name
    function set_school_name($sch_name)
    {
        $this->school_name = $sch_name;
    }
    // set school_po
    function set_school_po($sch_po)
    {
        $this->school_po = $sch_po;
    }
    // set school_box_code
    function set_school_box_code($sch_box_code)
    {
        $this->school_BOX_CODE = $sch_box_code;
    }
    // set school_box_code
    function set_school_contact($sch_contacts)
    {
        $this->school_contact = $sch_contacts;
    }
    // set school_box_code
    function set_document_title($title)
    {
        $this->school_document_title = $title;
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
        // $this->Image(dirname(__FILE__) . $this->school_logo, 6, 6, 20);
        // // Arial  15
        // $this->SetFont('Arial', '', 15);
        // // Title
        // $this->Cell($this->school_header_position, 5, strtoupper($this->school_name), 0, 0, 'C');
        // $this->Ln();
        // // Arial  15
        // $this->SetFont('Arial', '', 9);
        // $this->Cell($this->school_header_position, 5, "P.O Box : " . $this->school_po . "-" . $this->school_BOX_CODE, 0, 0, 'C');
        // $this->Ln();
        // $this->Cell($this->school_header_position, 5, "Contact us: " . $this->school_contact, 0, 0, 'C');
        // // Line break
        // $this->Ln();
        // $this->SetFont('Arial', 'IU', 11);
        // $this->Ln();
        // $this->Cell($this->school_header_position, 5, "Report Title: " . $this->school_document_title . "", 0, 0, 'C');
        // $this->SetTitle($this->school_document_title);
        // $this->SetFont('', '');
        // $this->SetAuthor($_SESSION['username']);
        // // Line break
        // $this->Ln(20);
    }

    // Page footer
    // function Footer()
    // {
    //     // Position at 1.5 cm from bottom
    //     $this->SetY(-15);
    //     // Arial italic 8
    //     $this->SetFont('Arial', 'I', 8);
    //     // Page number
    //     $this->Cell(0, 5, 'Page ' . $this->PageNo() . '', 0, 0, 'C');
    //     $this->Ln();
    //     $this->SetFont('Arial', 'I', 8);
    //     $this->Cell($this->school_header_position, 7, "This is a computer generated document. If found please return to ". trim($this->school_name) . " or contact " . $this->school_contact . "");
    // }

    function setHeaderPos($pos){
        $this->school_header_position = $pos;
    }

    // Colored table
    function FancyTable($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 9);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'R', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'L', $fill);
            $this->Cell($w[5], 6, $row[5], 1, 0, 'L', $fill);
            $this->Cell($w[6], 6, ($row[6]), 1, 0, 'C', $fill);
            $this->Cell($w[7], 6, ($row[7]), 1, 0, 'R', $fill);
            $this->Cell($w[8], 6, ($row[8]), 1, 0, 'R', $fill);
            $this->Cell($w[9], 6, ($row[9]), 1, 0, 'R', $fill);
            $this->Cell($w[10], 6, ($row[10]), 1, 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    // Colored table
    function financeTable($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 9);
        // Data
        $fill = false;
        foreach ($data as $row) {
            // for ($$index=0; $$index < count($row); $$index++) { 
            //     $this->Cell($w[$index], 6, $row[$index], 1, 0, 'L', $fill);
            // }
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'R', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'R', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'L', $fill);
            $this->Cell($w[5], 6, $row[5], 1, 0, 'L', $fill);
            $this->Cell($w[6], 6, ($row[6]), 1, 0, 'R', $fill);
            $this->Cell($w[7], 6, ($row[7]), 1, 0, 'R', $fill);
            $this->Cell($w[8], 6, ($row[8]), 1, 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    // balance table
    function balancesTable($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 9);
        // Data
        $fill = false;
        foreach ($data as $row) {
            // for ($$index=0; $$index < count($row); $$index++) { 
            //     $this->Cell($w[$index], 6, $row[$index], 1, 0, 'L', $fill);
            // }
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'R', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'C', $fill);
            $this->Cell($w[5], 6, $row[5], 1, 0, 'R', $fill);
            $this->Cell($w[6], 6, ($row[6]), 1, 0, 'R', $fill);
            $this->Cell($w[7], 6, ($row[7]), 1, 0, 'L', $fill);
            $this->Cell($w[8], 6, ($row[8]), 1, 0, 'L', $fill);
            $this->Cell($w[9], 6, ($row[9]), 1, 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    // Attendance fancy table
    function AttendanceTable($header, $data, $present_status)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = array(10, 50, 20, 20, 20, 34);
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 9);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'R', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'R', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'L', $fill);
            $this->Cell($w[5], 6, $present_status, 1, 0, 'L', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    // Colored table
    function StaffData($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 10);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'R', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'R', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'L', $fill);
            $this->Cell($w[5], 6, $row[5], 1, 0, 'L', $fill);
            $this->Cell($w[6], 6, ($row[6]), 1, 0, 'R', $fill);
            $this->Cell($w[7], 6, ($row[7]), 1, 0, 'R', $fill);
            $this->Cell($w[8], 6, ($row[8]), 1, 0, 'R', $fill);
            $this->Cell($w[9], 6, ($row[9]), 1, 0, 'R', $fill);
            $this->Cell($w[10], 6, ($row[10]), 1, 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    // Colored table
    function logTables($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 9);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'L', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'C', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'C', $fill);
            $this->Cell($w[5], 6, $row[5], 1, 0, 'C', $fill);
            $this->Cell($w[6], 6, ($row[6]), 1, 0, 'R', $fill);
            $this->Cell($w[7], 6, ($row[7]), 1, 0, 'R', $fill);
            // $this->Cell($w[8], 6, ($row[8]), 1, 0, 'R', $fill);
            // $this->Cell($w[9], 6, ($row[9]), 1, 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    // Colored table
    function classTrData($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        // $this->SetFont('','B');
        // Header
        $w = $width;
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 1, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Helvetica', '', 10);
        // Data
        $fill = false;
        foreach ($data as $row) {
            $this->Cell($w[0], 6, $row[0], 1, 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 1, 0, 'L', $fill);
            $this->Cell($w[2], 6, ($row[2]), 1, 0, 'C', $fill);
            $this->Cell($w[3], 6, ($row[3]), 1, 0, 'R', $fill);
            $this->Cell($w[4], 6, $row[4], 1, 0, 'R', $fill);
            $this->Cell($w[5], 6, $row[5], 1, 0, 'C', $fill);
            // $this->Cell($w[6], 6, ($row[6]), 1, 0, 'R', $fill);
            // $this->Cell($w[7], 6, ($row[7]), 1, 0, 'R', $fill);
            // $this->Cell($w[8], 6, ($row[8]), 1, 0, 'R', $fill);
            // $this->Cell($w[9], 6, ($row[9]), 1, 0, 'R', $fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    function SetDash($black=null, $white=null)
    {
        if($black!==null)
            $s=sprintf('[%.3F %.3F] 0 d',$black*$this->k,$white*$this->k);
        else
            $s='[] 0 d';
        $this->_out($s);
    }
    function feesStructure($header, $data,$width)
    {
        // Colors, line width and bold font
        $this->SetFillColor(157, 183, 184);
        // $this->SetTextColor(255);
        $this->SetDrawColor(0, 0, 0);
        $this->SetLineWidth(.1);
        $this->SetFont('','B', 9);
        // Header
        $w = $width;
        $this->Cell(5, 8, "", 0, 0, 'C', 0);
        for ($i = 0; $i < count($header); $i++)
            $this->Cell($w[$i], 8, $header[$i], 0, 0, 'C', true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(205, 211, 218);
        $this->SetTextColor(0);
        $this->SetFont('Arial', '', 9);
        // Data
        $fill = false;
        $term1 = 0;
        $term2 = 0;
        $term3 = 0;
        foreach ($data as $row) {
            $this->Cell(5, 6, "", 0, 0, 'C', 0);
            $this->Cell($w[0], 6, $row[0], 'TB', 0, 'L', $fill);
            $this->Cell($w[1], 6, ucwords(strtolower($row[1])), 'TB', 0, 'L', $fill);
            $this->Cell($w[2], 6, "Kes ".number_format($row[2]), 'TB', 0, 'R', $fill);
            $this->Cell($w[3], 6, "Kes ".number_format($row[3]), 'TB', 0, 'R', $fill);
            $this->Cell($w[4], 6, "Kes ".number_format($row[4]), 'TB', 0, 'R', $fill);
            $this->Cell($w[5], 6, $row[5], 'TB', 0, 'C', $fill);
            $this->Ln();
            // $fill = !$fill;
            $term1+=$row[2];
            $term2+=$row[3];
            $term3+=$row[4];
        }
        $this->SetFont('Helvetica', 'B', 9);
        $this->Cell(5, 6, "", 0, 0, 'C', 0);
        $this->Cell($w[0], 6, "", "TB", 0, 'L', false);
        $this->Cell($w[1], 6, "Total", "TB", 0, 'L', false);
        $this->Cell($w[2], 6, "Kes ".number_format($term1), "TB", 0, 'R', false);
        $this->Cell($w[3], 6, "Kes ".number_format($term2), "TB", 0, 'R', false);
        $this->Cell($w[4], 6, "Kes ".number_format($term3), "TB", 0, 'R', false);
        $this->Cell($w[5], 6, "", "TB", 0, 'C', false);
        $this->Ln();
        // Closing line
        // $this->Cell(array_sum($w), 0, '', 'T');
    }
}
?>