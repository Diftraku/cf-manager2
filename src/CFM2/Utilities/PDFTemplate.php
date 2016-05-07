<?php
/**
 * Created by IntelliJ IDEA.
 * User: diftraku
 * Date: 05/05/16
 * Time: 00:21
 */

namespace CFM2\Utilities;

use TCPDF;

define ('K_PATH_IMAGES', CF_DIR_IMAGES.'/');

class PDFTemplate extends TCPDF {

    public function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=true) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache, $pdfa);
        $this->SetFont('dejavusanscondensed', '', 12);
        $this->setPrintFooter(true);
        $this->setHeaderMargin(10);
        $this->setFooterMargin(10);
        $this->SetMargins(15, 25, 15);
        $this->setHeaderFont(array('dejavusanscondensed', '', 12));
        $this->setPrintFooter(true);
        $this->SetCreator('CF-Manager2');
        $this->SetAuthor('CF-Manager2');
        $this->SetSubject('CF-Manager2 Ticket');
        //$this->SetHeaderData('logo_print.png', 50, 'Crystal Fair 2014', 'Event Ticket');
    }

    public function Footer() {
        $cur_y = $this->y;
        $this->SetTextColorArray($this->footer_text_color);
        //set style for cell border
        $line_width = (0.85 / $this->k);
        $this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
        //print document barcode
        $barcode = $this->getBarcode();
        if (!empty($barcode)) {
            $this->Ln($line_width);
            $barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
            $style = array(
                'position' => $this->rtl?'R':'L',
                'align' => $this->rtl?'R':'L',
                'stretch' => false,
                'fitwidth' => true,
                'cellfitalign' => '',
                'border' => false,
                'padding' => 0,
                'fgcolor' => array(0,0,0),
                'bgcolor' => false,
                'text' => false
            );
            $this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 1.5) - $line_width), 0.3, $style, '');
        }
        $w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
        if (empty($this->pagegroups)) {
            $pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
        } else {
            $pagenumtxt = $w_page;//.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
        }
        $this->SetY($cur_y);

        $this->SetX($this->original_rMargin);
        $this->Cell(0, 0, date('Y-m-d G:i:s'), 'T', 0, 'L');

        //Print page number
        if ($this->getRTL()) {
            $this->SetX($this->original_rMargin);
            $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
        } else {
            $this->SetX($this->original_lMargin);
            $this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
        }
    }
}