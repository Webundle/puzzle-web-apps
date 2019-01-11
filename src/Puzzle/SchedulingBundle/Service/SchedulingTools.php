<?php
namespace Puzzle\SchedulingBundle\Service;

/**
 * 
 * @author qwincy<qwincypercy@fermentuse.com>
 *
 */
class SchedulingTools
{
	/**
	 * Convert Interval
	 *
	 * @param integer $intervale
	 * @param string $unity
	 * @return number
	 */
	public function convertIntervale($intervale, $unity)
	{
		switch ($unity){
		    case "P1M":
		        $intervale = $intervale * 60 * 24 * 30;
		        break;
		    case "P1W":
		        $intervale = $intervale * 60 * 24 * 7;
		        break;
		    case "P1D":
		        $intervale = $intervale * 60 * 24;
		        break;
			case "PT1H":
				$intervale = $intervale * 60;
				break;
			default:
				break;
		}
		
		return $intervale;
	}
}