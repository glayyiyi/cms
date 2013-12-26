<?php

function WPSNSShare_subString($str, $start, $allow) {
	$l = strlen($str);
	$length = 0;
	$i = 0;
	for(;$i < $l;$i++){
		$c = $str[$i];
		$n = ord($c);
		if(($n >> 7) == 0){			//0xxx xxxx, asci, single
			$length += 0.5;
		}
		else if(($n >> 4) == 15){ 	//1111 xxxx, first in four char
			if(isset($str[$i + 1])){
				$i++;
				if(isset($str[$i + 1])){
					$i++;
					if(isset($str[$i + 1])){
						$i++;
					}
				}
			}
			$length++;
		}
		else if(($n >> 5) == 7){ 	//111x xxxx, first in three char
			if(isset($str[$i + 1])){
				$i++;
				if(isset($str[$i + 1])){
					$i++;
				}
			}
			$length++;
		}
		else if(($n >> 6) == 3){ 	//11xx xxxx, first in two char
			if(isset($str[$i + 1])){
				$i++;
			}
			$length++;
		}
		if($length >= $allow) break;
	}
	$ret = substr($str, 0, $i + 1);
	if($i + 1 < $l) $ret .= '...';
	return $ret;
}

function WPSNSShare_remove_caption($content){
	$pattern = '/\[caption[\s\S]*\[\/caption\]/i';
	return preg_replace($pattern, '', $content);
}
