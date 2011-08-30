<?php
/*************************************************************************************************
 * Question2Answer theme mods
 *
 * This file contains some example modifications to a Q2A advanced theme. They are split into
 * sections and are designed to be cherry-picked and a section copied to your own qa-theme.php,
 * rather than all used at once. Some functions may conflict with each other, at least until I
 * decide the best way to publish these code snippets.
 *
 *************************************************************************************************/

class qa_html_theme extends qa_html_theme_base
{

	/* NO-NOFOLLOW
	 * Removes nofollow for links to your own website
	 * You must replace YOURDOMAIN.COM with your actual domain name, e.g. www.question2answer.org
	 *********************************************************************************************/

	function _remove_nofollow( $str )
	{
		$search = '#<a rel="nofollow" href="http://YOURDOMAIN.COM("|/[^"]*")#i';
		$replace = '<a href="http://YOURDOMAIN.COM\1';
		return preg_replace( $search, $replace, $str );
	}

	function q_view_content($q_view)
	{
		if (!empty($q_view['content']))
		{
			$this->output(
				'<DIV CLASS="qa-q-view-content">',
				$this->_remove_nofollow( $q_view['content'] ),
				'</DIV>'
			);
		}
	}

	function a_item_content($a_item)
	{
		$this->output(
			'<DIV CLASS="qa-a-item-content">',
			$this->_remove_nofollow( $a_item['content'] ),
			'</DIV>'
		);
	}

	function c_item_content($c_item)
	{
		$this->output(
			'<SPAN CLASS="qa-c-item-content">',
			$this->_remove_nofollow( $c_item['content'] ),
			'</SPAN>'
		);
	}

}
