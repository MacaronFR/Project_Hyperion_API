<?php
/**
 * @var int $id_cart
 * @var array $user
 */

namespace Hyperion\API;

use FPDF;

require("fpdf/fpdf.php");

$picm = new ProductInCartModel();
$pm = new ProductModel();

$products_id = $picm->selectByCart($id_cart);
$products = [];
$tab = [[
	'userName' => $user['name'],
	'firstname' => $user['fname'],
	'address' => $user['addr']['address'],
	'zip' => $user['addr']['zip'],
	'city' => $user['addr']['city']
]];
foreach($products_id as $pid){
	$p = $pm->select($pid['product']);
	$p['spec'] = $pm->selectWithDetail($pid['product'])['spec'];
	$tab[] = ['description' => $p['spec']['brand'] . " " . $p['spec']['model'], 'tva' => 20, 'selling_price' => $p['sell_p']];
}



class InvoicePDF extends FPDF
{
// Header
	function Header() {
		$this->Image(__DIR__ . '/fpdf/HyperionLogo2.png',15,5, 35);
		$this->Ln(20);
		$this->SetFont('Helvetica','',20);
		$this->setFillColor(230,230,230);
		$this->SetX(70);
		$this->Cell(80,10,'AVOIR',0,0,'C',1);
		$this->Ln(18);
	}

	function Footer() {
		$this->SetY(-15);
		$this->SetFont('Helvetica','I',9);
		$this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
	}
}

$pdf = new InvoicePDF('P','mm','A4');
$pdf->AddPage();
$pdf->SetFont('Helvetica','',9);
$pdf->SetTextColor(0);
$pdf->AliasNbPages();

$pdf->SetFont('Helvetica','B',12);
$pdf->setFillColor(230,230,230);
$pdf->Cell(75,6,'Adresse de facturation:',0,0,'L',1);
$pdf->Cell(112,6,'Hyperion S.A.R.L',0,1,'R',0);
$pdf->SetFont('Helvetica','',9);
$pdf->Cell(75,6, utf8_decode($tab[0]['userName']." ".$tab[0]['firstname'] ),0,0,'L',1);
$pdf->Cell(112,6,'242 Rue du Faubourg Saint-Antoine',0,1,'R',0);
$pdf->Cell(75,6, utf8_decode($tab[0]['address']),0,0,'L',1);
$pdf->Cell(112,6,'Paris XII 75012',0,1,'R',0);
$pdf->Cell(75,6, utf8_decode($tab[0]['zip'].", ".utf8_decode($tab[0]['city'])),0,0,'L',1);
$pdf->Cell(112,6,'TVA: FR12637848934987',0,1,'R',0);
$pdf->Ln(10);

$pdf->SetFont('Helvetica','B',9);
$pdf->Cell(40,6, 'Numero de la commande:',0,0,'L',1);
$pdf->SetFont('Helvetica','',9);
$pdf->Cell(75,6, '1',0,1,'',1);

$pdf->SetFont('Helvetica','B',9);
$pdf->Cell(40,6, 'Date de la commande:',0,0,'L',1);
$pdf->SetFont('Helvetica','',9);
$pdf->Cell(75,6, '19.06.2021',0,1,'',1);



function entete_table($position_entete, $pdf) {
	$pdf->SetDrawColor(183);
	$pdf->SetFillColor(221);
	$pdf->SetTextColor(0);
	$pdf->SetY($position_entete);
	$pdf->SetX(10);
	$pdf->Cell(80,8,'Description de l article','LRT',0,'C',1);
	$pdf->SetX(90);
	$pdf->Cell(20,8,'Taux TVA%','LRT',0,'C',1);
	$pdf->SetX(110);
	$pdf->Cell(20,8,'Prix unitaire','LRT',0,'C',1);
	$pdf->SetX(130);
	$pdf->Cell(20,8,'montant TVA','LRT',0,'C',1);
	$pdf->SetX(150);
	$pdf->Cell(20,8,'Prix total','LRT',0,'C',1);
	$pdf->Ln();

	$pdf->SetDrawColor(183);
	$pdf->SetFillColor(221);
	$pdf->SetTextColor(0);
	$pdf->SetY($position_entete + 6);
	$pdf->SetX(10);
	$pdf->SetFont('Helvetica','',6);
	$pdf->Cell(80,4,'','LR',0,'C',1);
	$pdf->SetX(90);
	$pdf->Cell(20,4,'','LR',0,'C',1);
	$pdf->SetX(110);
	$pdf->Cell(20,4,'(hors TVA)','LR',0,'C',1);
	$pdf->SetX(130);
	$pdf->Cell(20,4,'(TVA seul)','LR',0,'C',1);
	$pdf->SetX(150);
	$pdf->Cell(20,4,'(inclus TVA)','LR',0,'C',1);
	$pdf->Ln();
}


$position_entete = 100;
$pdf->SetFont('Helvetica','',9);
$pdf->SetTextColor(0);
entete_table($position_entete, $pdf);

$position_detail = 110;
$htSum = 0;
$tvaSum = 0;
$ttcSum = 0;

for($i=1; $i<count($tab); $i++){
	$pdf->SetY($position_detail);
	$pdf->SetX(10);
	$pdf->MultiCell(80,8,utf8_decode($tab[$i]['description']),1,'C');
	$pdf->SetY($position_detail);
	$pdf->SetX(90);
	$pdf->MultiCell(20,8,utf8_decode($tab[$i]['tva'])."%",1,'C');
	$pdf->SetY($position_detail);
	$pdf->SetX(110);
	$pdf->MultiCell(20,8,utf8_decode(number_format($tab[$i]['selling_price'] * 100 / 120, 2, ",", " "))." ".chr(128),1,'C');
	$pdf->SetY($position_detail);
	$pdf->SetX(130);
	$pdf->MultiCell(20,8,utf8_decode(number_format((($tab[$i]['selling_price'])*($tab[$i]['tva']))/120, 2, ",", " "))." ".chr(128),1,'C');
	$pdf->SetY($position_detail);
	$pdf->SetX(150);
	$pdf->MultiCell(20,8,utf8_decode(number_format($tab[$i]['selling_price'], 2, ",", " "))." ".chr(128),1,'C');
	$position_detail += 8;
	$htSum += $tab[$i]['selling_price'] * 100 / 120;
	$tvaSum += $tab[$i]['selling_price'] * 20 / 120;
	$ttcSum += $tab[$i]['selling_price'];
}
$pdf->SetY($position_detail);
$pdf->SetX(10);
$pdf->Cell(80,8,'','LRT',0,'C',1);
$pdf->SetX(90);
$pdf->Cell(20,8,'TOTAL','LRT',0,'C',1);
$pdf->SetX(110);
$pdf->Cell(20,8,utf8_decode(number_format($htSum, 2, ",", " "))." ".chr(128),'LRT',0,'C',1);
$pdf->SetX(130);
$pdf->Cell(20,8,utf8_decode(number_format($tvaSum, 2, ",", " "))." ".chr(128),'LRT',0,'C',1);
$pdf->SetX(150);
$pdf->Cell(20,8,utf8_decode(number_format($ttcSum, 2, ",", " "))." ".chr(128),'LRT',0,'C',1);
$pdf->Ln();

ob_start();
$pdf->SetFont('Helvetica','',11);
$pdf->SetTextColor(0);
$pdf->Output('facture.pdf','I');
$pdf_output = ob_get_clean();