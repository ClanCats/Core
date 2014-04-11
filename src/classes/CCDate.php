<?php namespace CC\Core;
/**
 * Clan Cats date functions
 *
 * @package 		ClanCats-Framework
 * @author     		Mario Döring <mariodoering@me.com>
 * @version 		0.5
 * @copyright 		2010 - 2013 ClanCats GmbH 
 *
 */
class CCDate 
{
	
	const Minute 	= 60;
	const Hour		= 3600;
	const Day		= 86400;
	const Week		= 604800;
	const Month		= 2628000;
	const Year		= 31536000;
	
	/**
	 * format the date
	 */
	public static function format( $time, $format = 'j F Y \a\t G:i' ) {
		return date( $format, $time );  
	}
	
	/**
	 * format a date without time
	 */
	public static function format_date( $date, $format = 'j F Y' ) {
		return date( $format, strtotime( $date ) ); 
	}
	
	/**
	 * returns a timestamp before some seconds
	 */
	public static function before( $sec ) {
		return time() - $sec;
	}
	
	/**
	 * to string relative string
	 *
	 * @param int	$ts
	 * @return string
	 */
	public static function relative($ts)
	{
		if(!ctype_digit($ts))
			$ts = strtotime($ts);
	
		$diff = time() - $ts;
		if($diff == 0)
			return __('core::common.date.now');
		elseif($diff > 0)
		{
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 60) return __('core::common.date.just_now');
				if($diff < 120) return __('core::common.date.minute_ago');
				if($diff < 3600) return __('core::common.date.minutes_ago', array( 'num' => floor($diff / 60) ) );
				if($diff < 7200) return __('core::common.date.hour_ago');
				if($diff < 86400) return __('core::common.date.hours_ago', array( 'num' => floor($diff / 3600) ) );
			}
			if($day_diff == 1) return __('core::common.date.yesterday');
			if($day_diff < 7) return __('core::common.date.days_ago', array( 'num' => $day_diff ) );
			if($day_diff < 31) return __('core::common.date.weeks_ago', array( 'num' => ceil($day_diff / 7) ) );
			if($day_diff < 60) return __('core::common.date.last_month');
			return date('F Y', $ts);
		}
		else
		{
			$diff = abs($diff);
			$day_diff = floor($diff / 86400);
			if($day_diff == 0)
			{
				if($diff < 120) return __('core::common.date.in_minute');
				if($diff < 3600) return __('core::common.date.in_minutes', array( 'num' => floor($diff / 60) ) );
				if($diff < 7200) return  __('core::common.date.in_hour');
				if($diff < 86400) return __('core::common.date.in_hours', array( 'num' => floor($diff / 3600) ) );
			}
			if($day_diff == 1) return __('core::common.date.tomorrow');
			if($day_diff < 4) return date('l', $ts);
			if($day_diff < 7 + (7 - date('w'))) return 'next week';
			if(ceil($day_diff / 7) < 4) return 'in ' . ceil($day_diff / 7) . ' weeks';
			if(date('n', $ts) == date('n') + 1) return 'next month';
			return date('F Y', $ts);
		}
	}
}