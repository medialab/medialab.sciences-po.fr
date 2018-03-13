<?php
	/**
	 * output dump of vixed variable. If second arfument is true then do exit of script execution
	 *
	 * @param mixed $mixed
	 * @param bool $stop
	 */
	function pa($mixed, $stop = false)
	{
		$ar = debug_backtrace();
		$key = pathinfo($ar[0]['file']);
		$key = $key['basename'].':'.$ar[0]['line'];
		$GLOBALS['print_r_view'][$key][] = $mixed;
		if($stop > 0)
		{
			$str = '';
			foreach($GLOBALS['print_r_view'] as $line => $values)
			{
				foreach($values as $key => $value)
				{
					$temp_ar = array($line => $value);
					$tag = 'pre';
					if(defined('SITE_WAP_MODE') && SITE_WAP_MODE)
					{
						$tag = 'wml';
					}
					$str .= '<'.$tag.'>'.htmlspecialchars(print_r($temp_ar,1)).'</'.$tag.'>';
				}
			}
			if($stop == 1)
			{
				echo $str;
				exit;
			}
			if($stop == 2)
			{
				return $str;
			}
		}	
	}
