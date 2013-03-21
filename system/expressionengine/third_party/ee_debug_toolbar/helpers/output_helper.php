<?php

/**
 * Formats $arr for use in Panels with Array data
 * @param array $arr
 * @param string $default
 * @param string $pair_delim
 * @param string $tail_delim
 * @return string
 */
function eedt_output_array($arr, $default = 'nothing_found', $pair_delim = ' =&gt; ', $tail_delim = '<br />')
{
	if(!is_array($arr) || count($arr) == '0')
	{
		return lang($default);
	}
	
	$return = '';
	foreach($arr AS $key => $value)
	{
		if(is_array($value))
		{
			$return .= $key.$pair_delim.print_r($value, TRUE);
		}
		else
		{
			$return .= $key.$pair_delim.$value.$tail_delim;
		}
	}
	
	return $return;
}