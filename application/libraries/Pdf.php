<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once APPPATH."third_party/tcpdf/tcpdf.php";
class Pdf extends TCPDF { 
    public function __construct() { 
        parent::__construct(); 
    }

    public function Header()
    {
    	$html = "<h2 style=\"color: #f5f5f5; background-color: #353535;\">" . PDF_TITLE . "</h2>";
    	$image_file = K_PATH_IMAGES . 't2ds-logo.png';
        $this->Image($image_file, 50, 30, 60, '', 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        $this->setTextColorArray(array(53, 53, 53));
        $this->writeHTMLCell(432, '', '', '', PDF_TITLE, 0, 1, 0, true, 'R', true);
        // $this->setFillColorArray(array(53, 53, 53));
        // $this->setTextColorArray(array(245, 245, 245));
        // $this->writeHTMLCell(432, '', '', '', PDF_TITLE, 0, 1, 1, true, 'R', true);
    }
}
