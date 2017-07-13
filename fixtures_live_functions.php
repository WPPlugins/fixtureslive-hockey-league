<?php
/**
 * Helper functions for Fixtures Live Content
 *
 * @package             Fixtures Live
 * @category            Cup
 * @author              Fixtures Live
 * @copyright           Copyright © 2013 Fixtures Live.
 */

/**
 * FUNCTION get_current_post_type()
 * gets the current post type in the WordPress Admin
 * @return string / null
 */
if(!function_exists('get_current_post_type')) {
	function get_current_post_type() {
	  global $post, $typenow, $current_screen;
		
	  //we have a post so we can just get the post type from that
	  if ( $post && $post->post_type )
	    return $post->post_type;
	    
	  //check the global $typenow - set in admin.php
	  elseif( $typenow )
	    return $typenow;
	    
	  //check the global $current_screen object - set in sceen.php
	  elseif( $current_screen && $current_screen->post_type )
	    return $current_screen->post_type;
	  
	  //lastly check the post_type querystring
	  elseif( isset( $_REQUEST['post_type'] ) )
	    return sanitize_key( $_REQUEST['post_type'] );

	  elseif (get_post_type( @$_GET['post'] )) {
	  	 return get_post_type( @$_GET['post'] );
	  } 
		
	  //we do not know the post type!
	  return null;
	}
}

/**
* Function - get_page_type
* More of a helper for the templating than anything
* @param $pst
* @return int
*/
function get_page_type($post = null) {
	global $fixtures_live_options;
	if(!$post) { global $post; }

	switch(get_post_type()) {
		case 'league' :
			return 'division';
		break;
		case 'cup' :
			return 'cup';
		break;
	}

	if(@$_GET['external']) {
		return 'external-comp';
	}

	if(@$_GET['venue']) {
		return 'venue';
	}

	if($post) {
		switch($post->ID) {
			case $fixtures_live_options['fl_fixtures_page_id'] :
				return 'fixtures';
			break;
			case $fixtures_live_options['fl_results_page_id'] :
				return 'results';
			break;
			case $fixtures_live_options['fl_team_page_id'] :
				return 'team';
			break;
			case $fixtures_live_options['fl_archives_page_id'] :
				return 'archives';
			break;
		}
	}
	

}

/**
* Function - accountCanAccessMethod
* @return int
*/
function get_data_type() {
	global $fixtures_live_options;
	$fixtures_live_options['plugin_data_type'] = 1;
	return $fixtures_live_options['plugin_data_type'];
}

/**
* Function - accountCanAccessMethod
* @return boolean
*/
function using_embeds() {
	return get_data_type() == 0;
}

/**
* Function - accountCanAccessMethod
* @param $post
* @return boolean
*/
function get_fixtures_live_id($post = null) {
	if(!$post) { global $post; }
	if(is_numeric($post)) {
		return get_post_meta($post,'fixtures_live_id',true);
	} else {
		return get_post_meta($post->ID,'fixtures_live_id',true);
	}

}


/**
* Function - accountCanAccessMethod
* @param $method_name
* @return boolean
*/
function accountCanAccessMethod($method_name) {
	global $fixtures_live_api_permissions;
	return in_array($method_name, $fixtures_live_api_permissions);
}


/**
* Function - isAccountLeague
* @param $fixtures_live_league_id
* @return ID
*/
function isAccountLeague($league_id) {
	$account = new WP_Query( 
		array( 'post_type' => array('league','cup'), 'meta_key' => 'fixtures_live_id', 'meta_value' => intval($league_id), 'type' => 'numeric' ) 
	);
	if($account->posts) {
		return $account->posts[0];
	} else {
		return false;
	}
}

/*====================================================
=	FIXTURES LIVE API METHODS AND DATA FLOW
=
=   V1
=	==
=   
=   Below is all the methods and calls to the external
=   Fixtures live end point. 
=
==================================================== */


/*====================================================
=	FIXTURES LIVE DIVISONS
==================================================== */

/**
* Function - get_divison_data
* @param $Ffixtureslive_id (int)
* @param $params (Array)
* @return XML
*/
function get_divison_data($fixtureslive_id = null,$params) {
	$fixtureslive_id = ($fixtureslive_id ) ? $fixtureslive_id : get_fixtures_live_id();
	$transient_prefix = $params['data']; 

	// -- Get Transient // Will only return true if its in date and has content
	if( get_transient('fixtures_live_transiet_' . $transient_prefix . '_' . $fixtureslive_id)) {
		$cache = get_transient('fixtures_live_transiet_' . $transient_prefix . '_' . $fixtureslive_id);
		if($cache) {
			return $cache;
		}
	} 

	// -- If Nothing there then we need to reset it or create it
	if($params['data'] == 'standings') {
		$raw = get_raw_fl_division_data($fixtureslive_id);
	} elseif($params['data'] == 'fixtures') {
		$raw = get_raw_fl_fixtures_data($fixtureslive_id);
	} elseif($params['data'] == 'results') {
		$raw = get_raw_fl_results_data($fixtureslive_id);
	} elseif($params['data'] == 'scorers') {
		$raw = get_raw_fl_scorers_data($fixtureslive_id);
	}	

	// -- Set it and return it

	if($raw) {
		set_transient('fixtures_live_transiet_' . $transient_prefix . '_' . $fixtureslive_id, $raw,5*MINUTE_IN_SECONDS);
		return $raw;
	}	
}




/**
* Function - get_raw_fl_division_data
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_division_data($id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetLeagueTable')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetLeagueTable($id);
		return $response;
	}
}

/**
* Function - get_raw_fl_fixtures_data
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_fixtures_data($id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetFixturesByDivision')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		//$response = $FL_METHOD_CALL->GetFixturesByLeague($id, date('Y') . '-09-01T08:00:00',date('Y')+1 . '-04-30T20:30:00');
		$response = $FL_METHOD_CALL->GetFixturesByDivision($id, date('Y-m-d'),date('Y')+1 . '-05-01');
		return $response;
	}
}

/**
* Function - get_raw_fl_results_data
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_results_data($id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetFixturesByDivision')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		//$response = $FL_METHOD_CALL->GetFixturesByLeague($id, date('Y') . '-09-01T08:00:00',date('Y')+1 . '-04-30T20:30:00');
		if(@$_GET['external']) {
			$response = $FL_METHOD_CALL->GetFixturesByDivision($id, '1970-09-01',date('Y') . '-' . date('m') . '-' . date('d'));
		} else {
			$response = $FL_METHOD_CALL->GetFixturesByDivision($id, '1970-09-01',date('Y') . '-' . date('m') . '-' . date('d'));
		}
		return $response;
	}
}

/**
* Function - get_raw_fl_scorers_data
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_scorers_data($id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetTop10HockeyScorersForDivision')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetTop10HockeyScorersForDivision($id);
		return $response;
	}
}


/**
* Function - render_division_table
* @return HTML
*/
function render_division_table($id = false, $show_header = 'true', $echo = true) {
	$strHTML = null;
	$data = get_divison_data($id,array('type'=>'division','data'=>'standings'));
	$division_data = new SimpleXMLElement($data);
	if($division_data) {
		$promotion = intval($division_data->Promotion) ? intval($division_data->Promotion) : 0;
		$promotion_possible = intval($division_data->PromotionPoss) ? intval($division_data->PromotionPoss) : 0;
		$relegation = intval($division_data->Relegation) ? intval($division_data->Relegation) : 0;
		$relegation_possible = intval($division_data->RelegationPoss) ?intval($division_data->RelegationPoss) : 0;
		// -- Division Header
		if($show_header != 'false') {
			 $strHTML .= '<div class="league_details_header">';
				 if($division_data->LeagueLogo != '') { 
					$strHTML .= '<figure>
						<img src="http://www.fixtureslive.com/uploads/logos/' .  $division_data->LeagueLogo . '" alt="' .  $division_data->LeagueName . '" />
					</figure>';
				 } 
			$strHTML .='<header><h2>' . $division_data->LeagueName . '</h2></header></div>';
		} 
		// -- Division Table Headers
		$strHTML.='<table border="1" class="fl_table division">
			<tr>
				<th></th>
				<th></th>
				<th></th>
				<th class="tac">P</th>
				<th class="tac">W</th>
				<th class="tac">D</th>
				<th class="tac">L</th>
				<th class="tac">F</th>
				<th class="tac">A</th>
				<th class="tac">+/-</th>
				<th class="tac">Pts</th>
			</tr>';
			 $c=1; foreach($division_data->LeagueTableRows->LeagueTableRow as $standing) {  

			
				$classes = array();
				if($promotion == $c || $relegation == $c) $classes[] = 'promotion_line';
				if($promotion_possible == $c || $relegation_possible == $c) $classes[] = 'promotion_possible_line';
				$strHTML.= '<tr class="' . implode(' ', $classes) . '">';
				$strHTML.= '<td>' . $standing->Position . '</td>';
				$strHTML.= '<td class="tac">';
					if(intval($standing->PreviousPosition) > intval($standing->Position) && intval($standing->PreviousPosition) > 0) : ?>
						<?php $strHTML.='<i class="fa fa-caret-up"></i>'; ?>
					<?php elseif(intval($standing->PreviousPosition) < intval($standing->Position) && intval($standing->PreviousPosition) > 0): ?>
						<?php $strHTML.='<i class="fa fa-caret-down"></i>'; ?>
					<?php else: ?>
						<?php $strHTML.='-'; ?>
					<?php endif; ?>
				<?php $strHTML.= '</td>
				<td><span class="team-color" style="background:' .  $standing->TeamColour . '"></span> 
				<a rel="nofollow" href="' . add_query_arg('item', str_replace(',', '', $standing->TeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE) ) . '">' . $standing->ClubTeamName . '</a></td>';
				$strHTML.='<td class="tac">' . $standing->Played . '</td>';
				$strHTML.='<td class="tac">' .  $standing->Won . '</td>';
				$strHTML.='<td class="tac">' .  $standing->Drawn . '</td>';
				$strHTML.='<td class="tac">' .  $standing->Lost . '</td>';
				$strHTML.='<td class="tac">' .  number_format((int)$standing->GoalsFor) . '</td>';
				$strHTML.='<td class="tac">' .  number_format((int)$standing->GoalsAgainst) . '</td>';
				$strHTML.='<td class="tac">' .  $standing->GoalDiff . '</td>';
				$strHTML.='<td class="tac">';
				if((int)$standing->PointsDeducted>0) : 
					$strHTML.= number_format((int)$standing->NetPoints);  
				 else: 
					$strHTML.= number_format((int)$standing->Points); 
				 endif;
				$strHTML.= '</td>';
			$strHTML.= '</tr>';
		$c++; } 
		$strHTML.= '</table>';
	}
	if($echo) {
		echo $strHTML;
	} else {
		return $strHTML;
	}
}

/**
* Function - render_division_fixtures
* @param $fixtureslive_id (int)
* @return HTML
*/
function render_division_fixtures($fixtureslive_id = null, $echo = true) {
	$fixtureslive_id = ($fixtureslive_id) ? $fixtureslive_id : '';
	$data = get_divison_data($fixtureslive_id ,array('type'=>'division','data'=>'fixtures'));
	if($data) {
		$sHTML = null;
		$fixture_data = new SimpleXMLElement($data);
		$previous_date = null;
		if (count($fixture_data->Fixture)>0) {
			foreach($fixture_data->Fixture as $fixture) {
				//print_r($fixture);
				$fixture_date = explode('T',$fixture->MatchDate); 	
				if($previous_date != $fixture_date[0]) 	{
					if($previous_date) { $sHTML .= '</table></div>'; }
					$sHTML .= '<div class="fixture_block"><table cellpadding="5" cellspacing="0" border="1" class="fl_table fixtures"><th colspan="3">' . date('d F Y', strtotime($fixture_date[0])) . '</th>';
				}
				$sHTML .= '<tr><td class="home"><a rel="nofollow" href="' . add_query_arg('item', number_format((int)$fixture->HomeTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $fixture->HomeClub . ' ' . $fixture->HomeTeam . '</a></td><td class="result">' . $fixture->HomeGoals . '-' . $fixture->AwayGoals . '</td><td class="away"><a rel="nofollow" href="' . add_query_arg('item', number_format((int)$fixture->AwayTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $fixture->AwayClub . ' ' . $fixture->AwayTeam . '</a></td></tr>';

				$previous_date = $fixture_date[0];
			}
			$sHTML .= '</table></div>';
		} else {
			$sHTML .= '<p>No future fixtures.</p>';
		}
		if($echo) {
			echo $sHTML;
		} else {
			return $sHTML;
		}
	}

}

/**
* Function - render_competition_top_scorers
* @param $fixtureslive_id (int)
* @return HTML
*/
function render_competition_top_scorers($fixtureslive_id = null) {
	$fixtureslive_id = ($fixtureslive_id) ? $fixtureslive_id : '';

	$data = get_divison_data($fixtureslive_id ,array('type'=>'division','data'=>'scorers'));
	


	if($data) {
		$scorers_data = new SimpleXMLElement($data);

		if($scorers_data) {
			?>
			<table border="1" class="fl_table scorers">
			<tr>
				<th>Name</th>
				<th>Club</th>
				<th class="tac">Open</th>
				<th class="tac">Stroke</th>
				<th class="tac">PC</th>
				<th class="tac">Total</th>
			</tr>
			<?php
			foreach($scorers_data as $scorer) {
				?>
				<tr>
					<td><?php echo $scorer->FirstName; ?> <?php echo $scorer->LastName; ?></td>
					<td><?php echo $scorer->Club; ?></td>
					<td class="tac"><?php echo $scorer->OpenPlay; ?></td>
					<td class="tac"><?php echo $scorer->PenaltyStroke; ?></td>
					<td class="tac"><?php echo $scorer->PenaltyCorner; ?></td>
					<td class="tac"><?php echo $scorer->GoalTotal; ?></td>
				</tr>
				<?php
			}
			?></table><?php
		} else {
			?>
			<p>None entered</p>
			<?php 
		}
		
	} else {
		?>
				<p>None entered</p>
		<?php
	}
}

/**
* Function - render_division_fixtures
* @param $render_division_results (int)
* @return HTML
*/
function render_division_results($fixtureslive_id = null, $echo = true) {
	$fixtureslive_id = ($fixtureslive_id) ? $fixtureslive_id : '';
	$data = get_divison_data($fixtureslive_id,array('type'=>'division','data'=>'results'));
	if($data) {
		$sHTML = null;
		$fixture_data = new SimpleXMLElement($data);
		$previous_date = null;
		// -- Reverse the array so latest first
		if($fixture_data) {
			$reverseArray = (array) $fixture_data;
			$items = @array_reverse($reverseArray["Fixture"]);
			if (count($items)>0) {
				foreach($items as $fixture) {
					$fixture_date = explode('T',$fixture->MatchDate); 

					if($previous_date != $fixture_date[0]) {
						if($previous_date) { $sHTML .= '</div>'; }
						$sHTML .= '<div class="fixture_block"><table cellpadding="5" cellspacing="0" border="1" class="fl_table fixtures"><th colspan="3">' . date('d F Y', strtotime($fixture_date[0])) . '</th>';
					}
					$sHTML .= '<tr><td class="home"><a href="' . add_query_arg('item', number_format((int)$fixture->HomeTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $fixture->HomeClub . ' ' . $fixture->HomeTeam . '</a></td><td class="result">' . $fixture->HomeGoals . '-' . $fixture->AwayGoals . '</td><td class="away"><a href="' . add_query_arg('item', number_format((int)$fixture->AwayTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $fixture->AwayClub . ' ' . $fixture->AwayTeam . '</a></td></tr>';

					if($fixture->Notes != '') {
						$sHTML .= '<tr><td colspan="3" class="walkover">' . $fixture->Notes . '</td></tr>';
					}

					$previous_date = $fixture_date[0];
				}
				$sHTML .= '</table></div>';
			} else {
				$sHTML .= '<p>No Results.</p>';
			}
		} else {
			$sHTML .= '<p>No Results.</p>';
		}	
		if($echo) {
			echo $sHTML;
		} else {
			return $sHTML;
		}
	}
}


/*====================================================
=	FIXTURES LIVE CUPS
==================================================== */


/**
* Function - get_cup_data
* @param $Ffixtureslive_id (int)
* @param $params (Array)
* @return XML
*/
function get_cup_data($fixtureslive_id = null,$params) {

	$fixtureslive_id = ($fixtureslive_id ) ? $fixtureslive_id : get_fixtures_live_id();
	$transient_prefix = $params['data']; 

	// -- Get Transient // Will only return true if its in date and has content
	if( get_transient('fixtures_live_transiet_' . $transient_prefix . '_' . $fixtureslive_id)) {
		$cache = get_transient('fixtures_live_transiet_' . $transient_prefix . '_' . $fixtureslive_id);
		if($cache) {
			return $cache;
		}
	} 

	$raw_cup_rounds = get_raw_fl_cup_rounds($fixtureslive_id);

	if($raw_cup_rounds) {
		set_transient('fixtures_live_transiet_' . $transient_prefix . '_' . $fixtureslive_id, $raw,5*MINUTE_IN_SECONDS);
		return $raw_cup_rounds;
	}	

}

/**
* Function - get_raw_fl_cup_rounds
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_cup_rounds($id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetCupRounds')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetCupRounds($id);
		return $response;
	}
}

/**
* Function - get_raw_fl_cup_fixtures
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_cup_fixtures($round_id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetCupFixedSpotsForRound') && $round_id) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetCupFixedSpotsForRound($round_id);
		return $response;
	}
}	

/**
* Function - get_raw_fl_cup_fixtures_legacy
* @param $id (int / Fixtures Live ID)
* @return XML
*/
function get_raw_fl_cup_fixtures_legacy($round_id) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetFixturesForCupRound') && $round_id) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetFixturesForCupRound($round_id);
		return $response;
	}
}

/**
* Function - render_cup();
* @return HTML
*/
function render_cup($id = false, $echo = true) {
	$fixtureslive_id = (!$id) ? get_fixtures_live_id() : $id;
	//$data = get_cup_data($fixtureslive_id,array('data'=>'cup'));
	if(using_embeds()) {
		echo do_shortcode('[fl_legacy_embed_league id="' . $fixtureslive_id . '"]');
		return;
	}
	// -- If We dont have permisiion show embed
	if(!accountCanAccessMethod('GetCupFixedSpotsForRound') || !accountCanAccessMethod('GetCupRounds')) {
		echo do_shortcode('[fl_legacy_embed_league id="' . $fixtureslive_id . '"]');
		return;
	}
	// -- Get A Cached Version
	if ( get_transient('fixtures_live_transiet_cup_' . $fixtureslive_id) ) {
		$cup_data_array = get_transient('fixtures_live_transiet_cup_' . $fixtureslive_id);
	} else {
		// -- We Need To Get A New Version
		$data = get_raw_fl_cup_rounds($fixtureslive_id);
		if($data) {
			$cup_data_xml = new SimpleXMLElement($data);
			$cup_data_array = array();
			if (count($cup_data_xml)>0) {
				foreach($cup_data_xml as $item) {
					// -- Sort The Cup Logic 
					$render_function = 'render_cup_fixtures';
					$fixtures = get_raw_fl_cup_fixtures((int)$item->RoundID);
					
					if(! new SimpleXMLElement($fixtures)) {
						$fixtures = get_raw_fl_cup_fixtures_legacy((int)$item->RoundID);
						$render_function = 'render_cup_fixtures_legacy';
					}
		
					// -- Assign to the Array
					$cup_data_array[(int)$item->RoundID] = array(
						'RoundID' => (string)$item->RoundID,
						'RoundName' => (string)$item->RoundName,
						'RoundDate' => (string)$item->RoundDate,
						'RoundType' => (string)$item->RoundType,
						'RoundNumber' => (int)$item->RoundNumber,
						'IsFinal' => (string)$item->IsFinal,
						'fixtures' => $fixtures,
						'render_function' => $render_function
					);
				}
				set_transient('fixtures_live_transiet_cup_' . $fixtureslive_id, $cup_data_array, 5*MINUTE_IN_SECONDS);
			}
		} 	
	}

	$round_content = '';
	if($cup_data_array) {
		$round_tabs = "";
		// -- So We Have The Cup Data From The API Lets Do Something Cool With It
		foreach($cup_data_array as $round) {
			//print_r($round);
			// -- Do Tabs
			$round_tabs .= '<li><a href="#">' . $round['RoundName'] . '</a></li>';
			// -- Do Content
			$round_content .= '<li class="slide">';
			$round_content .= '<header><h2>' . $round['RoundName'] . '</h2></header>';
			$fixture_date = explode('T',$round['RoundDate']); 
			$round_content .= '<p class="cup_fixture_date">Ties to be played on: ' . date('d F Y', strtotime($fixture_date[0])) . '</p>';
			$round_content .= call_user_func( $round['render_function'], $round['fixtures']);
			$round_content .= '</li>';
		}
		$html = '<div class="cup_viewer"><ul class="slide_indexes">' . $round_tabs . '</ul> <div class="cup_flexi"><ul class="slides">' . $round_content . '</ul></div> </div>';
		if($echo) {
			echo $html;
		} else {
			return $html;
		}
	} else {	
		// -- Show Shortcode as a fallback
		echo do_shortcode('[fl_legacy_embed_league id="' . $fixtureslive_id . '"]');
	}
}


function render_cup_fixtures($data) {
	// -- Right Set Some Vars Us
	$HTML = '';
	$previous_date = '';
	$matches_rendered = array();
	// -- Load Fixture XML In
	$fixture_data = new SimpleXMLElement($data);
	$fixture_data->registerXPathNamespace('fl' , 'http://www.fixtureslive.com/');
	// -- Begin Loop
	if(count($fixture_data->CupRoundSpot)>0) {
		foreach($fixture_data->CupRoundSpot as $fixture) {
			$fixture_date = explode('T',$fixture->FixtureDate);
			if($previous_date != $fixture_date[0]) 	{
				if($previous_date) { $HTML .= '</table></div>'; }
				$header_time = (date('Y', strtotime($fixture_date[0]))!='0001') ? date('d F Y', strtotime($fixture_date[0])) : 'N/A'; 
				$HTML .= '<div class="fixture_block"><table cellpadding="5" cellspacing="0" border="1" class="fl_table fixtures"><tr><th colspan="3">' . $header_time . '</th></tr>';
			}
			// -- Check To See If We Have The Match Already Rendered
			if(!in_array($fixture->MatchNumber, $matches_rendered)) {
				// -- Bit Of Xpath Magic Here
				$match_data = $fixture_data->xpath("/fl:ArrayOfCupRoundSpot/fl:CupRoundSpot/fl:MatchNumber[.='" . $fixture->MatchNumber . "']/..");
				// -- Check If We Have A Match
				if(sizeof($match_data)>1) {
					$home_team = $match_data[0];
					$away_team = $match_data[1];				
					// -- If there is an away team then there must be a fixture
					if(!(int)$home_team->Team->ClubID && !(int)$away_team->Team->ClubID) {
						$HTML .= '<tr><td class="bye">No Fixtures Announced</td></tr>';
					} elseif(!(int)$away_team->Team->ClubID) {
						$HTML .= '<tr><td class="bye tc" colspan="3">';
						$HTML .= $home_team->Team->Club . ' ' . $home_team->Team->TeamName . ' - BYE';
						$HTML .= '</td></tr>';
					} elseif((int)$home_team->Team->ClubID) {
						$HTML .= '<tr><td class="home">';
						$HTML .= '<a rel="nofollow" href="' . add_query_arg('item', number_format((int)$home_team->Team->TeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $home_team->Team->Club . ' ' . $home_team->Team->TeamName  . '</a>';
						$HTML .= '</td>';
						$HTML .=  '<td class="result">';
						if(!$home_team->WONotes) {
							$HTML .= $home_team->Goals. ' - ' . $away_team->Goals;	
						} else {
							$HTML .= 'vs';
						}
						$HTML .= '</td>';
						$HTML .= '<td class="away">';
						$HTML .= '<a rel="nofollow" href="' . add_query_arg('item', number_format((int)$away_team->Team->TeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $away_team->Team->Club . ' ' . $away_team->Team->TeamName  . '</a>';
						$HTML .= '</td></tr>';
						if($home_team->WONotes ) {
							$HTML .= '<tr><td class="walkover"  colspan="3">' . $home_team->WONotes . '</td></tr>';
						}
					} else {
						$HTML .= '<tr><td class="bye">No Fixtures Announced</td></tr>';
					}
				} else {
					if(!(int)$home_team->Team->ClubID && !(int)$away_team->Team->ClubID) {
						$HTML .= '<tr><td class="bye">No Fixtures Announced</td></tr>';
					} else {
						$bye_team = $match_data[0];
						$HTML .= '<tr><td class="bye tc" colspan="3">';
						$HTML .= $bye_team->Team->Club . ' ' . $bye_team->Team->TeamName . ' - xxBYE';
						$HTML .= '</td></tr>';
					}
				}
				// -- Assign Match To Array
				$matches_rendered[] = (int)$fixture->MatchNumber;
			}
			// -- Set Up Scroller Flag for creating Panels
			$previous_date = $fixture_date[0];
		}
		$HTML .= '</table></div>';
		return $HTML;
	}
}


function render_cup_fixtures_legacy($data) {

	// -- Right Set Some Vars Us
	$HTML = '';
	$previous_date = '';
	$matches_rendered = array();
	// -- Load Fixture XML In
	$fixture_data = new SimpleXMLElement($data);
	if(count($fixture_data->Fixture)>0) {
		foreach($fixture_data->Fixture as $fixture) {
			$fixture_date = explode('T',$fixture->MatchDate);
			if($previous_date != $fixture_date[0]) 	{
				if($previous_date) { $HTML .= '</table></div>'; }
				$header_time = (date('Y', strtotime($fixture_date[0]))!='0001') ? date('d F Y', strtotime($fixture_date[0])) : 'N/A'; 
				$HTML .= '<div class="fixture_block"><table cellpadding="5" cellspacing="0" border="1" class="fl_table fixtures"><tr><th colspan="3">' . $header_time . '</th></tr>';
			}

			$HTML .= '<tr><td class="home">';
			$HTML .= '<a rel="nofollow" href="' . add_query_arg('item', number_format((int)$fixture->HomeTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $fixture->HomeClub . ' ' .$fixture->HomeTeam  . '</a>';
			$HTML .= '</td>';
			$HTML .=  '<td class="result">';
			if($fixture->HomeGoals && $fixture->AwayGoals) {
				$HTML .= $fixture->HomeGoals . ' - ' . $fixture->AwayGoals;	
			} else {
				$HTML .= 'vs';
			}
			$HTML .= '</td>';
			$HTML .= '<td class="away">';
			$HTML .= '<a rel="nofollow" href="' . add_query_arg('item', number_format((int)$fixture->AwayTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)) . '">' . $fixture->AwayClub . ' ' .$fixture->AwayTeam  . '</a>';
			$HTML .= '</td></tr>';
			if($fixture->CupRound->Notes != '' ) {
				$HTML .= '<tr><td class="walkover"  colspan="3">' . $fixture->CupRound->Notes  . '</td></tr>';
			}
		}
		$HTML .= '</table></div>';
		return $HTML;
		
	}
}


/*====================================================
=	FIXTURES LIVE MAP
==================================================== */

/**
* Function - render_league_map
* @return HTML
*/
function get_raw_fl_mapData($league_id = null) {
	global $fixtures_live_options;
	if(accountCanAccessMethod('GetClubLocationsForLeague')) {
		if( get_transient('fixtures_live_transiet_club_locations')) {
			$raw = get_transient('fixtures_live_transiet_club_locations');
		} else {
			$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
			if($league_id) {
				$raw = $FL_METHOD_CALL->GetClubLocationsForLeague($league_id);
			} else {
				$leagues = explode(',',$fixtures_live_options['league_id']);
				if($leagues) {
					$raw = $FL_METHOD_CALL->GetClubLocationsForLeague($leagues[0]);
				}
			}
			set_transient('fixtures_live_transiet_club_locations', $raw,5*MINUTE_IN_SECONDS);
		}
		return $raw;
	}
}

/*====================================================
=	FIXTURES LIVE TEAM
==================================================== */

/**
* Function - get_raw_fl_division_data
* @param $id (int / Fixtures Live Team ID)
* @return XML
*/
function get_raw_fl_teamData($team_id) {
	global $fixtures_live_options;

	if(accountCanAccessMethod('GetTeamDetailsPublic')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetTeamDetailsPublic($team_id);
		return $response;
	}
}


/**
* Function - get_raw_fl_division_data
* @param $id (int / Fixtures Live Team ID)
* @return XML
*/
function get_raw_fl_teamFixtures($team_id) {
	global $fixtures_live_options;
	global $fixtures_live_api_permissions;
	if(accountCanAccessMethod('GetFixturesByTeam')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetFixturesByTeam($team_id);
		return $response;
	}
}


/**
* Function - render_team_page
* @return HTML
*/
function fl_get_team_details($team_id) {
	global $fixtures_live_api_permissions;
	if(accountCanAccessMethod('GetTeamDetailsPublic')) {
		return get_raw_fl_teamData($team_id);
	} else {
		do_shortcode('[fl_legacy_embed_team id="' .$team_id . '"]'); 
	}

}

/**
* Function - render_team_details
* @return HTML
*/
function render_team_details($team_id) {

	if( using_embeds() ) {
		do_shortcode('[fl_legacy_embed_team id="' .$team_id . '"]'); 
	} else {
		$team_details = fl_get_team_details($team_id);
		if($team_details) {
			$team_details = new SimpleXMLElement($team_details);
			$team = $team_details[0];
			?>
			<div class="fixtures_live single_team_info <?php if($team->LogoLocation != '') : ?>has-logo<?php endif; ?>">
				<?php if($team->LogoLocation != '') : ?>
				<div class="single_team_details_logo">			
					<figure>	
						<img src="http://www.fixtureslive.com/uploads/logos/<?php echo $team->LogoLocation; ?>" alt="<?php echo $team->TeamName; ?>" />			
					</figure>
				</div>
				<?php endif; ?>
				
				<div class="single_team_details">
					<h2><?php echo $team->ClubName; ?>  <?php echo $team->TeamName; ?></h2>
					<h3><?php echo $team->Captain->FirstName; ?> <?php echo $team->Captain->LastName; ?></h3>
					<ul>
						<?php if($team->Captain->HomePhone != '') : ?>
							<li>Home: <?php echo $team->Captain->HomePhone; ?></li>
						<?php endif; ?>
						<?php if($team->Captain->MobilePhone  != '') : ?>
							<li>Mobile: <?php echo $team->Captain->MobilePhone; ?></li>
						<?php endif; ?>
						<?php if($team->Captain->WorkPhone  != '') : ?>
							<li>Work: <?php echo $team->Captain->WorkPhone; ?></li>
						<?php endif; ?>
						<?php if($team->Captain->Email  != '') : ?>
							<li>Email: <a href="mailto:<?php echo $team->Captain->Email; ?>"><?php echo $team->Captain->Email; ?></a></li>
						<?php endif; ?>
						<?php if($team->Website  != '') : ?>
							<li>URL: <a target="_blank" href="<?php echo $team->Website; ?>"><?php echo str_replace('http://', '', $team->Website); ?></a></li>
						<?php endif; ?>
						<?php if($team->TwitterTag != '') : ?>
							<li>Twitter: Follow <a href="https://www.twitter.com/<?php echo $team->TwitterTag; ?>"><?php echo $team->TwitterTag; ?></a></li>
						<?php endif; ?>
						
					</ul>
				</div>
			</div>

			<div class="fixtures_live team_fixtures">
				<?php echo render_team_fixtures($team->TeamID); ?>
			</div>


			<?php 
		}
	}
	//return fl_get_team_details($team_id);
}


/**
* Function - render_team_fixtures
* @return HTML
*/
function render_team_fixtures($team_id) {

	if( get_transient('fixtures_live_transiet_team_' . $team_id)) {
		$data = get_transient('fixtures_live_transiet_team_' . $team_id );
	} else {
		$data = get_raw_fl_teamFixtures($team_id);
		if($data) {
			set_transient('fixtures_live_transiet_team_' . $team_id, $data,5*MINUTE_IN_SECONDS);
		}
	}

	$data = get_raw_fl_teamFixtures($team_id);
	if(!$data) return;
	$xml = new SimpleXMLElement($data);
	$output="<table class='fl_table team-details'>";
	$output.="<tr><th>Opponent</th><th>Score</th><th>Competition</th><th>Date</th><th class=\"tac no-mob-table\">H/A</th><th>Venue</th></th>";
	//print_r($xml);
	foreach ($xml->Fixture as $licenseElement)
	{

		$outputRowHeader='<tr>';
		$outputRowItem;
		//echo " <br>";
 
 		// -- Its a Home Fixture
		if($team_id == (int)$licenseElement->HomeTeamID) {
			
			if((int)$licenseElement->HomeGoals > (int)$licenseElement->AwayGoals ) {
				$tdClass = 'result-win';
			} elseif ( (int)$licenseElement->HomeGoals < (int)$licenseElement->AwayGoals ) {
				$tdClass = 'result-loss';
			} else {
				$tdClass = 'result-draw';
			}
			$ha = 'H';
			 $outputRowItem= $outputRowHeader. '<td><span class="team-color  no-mob" style="background:' . $licenseElement->AwayClubColor . '"></span> <a rel="nofollow" href="'.add_query_arg('item', str_replace(',', '', $licenseElement->AwayTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)).'">'. $licenseElement->AwayClub .' '. $licenseElement->AwayTeam .'</a></td>';
		} else {
			// -- Away Fixture
			if((int)$licenseElement->AwayGoals > (int)$licenseElement->HomeGoals ) {
				$tdClass = 'result-win';
			} elseif ( (int)$licenseElement->AwayGoals < (int)$licenseElement->HomeGoals ) {
				$tdClass = 'result-loss';
			} else {
				$tdClass = 'result-draw';
			}
			$ha = 'A';
		  	$outputRowItem = $outputRowHeader. '<td><span class="team-color no-mob" style="background:' .  $licenseElement->HomeClubColor . '"></span> <a rel="nofollow" href="'.add_query_arg('item', str_replace(',', '', $licenseElement->HomeTeamID), get_permalink(FIXTURES_LIVE_TEAM_PAGE)).'">'. $licenseElement->HomeClub .' '. $licenseElement->HomeTeam .'</a></td>';
		}

		

		if($ha == 'H') {
			if(isset($licenseElement->Result)) {
				$outputRowItem.=  '<td  class="' . $tdClass . ' tac">'.$licenseElement->HomeGoals.'-'.$licenseElement->AwayGoals.'</td>';				
				} else {
				$outputRowItem.=  '<td class="tac">vs</td>';				
			}
		} else {
			if(isset($licenseElement->Result)) {
				$outputRowItem.=  '<td  class="' . $tdClass . ' tac">'.$licenseElement->AwayGoals.'-'.$licenseElement->HomeGoals.'</td>';				
				} else {
				$outputRowItem.=  '<td class="tac">vs</td>';				
			}
		}
		
		$is_installed_comp = isAccountLeague($licenseElement->DivisionID);

		$comp_url = $is_installed_comp  ? get_permalink( $is_installed_comp ) : remove_query_arg('item',add_query_arg( array('external' => str_replace(',', '', $licenseElement->DivisionID),'external_name' => strtolower(str_replace(' ','-',$licenseElement->LeagueName)) ) ), get_permalink(FIXTURES_LIVE_EXTERNAL_COMP_PAGE) );

		if($is_installed_comp) {
			$outputRowItem.= '<td class="tac"><a href="' . $comp_url . '">'.$licenseElement->LeagueShortName.' ' . $licenseElement->DivisionShortName.'</a></td>';
		} else {
			$outputRowItem.= '<td class="tac"><a rel="nofollow" href="' . $comp_url . '">'.$licenseElement->LeagueShortName.' ' . $licenseElement->DivisionShortName.'</a></td>';
		}	

		$outputRowItem.= '<td class="tac">'.date("d/m/y", strtotime($licenseElement->MatchDate)).' @ '.date("H:i", strtotime($licenseElement->MatchDate)).'</td>';					
		$outputRowItem.= '<td class="tac no-mob-table">'.$ha.'</td>';	
		$outputRowItem.= '<td class="tac"><a rel="nofollow" href="'.remove_query_arg('item',add_query_arg( array('venue' => str_replace(',', '', $licenseElement->Venue->VenueID),'venue_name' => strtolower(str_replace(' ','-', $licenseElement->Venue->VenueName) ) ),get_permalink(FIXTURES_LIVE_VENUES_PAGE) ) ). '">'. $licenseElement->Venue->VenueInitials .'</a></td>';	
		$outputRowItem.'</tr>';
	
		if($licenseElement->Notes != '') {
			$outputRowItem .= '<tr>';
			$outputRowItem .= '<td  class="walkover" colspan="6">' . $licenseElement->Notes . '</td>';
			$outputRowItem .= '</tr>';
		}
		
		$output .= $outputRowItem;

	}
	
	$output.= $outputRowHeader."</tbody></table>";
	return $output;		
}

// -- Used for removing brackets
function venue_summarise($str) {
	$short_venue = "";
	$str =  trim(preg_replace('/\s*\([^)]*\)/', '', $str));
	$str_parts = explode(' ',$str);
	foreach($str_parts as $str) {
		$short_venue .= substr($str, 0, 1);
	}
	return $short_venue ;
}

/**
* Function - render_divison_team_list
* @return HTML
*/
function render_divison_team_list() {

}

/*====================================================
=	FIXTURES LIVE EXTERNAL COMPS
==================================================== */
function render_external_comp($comp_id) {
	global $fixtures_live_options;

	if(!accountCanAccessMethod('GetCompetitionTypeByDivisionID') || !$comp_id) {
		do_shortcode('[fl_legacy_embed_league_cb id="' .$comp_id . '"]'); 
	} else {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetCompetitionTypeByDivisionID($comp_id);
		//if(!$respone) return;

		$xml = new SimpleXMLElement($response);
		$comp_type = (int)$xml->CompetitionType;
		
		if($comp_type == 1) {
			render_division_table($comp_id);
		} else {
			render_cup(	$comp_id );	
		}

	}


	// GetCompetitionTypeByDivisionID
}

/*====================================================
=	FIXTURES LIVE VENUES
==================================================== */

/**
* Function - fl_get_venue_details
* @return XML
*/
function fl_get_venue_details($venue_id) {
	global $fixtures_live_api_permissions, $fixtures_live_options;
	if(accountCanAccessMethod('GetVenue')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetVenue($venue_id);
		return $response;
	}

}

/**
* Function - render_venue
* @return HTML
*/
function render_venue($venue_id) {
	if($venue_id) {	
	
		$data = fl_get_venue_details($venue_id);
		if($data) {
			$xml = new SimpleXMLElement($data);
			?>
				<div data-lng="<?php echo $xml->Longitude; ?>" data-lat="<?php echo $xml->Latitude; ?>" id="venue-map"></div>
				<div class="location-details">
					<header>
						<h2><?php echo $xml->VenueName; ?></h2>
					</header>
					<address>
						<?php echo str_replace(',','<br/>', $xml->VenueAddress); ?><br/>
						<?php echo $xml->VenuePostcode; ?>
					</address>
				</div>

				<div class="directions">
					<h2>Directions</h2>
					<?php echo $xml->Directions; ?>
				</div>
			<?php
		} else {
			?>
			<p>Sorry! The venue requested has no data.</p>
			<?php
		}
	} 
}

/*====================================================
=	FIXTURES LIVE SCORERS
==================================================== */
/**
* Function - render_team_page
* @return HTML
*/
function fl_get_top_10_scorers_details($division_id) {
	global $fixtures_live_api_permissions, $fixtures_live_options;
	if(accountCanAccessMethod('GetTop10HockeyScorersForDivision')) {
		$FL_METHOD_CALL = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		$response = $FL_METHOD_CALL->GetTop10HockeyScorersForDivision($division_id);
		return $response;
	}
}



?>