<?php
class datamine {
	
	# Purpose: Retrieve values from inside PDF
	# Usage  : pdf_decode(String $file, Array $words_to_search_for);
	# Input  : Contents of PDF, and array or words to retrieve from file
	# Return : Named array of values indexed by passed search array
	function pdf($local_file,$testfor) {
		$fh = fopen($local_file,'r');
		$file_contents = fread($fh,filesize($local_file));

		foreach ($testfor as $inside) {
        	$pattern="/\/".$inside."\s*\<FEFF00([\w\'\`\-\_\s\.\:]*)>/";
        	preg_match($pattern,$file_contents,$matches);
        	$info[$inside] = $this->hex2String($matches[1]);
		}

		foreach ($testfor as $inside) {
        	$pattern="/\/".$inside."\s*\(([\w\'\`\-\_\s\.\:]*)\)/";
        	preg_match($pattern,$file_contents,$matches);
        	$info[$inside] = $matches[1];
		}

		# Shorten Creation date
		$pattern="/\w{1}:{1}(\d{4})/";
     	preg_match($pattern,$info['CreationDate'],$matches);
   		$info['CreationDate'] = $matches[1];
		return $info;
	}	

	# Purpose: Decodes data if encoded
	# Usage  : Called from pdf_decode only
	function hex2String($hex) {
        $s = "";
        for ($i = 0; $i < strlen($hex); $i+=2)
            $s .= chr(hexdec($hex{$i}.($hex{$i+1} ? $hex{$i+1} : 0)));

        return $s;
	}
}
?>
