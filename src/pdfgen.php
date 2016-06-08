<?php

require_once dirname(__FILE__) . '/../vendor/tecnick.com/tcpdf/tcpdf.php';

Class LabelPDF extends TCPDF {
	private $style = array(
		'position' => '',
		'align' => 'C',
		'stretch' => true,
		'fitwidth' => false,
		'cellfitalign' => '',
		'border' => false,
		'hpadding' => 'auto',
		'vpadding' => 'auto',
		'fgcolor' => array(0,0,0),
		'bgcolor' => false, //array(255,255,255),
		'text' => true,
		'font' => 'helvetica',
		'fontsize' => 8,
		'stretchtext' => 0
	);
	private $top_margin = 16;
	private $left_margin = 8;
	private $left_border = 2;
	private $top_border = 0;
	private $sticker_height = 38;
	private $sticker_width = 63;

	private $sticker_num = 0;
	private $myx = 0;
	private $myy = 0;

	function createSticker($code, $name, $ticket_type) {

		// echo $this->sticker_num;
		if ($this->sticker_num%21 == 0) {
			$this->AddPage();
		}
		$y = floor(($this->sticker_num%21)/3);
		$x = floor(($this->sticker_num%21)%3);
		$printX = $this->left_margin + $x * ($this->sticker_width + $this->left_border);
		$printY = $this->top_margin + $y * ($this->sticker_height + $this->top_border);

		$this->SetXY(3 + $printX, 3 + $printY);
		$this->SetFont('helvetica','', 12);
		$this->Cell($this->sticker_width - 6,
			$this->sticker_height/3,
			"$name",
			0, 0, 'C',
			false, '', 1, true, 'T', 'C');
		$this->SetXY(3 + $printX, 3 + $printY+ $this->sticker_height/3);
		$this->SetFont('','B', 9);
		$this->Cell($this->sticker_width - 6,
			$this->sticker_height/3 - 9,
			"$ticket_type",
			0, 1, 'C',
			false, '', 1, true, 'T', 'C');
		// $this->Cell($this->sticker_width - 6,
		// 	$this->sticker_height/3 - 9,
		// 	"$ticket_type",
		// 	0, 'C', false, 1,
		// 	$printX + 3,
		// 	$this->sticker_height/3 + $printY + 3,
		// 	true, 0, false, true,
		// 	$this->sticker_height/3 - 9, 'M', true);
		$this->SetFont('courier');
		$this->style['label'] = '15 06 2014';
		$this->write1DBarcode($code, 'UPCA', 
			$printX + $this->sticker_width/4, 
			$printY + 2*$this->sticker_height/3 - 6,
			$this->sticker_width/2,
			$this->sticker_height/3 + 6,
			0.4, $this->style, 'M');

		$this->sticker_num++;
	}
}

function fiveChar($i) {
	return str_pad($i, 5, 0, STR_PAD_LEFT);
}

function ticket_type($id) {
	switch ($id) {
		case 2:
		case 3:
		case 4:
		case 8:
			return "Queue Jump";
			break;
		
		case 1:
		case 5:
		case 6:
		case 7:
			return "Standard"
		default:
			return "Unknown";
			break;
	}
}

$pdf = new LabelPDF('P', 'mm', 'A4', true, 'UTF-8', false);
$pdf->setPrintHeader(false);
$pdf->SetAutoPageBreak(false);

// for ($i=1; $i<150; $i++) {
	
// }


	// $dbhost = "emmajuneevent.com";
	// $dbuser = "root";
	// $dbpass = "LockDown1";
	// $dbname = "eje2014";
	// $connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname, 3306);
	// $connection->set_charset("utf8");
	// if ($connection->connect_errno > 0) {
	// 	throw new Exception ('Unable to connect to database [' . $connection->connect_error . ']');
	// }

	// if (!$result = $connection->query('SET SESSION group_concat_max_len = 10000;')) {
	// 	echo 'Could not set group_concat_max_len, so guest lists may be truncated. [' . $connection->error . ']';
	// }
	// $sql = 'SELECT ticket_id AS ticket_id, booker_id AS booker_id, guest_name, ticket_type_id
	// 	FROM cl_guestlist
	// 	WHERE ((status_id =1) OR (status_id =2))
	// 		-- AND ((ticket_id=116) OR (ticket_id=117))
	// 	ORDER BY booker_id ASC , ticket_id ASC';
	
	// if (!$result = $connection->query($sql)) {
	// 	throw new Exception ('There was an error running query[' . $connection->error . ']');
	// }
	$data = array(
		"Christopher Little" => "President",
		"Sakshi Rathi" => "President",
		"Lydia Doster" => "Treasurer & Staffing",
		"Ada Lo" => "Food",
		"Emma Meads" => "Food",
		"Miles Fan" => "Security",
		"Garima Singhal" => "Logistics",
		"Matt Hay" => "Drinks",
		"Sophie Buck" => "Music",
		"Molly Llewellyn-Smith" => "Music",
		"Daisy Savage" => "Entertainment",
		"Fiona Hetherington" => "Entertainment",
		"Hannah Philp" => "Décor",
		"Cathy Smith" => "Décor",
		"Ezra Neil" => "Publicity & Design",
		"Christopher Sng" => "Publicity & Design",
		"Arif Khan" => "Standard"
	);

	foreach ($data as $name => $role) {
		$pdf->createSticker('0150620140', $name, $role);
	}
	// while ($row = $result->fetch_array()) {
	// 	echo $row['ticket_id'] . "\n";
	// 	$pdf->createSticker(fiveChar($row['booker_id']).fiveChar($row['ticket_id']), $row['guest_name'], ticket_type($row['ticket_type_id']));
	// }


//Close and output PDF document
// $pdf->Output('test.pdf', 'F');
$pdf->Output(__DIR__ . '/test.pdf', 'F');

?>