<?php
class DateComponent extends Object
{
	var $components = array('Validate');
    public function initialize()
    {
        
    }
    public function startup()
    {
        
    }
    public function beforeRender(Controller $controller)
    {

    }
    public function shutdown()
    {
        
    }
	static function add($date, $DATE_ATOM, $increment, $format = 'Y-m-d')
	{
		/*
			strtotime("10 September 2000"), "\n";
			strtotime("+1 day"), "\n";
			strtotime("+1 week"), "\n";
			strtotime("+1 week 2 days 4 hours 2 seconds"), "\n";
			strtotime("next Thursday"), "\n";
			strtotime("last Monday"), "\n";
		*/
		$result = $date;

		if ($result != "")
		{
			$result = strtotime ("{$increment} {$DATE_ATOM}" , strtotime ($result)) ;
			$result = date ($format, $result);
		}

		return $result;
	}
	static function now()
	{
		$result = date ('Y-m-d');

		return $result;
	}
	static function monthsBetween($startDate, $endDate = "", $allowNegatives = true)
	{
		$result = 0;
		$endDate = empty($endDate) ? self::Now() : $endDate;

		if ($startDate != "")
		{
			$date1 = new DateTime($startDate);
			$date2 = new DateTime($endDate);

			if ($date2 > $date1)
			{
				$result = (int)$date1->diff($date2, true)->m;
			}
		}

		return !$allowNegatives && $result < 0 ? 0 : $result;
	}
	static function daysBetween($startDate, $endDate = "", $allowNegatives = true)
	{
		$result = "";
		$endDate = empty($endDate) ? self::Now() : $endDate;

		if ($startDate != "")
			$result = (strtotime($endDate) - strtotime($startDate)) / (60 * 60 * 24);

		return !$allowNegatives && $result < 0 ? 0 : $result;
	}
	static function between($startDate, $endDate = "")
	{
		$result = false;
		$endDate = empty($endDate) ? self::Now() : $endDate;

		$now = date('Y-m-d H:i:s');
		if (strtotime($now) >= strtotime($startDate) && strtotime($now) <= strtotime($endDate))
			$result = true;

		return $result;
	}
	static function lastDayOfMonth($date)
	{
		if ($date == "")
			$result = date::Now();
		else
			$result = date('t', $date);

		return $result;
	}
	static function fromXML($xmlDate, $format = 'Y-m-d')
	{
		$date = BLANK;
		if ($xmlDate != BLANK)
		{
			$date = str_replace("T"," ", $xmlDate);
			$date = substr($date, 0, 6) . "-" . substr($date, 6);
			$date = substr($date, 0, 4) . "-" . substr($date, 4);
			$date = date($format, strtotime($date));
		}
		else
			$date = date($format);

		return $date;
	}
	function validate($date, $format = 'm/d/Y')
	{
		$result = false;
		$month = null;
		$day = null;
		$year = null;

		if (strpos($date, '-') !== FALSE)
			$delimiter = '-';
		else if (strpos($date, '.') !== FALSE)
			$delimiter = '.';
		else
			$delimiter = '/';

		$pieces = explode($delimiter, $date);
		if (sizeof($pieces) == 3)
		{
			switch ($format)
			{
				case "m/d/Y":
				case "m-d-Y":
				case "m.d.Y":
					$month = $pieces[0];
					$day = $pieces[1];
					$year = $pieces[2];
				break;
				case "Y/m/d":
				case "Y-m-d":
				case "Y.m.d":
					$month = $pieces[2];
					$day = $pieces[1];
					$year = $pieces[0];
				break;
			}
			$result = ($this->Validate->numeric($month) && $this->Validate->numeric($day) && $this->Validate->numeric($year));
			if ($result)
			{
				$result = checkdate($month, $day, $year);
			}
		}
		return $result;
	}
}
?>