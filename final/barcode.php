<?php

	class Barcode
	{

		public function generate(
			string $text = 'some text', 
			string $filename = 'barcode.jpg', 
			string $mode = 'B'
		):void 
		{

			$allowed_modes = ['A','B','C'];
			$mode = in_array($mode, $allowed_modes) ? $mode : 'B';
			$data = $this->get_data('B');

			$code = '';
			$code .= '0000000000';
			
			//add start code
			$code .= $data['Start Code B']['bin'];
			
			//add text
			$char_pos = 0;
			$checksum = $data['Start Code B']['value'];

			$count = strlen($text);
			for ($i = 0; $i < $count; $i++) { 
				
				$char_pos++;
				$code .= $data[$text[$i]]['bin'] ?? '';
				
				$checksum = $checksum + ($char_pos * ($data[$text[$i]]['value'] ?? 0));
			}

			$checksum = $checksum % 103;
			//add checksum binary
			foreach ($data as $arr)
			{
				
				if($arr['value'] == $checksum){
					$checksum = $arr['bin'];
					break;
				}
			}

			$code .= $checksum;
			$code .= $data['Stop Pattern']['bin'];
			$code .= '0000000000';//silence

			$img_width = 500;
			$img_height = 150;

			$image = imagecreate($img_width, $img_height);
			
			//add bg color
			imagecolorallocate($image, 255, 255, 255);
			
			$white = imagecolorallocate($image, 255, 255, 255);
			$black = imagecolorallocate($image, 0, 0, 0);

			$count = strlen($code);
			$gap_size = $img_width / $count;

			for ($i = 0; $i < $count; $i++) { 
				
				$x1 = $i * $gap_size;
				$x2 = $x1 + $gap_size;

				$color = ($code[$i]) ? $black : $white;
				imagefilledrectangle($image, $x1, 10, $x2, ($img_height - 30), $color);
			}

			//imagestring($image, 5, 10, ($img_height - 20), $text, $black);

			imagejpeg($image, $filename, 90);
			imagedestroy($image);
		}

		private function get_data(string $mode = 'B'): array
		{

			$data['A'] = [
			];

			$data['C'] = [

			];

			$data['B'] = [

				' ' 	=> ['value' => 0,	'bin' => '11011001100'],
				'!' 	=> ['value' => 1,	'bin' => '11001101100'],
				'"' 	=> ['value' => 2,	'bin' => '11001100110'],
				'#' 	=> ['value' => 3,	'bin' => '10010011000'],
				'$' 	=> ['value' => 4,	'bin' => '10010001100'],
				'%' 	=> ['value' => 5,	'bin' => '10001001100'],
				'&' 	=> ['value' => 6,	'bin' => '10011001000'],
			 	'\''	=> ['value'	=> 7,	'bin' => '10011000100'],
			 	'('		=> ['value'	=> 8,	'bin' => '10001100100'],
			 	')'		=> ['value'	=> 9,	'bin' => '11001001000'],
			 	'*'		=> ['value'	=> 10,	'bin' => '11001000100'],
			 	'+'		=> ['value'	=> 11,	'bin' => '11000100100'],
			 	','		=> ['value'	=> 12,	'bin' => '10110011100'],
			 	'-'		=> ['value'	=> 13,	'bin' => '10011011100'],
			 	'.'		=> ['value'	=> 14,	'bin' => '10011001110'],
			 	'/'		=> ['value'	=> 15,	'bin' => '10111001100'],
			 	'0'		=> ['value'	=> 16,	'bin' => '10011101100'],
			 	'1'		=> ['value'	=> 17,	'bin' => '10011100110'],
			 	'2'		=> ['value'	=> 18,	'bin' => '11001110010'],
			 	'3'		=> ['value'	=> 19,	'bin' => '11001011100'],
			 	'4'		=> ['value'	=> 20,	'bin' => '11001001110'],
			 	'5'		=> ['value'	=> 21,	'bin' => '11011100100'],
			 	'6'		=> ['value'	=> 22,	'bin' => '11001110100'],
			 	'7'		=> ['value'	=> 23,	'bin' => '11101101110'],
			 	'8'		=> ['value'	=> 24,	'bin' => '11101001100'],
			 	'9'		=> ['value'	=> 25,	'bin' => '11100101100'],
			 	':'		=> ['value'	=> 26,	'bin' => '11100100110'],
			 	';'		=> ['value'	=> 27,	'bin' => '11101100100'],
			 	'<'		=> ['value'	=> 28,	'bin' => '11100110100'],
			 	'='		=> ['value'	=> 29,	'bin' => '11100110010'],
			 	'>'		=> ['value'	=> 30,	'bin' => '11011011000'],
			 	'?'		=> ['value'	=> 31,	'bin' => '11011000110'],
			 	'@'		=> ['value'	=> 32,	'bin' => '11000110110'],
			 	'A'		=> ['value'	=> 33,	'bin' => '10100011000'],
			 	'B'		=> ['value'	=> 34,	'bin' => '10001011000'],
			 	'C'		=> ['value'	=> 35,	'bin' => '10001000110'],
			 	'D'		=> ['value'	=> 36,	'bin' => '10110001000'],
			 	'E'		=> ['value'	=> 37,	'bin' => '10001101000'],
			 	'F'		=> ['value'	=> 38,	'bin' => '10001100010'],
			 	'G'		=> ['value'	=> 39,	'bin' => '11010001000'],
			 	'H'		=> ['value'	=> 40,	'bin' => '11000101000'],
			 	'I'		=> ['value'	=> 41,	'bin' => '11000100010'],
			 	'J'		=> ['value'	=> 42,	'bin' => '10110111000'],
			 	'K'		=> ['value'	=> 43,	'bin' => '10110001110'],
			 	'L'		=> ['value'	=> 44,	'bin' => '10001101110'],
			 	'M'		=> ['value'	=> 45,	'bin' => '10111011000'],
			 	'N'		=> ['value'	=> 46,	'bin' => '10111000110'],
			 	'O'		=> ['value'	=> 47,	'bin' => '10001110110'],
			 	'P'		=> ['value'	=> 48,	'bin' => '11101110110'],
			 	'Q'		=> ['value'	=> 49,	'bin' => '11010001110'],
			 	'R'		=> ['value'	=> 50,	'bin' => '11000101110'],
			 	'S'		=> ['value'	=> 51,	'bin' => '11011101000'],
			 	'T'		=> ['value'	=> 52,	'bin' => '11011100010'],
			 	'U'		=> ['value'	=> 53,	'bin' => '11011101110'],
			 	'V'		=> ['value'	=> 54,	'bin' => '11101011000'],
			 	'W'		=> ['value'	=> 55,	'bin' => '11101000110'],
			 	'X'		=> ['value'	=> 56,	'bin' => '11100010110'],
			 	'Y'		=> ['value'	=> 57,	'bin' => '11101101000'],
			 	'Z'		=> ['value'	=> 58,	'bin' => '11101100010'],
			 	'['		=> ['value'	=> 59,	'bin' => '11100011010'],
			 	'\\'	=> ['value'	=> 60,	'bin' => '11101111010'],
			 	']'		=> ['value'	=> 61,	'bin' => '11001000010'],
			 	'^'		=> ['value'	=> 62,	'bin' => '11110001010'],
			 	'_'		=> ['value'	=> 63,	'bin' => '10100110000'],
			 	'`'		=> ['value' => 64,	'bin' => '10100001100'],
			 	'a'		=> ['value' => 65,	'bin' => '10010110000'],
			 	'b'		=> ['value' => 66,	'bin' => '10010000110'],
			 	'c'		=> ['value' => 67,	'bin' => '10000101100'],
			 	'd'		=> ['value' => 68,	'bin' => '10000100110'],
			 	'e'		=> ['value' => 69,	'bin' => '10110010000'],
			 	'f'		=> ['value' => 70,	'bin' => '10110000100'],
			 	'g'		=> ['value' => 71,	'bin' => '10011010000'],
				'h'		=> ['value' => 72,	'bin' => '10011000010'],
				'i'		=> ['value' => 73,	'bin' => '10000110100'],
				'j'		=> ['value' => 74,	'bin' => '10000110010'],
				'k'		=> ['value' => 75,	'bin' => '11000010010'],
				'l'		=> ['value' => 76,	'bin' => '11001010000'],
				'm'		=> ['value' => 77,	'bin' => '11110111010'],
				'n'		=> ['value' => 78,	'bin' => '11000010100'],
				'o'		=> ['value' => 79,	'bin' => '10001111010'],
			 	'p'		=> ['value' => 80,	'bin' => '10100111100'],
			 	'q'		=> ['value' => 81,	'bin' => '10010111100'],
			 	'r'		=> ['value' => 82,	'bin' => '10010011110'],
			 	's'		=> ['value' => 83,	'bin' => '10111100100'],
			 	't'		=> ['value' => 84,	'bin' => '10011110100'],
			 	'u'		=> ['value' => 85,	'bin' => '10011110010'],
			 	'v'		=> ['value' => 86,	'bin' => '11110100100'],
			 	'w'		=> ['value' => 87,	'bin' => '11110010100'],
			 	'x'		=> ['value' => 88,	'bin' => '11110010010'],
				'y'		=> ['value' => 89,	'bin' => '11011011110'],
			 	'z'		=> ['value' => 90,	'bin' => '11011110110'],
			 	'{'		=> ['value' => 91, 	'bin' => '11110110110'],
			 	'|'		=> ['value' => 92, 	'bin' => '10101111000'],
			 	'}'		=> ['value' => 93, 	'bin' => '10100011110'],
			 	'~'		=> ['value' => 94, 	'bin' => '10001011110'],
			 	
			 	'DEL' 		=> ['value' => 95,	'bin' => '10111101000'],
			 	'FNC 3'		=> ['value' => 96,	'bin' => '10111100010'],
			 	'FNC 2'		=> ['value' => 97,	'bin' => '11110101000'],
			 	'Shift A'	=> ['value' => 98,	'bin' => '11110100010'],
			 	'Code C'	=> ['value' => 99,	'bin' => '10111011110'],
			 	'FNC 4'		=> ['value' => 100,	'bin' => '10111101110'],
			 	'Code A'	=> ['value' => 101,	'bin' => '11101011110'],
			 	'FNC 1'		=> ['value' => 102,	'bin' => '11110101110'],

				'Start Code A'	=> ['value' => 103, 'bin' => '11010000100'],
				'Start Code B'	=> ['value' => 104, 'bin' => '11010010000'],
				'Start Code C'	=> ['value' => 105, 'bin' => '11010011100'],
				'Stop'			=> ['value' => 106, 'bin' => '11000111010'],

				'Reverse Stop'	=> ['value' => 0, 'bin' => '11010111000'], 
				'Stop Pattern'	=> ['value' => 0, 'bin' => '1100011101011'],
				'Silent Zone'	=> ['value' => 0, 'bin' => '0000000000'],

			];

			return $data[$mode] ?? $data['B'];

		}
	
	}
