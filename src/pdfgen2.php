<?php

	require_once dirname(__FILE__) . '/../vendor/tecnick.com/tcpdf/tcpdf.php';

	Class PDF extends TCPDF {
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
		private $top_margin = 13;
		private $left_margin = 8;
		private $left_border = 2;
		private $top_border = 0;
		private $sticker_height = 38;
		private $sticker_width = 63;

		private $sticker_num = 0;
		private $myx = 0;
		private $myy = 0;

		function createSticker($code, $name, $ticket_type, $account_name) {

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

			$this->SetXY(3 + $printX, 3 + $printY + 3.5);
			$this->SetFont('','', 7);
			$this->Cell($this->sticker_width - 6,
				$this->sticker_height/3,
				"Guest of $account_name",
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
			$this->style['label'] = preg_replace('/(\d\d\d\d\d)(\d\d\d\d\d)/','$1  $2',$code);
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
				return "Standard";
			default:
				return "Unknown";
				break;
		}
	}
	$pdf = new PDF('P', 'mm', 'A4', true, 'UTF-8', false);
	$pdf->setPrintHeader(false);
	$pdf->SetAutoPageBreak(false);

	$dbhost = "emmajuneevent.com";
	$dbuser = "root";
	$dbpass = "LockDown1";
	$dbname = "mabel";
	$connection = new mysqli($dbhost, $dbuser, $dbpass, $dbname, 3306);
	$connection->set_charset("utf8");
	if ($connection->connect_errno > 0) {
		throw new Exception ('Unable to connect to database [' . $connection->connect_error . ']');
	}

	if (!$result = $connection->query('SET SESSION group_concat_max_len = 10000;')) {
		echo 'Could not set group_concat_max_len, so guest lists may be truncated. [' . $connection->error . ']';
	}
	$sql = "SELECT ticket.id ticket_id, user.id booker_id, user.name account_name, guest_name, ticket.ticket_type_id, status
		FROM ticket
		JOIN user ON user.id = ticket.user_id
		WHERE status=\"CONFIRMED\" 
		-- AND (
			
		-- 	-- ticket_id=384 OR 
		-- 	-- ticket_id=570

		-- ) 
		ORDER BY ticket.id DESC LIMIT 100";
	
	if (!$result = $connection->query($sql)) {
		throw new Exception ('There was an error running query[' . $connection->error . ']');
	}

	while ($row = $result->fetch_array()) {
		echo $row['ticket_id'] . "\n";
		$pdf->createSticker(fiveChar($row['booker_id']).fiveChar($row['ticket_id']), $row['guest_name'], ticket_type($row['ticket_type_id']), $row['account_name']);
	}

	//Close and output PDF document
	$pdf->Output(dirname(__FILE__) . '/barcodes.pdf', 'F');
?>