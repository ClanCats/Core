<?php namespace UI;
/**
 * Uiify Bootstrap Builder class
 *
 * @package 		Uiify
 * @author     	Mario Döring <mario@clancats.com>
 * @version 		0.1
 * @copyright 	2013 ClanCats GmbH
 *
 */
class Builder_Bootstrap implements Builder_Interface
{
	
	/**
	 * Build the UI alerts
	 *
	 * @param array 		$alerts
	 * @return UI\HTML
	 */
	public function build_alert( $alerts )
	{	
		return HTML::tag( 'div', function() use( $alerts ) 
		{
			// loop trough all alert types
			foreach( $alerts as $type => $items ) 
			{
				foreach( $items as $alert ) 
				{
					$alert = implode( "<br>\n", $alert );
					
					// close button
					$close = HTML::tag( 'button', '&times;' )
						->add_class('close')
						->type('button')
						->data( 'dismiss', 'alert' );
					
					// alert div
					echo HTML::tag( 'div', $close.$alert )
						->add_class( 'alert' )
						->add_class( 'fade in' )
						->add_class( 'alert-'.$type );
				}
			}
		})->add_class( 'ui-alert-container' );
	}
}