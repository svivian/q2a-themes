<?php
/*
	'Clean Base' theme for Question2Answer by Scott Vivian
		A basic theme similar to the Default theme, with cleaner
		colours and a handful of usability improvements

	-----------------------------------------------------------------------

	This program is free software: you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation, either version 3 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
	GNU General Public License for more details.

	More about this license: http://www.gnu.org/licenses/gpl.html
*/

class qa_html_theme extends qa_html_theme_base
{
	private $favourite;

	function doctype()
	{
		// use standards doctype
		$this->output( '<!DOCTYPE html>' );
	}

	function head_title()
	{
		// create unique page titles on paginated sections
		if ( qa_get('start') && isset($this->content['title']) )
			$this->content['title'] = $this->content['title'] . ' (page ' . floor(qa_get('start') / $this->_get_per_page() + 1) . ')';

		parent::head_title();
	}

	// function head_metas()
	// {
	// 	// remove meta description and meta keywords
	// }

	function page_title_error()
	{
		if ( isset($this->content['q_view']['url']) )
		{
			$this->content['title'] = '<a href="' . $this->content['q_view']['url'] . '">' . @$this->content['title'] . '</a>';
			if ( @$this->content['q_view']['raw']['closedbyid'] !== null )
				$this->content['title'] .= ' [closed]';
		}

		// remove favourite star here
		$this->favourite = @$this->content['favorite'];
		unset($this->content['favorite']);

		if ( $this->template != 'question' && isset($this->favourite) ) {
			$this->output('<DIV CLASS="qa-favoriting" '.@$this->favourite['favorite_tags'].'>');
			$this->favorite_inner_html($this->favourite);
			$this->output('</DIV>');
		}

		parent::page_title_error();
	}

	function q_item_stats($question)
	{
		$this->output('<DIV CLASS="qa-q-item-stats">');

		$this->voting($question);
		$this->a_count($question);
		$this->view_count($question);

		$this->output('</DIV>');
	}

	function q_item_main($question)
	{
		$this->output('<DIV CLASS="qa-q-item-main">');

		$this->q_item_title($question);
		$this->post_avatar($question, 'qa-q-item');
		$this->post_meta($question, 'qa-q-item');
		$this->post_tags($question, 'qa-q-item');

		$this->output('</DIV>');
	}

	function q_item_title($q_item)
	{
		// display "closed" message in question list
		$closed = @$q_item['raw']['closedbyid'] !== null;

		$this->output(
			'<DIV CLASS="qa-q-item-title">',
			'<A HREF="'.$q_item['url'].'">'.$q_item['title'].'</A>',
			($closed ? ' [closed] ' : ''),
			'</DIV>'
		);
	}

	function voting($post)
	{
		if (isset($post['vote_view'])) {

			if ( $this->template == 'question' )
				$this->output('<div style="float:left; width:56px">');

			$this->output('<DIV CLASS="qa-voting '.(($post['vote_view']=='updown') ? 'qa-voting-updown' : 'qa-voting-net').'" '.@$post['vote_tags'].' >');
			$this->voting_inner_html($post);
			$this->output('</DIV>');

			if ( $this->template == 'question' )
			{
				// add favourite star back
				if ( $post['raw']['type'] == 'Q' && isset($this->favourite) )
				{
					$this->output('<DIV style="text-align:center" '.@$this->favourite['favorite_tags'].'>');
					$this->favorite_inner_html($this->favourite);
					$this->output('</DIV>');
				}
				$this->view_count($post);
			}

			if ( $this->template == 'question' )
				$this->output('</div>');
		}
	}

	function voting_inner_html($post)
	{
		$this->vote_button_up($post);
		$this->vote_count($post);
		$this->vote_button_down($post);
		$this->vote_clear();
	}

	function vote_button_up($post)
	{
		$this->output('<DIV CLASS="qa-vote-buttons '.(($post['vote_view']=='updown') ? 'qa-vote-buttons-updown' : 'qa-vote-buttons-net').'">');

		switch (@$post['vote_state'])
		{
			case 'voted_down':
			case 'voted_down_disabled':
				break;
			case 'voted_up':
				$this->post_hover_button($post, 'vote_up_tags', '', 'qa-vote-one-button qa-voted-up');
				break;
			case 'voted_up_disabled':
				$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-one-button qa-vote-up');
				break;
			case 'enabled':
			case 'up_only':
				$this->post_hover_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
				break;
			default:
				$this->post_disabled_button($post, 'vote_up_tags', '', 'qa-vote-first-button qa-vote-up');
				break;
		}

		$this->output('</DIV>');
	}

	function vote_button_down($post)
	{
		$this->output('<DIV CLASS="qa-vote-buttons '.(($post['vote_view']=='updown') ? 'qa-vote-buttons-updown' : 'qa-vote-buttons-net').'">');

		switch (@$post['vote_state'])
		{
			case 'voted_up':
			case 'voted_up_disabled':
				break;
			case 'voted_down':
				$this->post_hover_button($post, 'vote_down_tags', '', 'qa-vote-one-button qa-voted-down');
				break;
			case 'voted_down_disabled':
				$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-one-button qa-vote-down');
				break;
			case 'enabled':
				$this->post_hover_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;
			default:
				$this->post_disabled_button($post, 'vote_down_tags', '', 'qa-vote-second-button qa-vote-down');
				break;
		}

		$this->output('</DIV>');
	}

	function vote_count($post)
	{
		$post['netvotes_view']['data'] = str_replace( '+', '', $post['netvotes_view']['data'] );
		parent::vote_count($post);
	}

	function a_count($post)
	{
		// You can also use $post['answers_raw'] to get a raw integer count of answers

		$extraclass = null;
		if ( @$post['answers_raw'] == 0 )
			$extraclass = 'qa-a-count-zero';
		if ( @$post['answer_selected'] )
			$extraclass = 'qa-a-count-selected';

		$this->output_split(@$post['answers'], 'qa-a-count', 'SPAN', 'SPAN', $extraclass);
	}

	function finish() {} // override indentation comment


	function post_meta_who($post, $class)
	{
		// if ( $post['raw']['type'] != 'C' )
			// $this->output('<br/>');

		if ( isset($post['who']) )
		{
			$this->output('<SPAN CLASS="'.$class.'-who">');

			if (strlen(@$post['who']['prefix']))
				$this->output('<SPAN CLASS="'.$class.'-who-pad">'.$post['who']['prefix'].'</SPAN>');

			if (isset($post['who']['data']))
				$this->output('<SPAN CLASS="'.$class.'-who-data">'.$post['who']['data'].'</SPAN>');

			if (isset($post['who']['title']))
				$this->output('<SPAN CLASS="'.$class.'-who-title">'.$post['who']['title'].'</SPAN>');

			// You can also use $post['level'] to get the author's privilege level (as a string)

			if ( isset($post['who']['points']) && $post['raw']['type'] != 'C' )
			{
				$post['who']['points']['prefix']='('.$post['who']['points']['prefix'];
				$post['who']['points']['suffix']=')'; // remove 'points' text

				// show zero for all negative points
				$post['who']['points']['data'] = max($post['who']['points']['data'],0);
				$this->output_split($post['who']['points'], $class.'-who-points');
			}

			if (strlen(@$post['who']['suffix']))
				$this->output('<SPAN CLASS="'.$class.'-who-pad">'.$post['who']['suffix'].'</SPAN>');

			$this->output('</SPAN>');
		}
	}




	private function _get_per_page()
	{
		$arr = array('page_size_qs', 'page_size_tags', 'page_size_users', 'page_size_search');
		$options = qa_get_options($arr);

		switch ( $this->template )
		{
			case 'questions':
				return $options['page_size_qs'];
			case 'tags':
				return $options['page_size_tags'];
			case 'users':
				return $options['page_size_users'];
			case 'search':
				return $options['page_size_search'];
		}

		return 20;
	}

	private function _debug( $var )
	{
		$dump = $var ? print_r($var, true) : 'NULL';
		$this->output( '<pre>'.$dump.'</pre>' );
	}

}
