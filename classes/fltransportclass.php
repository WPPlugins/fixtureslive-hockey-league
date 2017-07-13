<?php
/**
 * Fixtures Live Transport Helper
 *
 * Helper to use Fixtures Live Data API
 *
 * @copyright  2013 Clark Studios
 * @version    Release: 1
 * @since      Class available since Release 1.0
 */ 



class FixturesLiveTransportHelper {

	private $AccountKey;
	private $url_params;
	private $mode;
	var $log;
	var $endPoint = 'http://www.fixtureslive.com/external/webservice/fl.asmx/';
	
	/**
	* Constructor
	*
	* @param  Account Key    $key  FL API Key 
	*/
	public function __construct($key,$mode='XML') {
		$this->AccountKey = $key;
		$this->mode = $mode;
	}
	
	/**
	* GetAllMatchInfoForLeague
	*
	* @param  League ID    $intLeagueID  ID of the league
	* @return mixed
	*/
	public function GetAllMatchInfoForLeague($intLeagueID) {
		return $this->_request('GetAllMatchInfoForLeague',array('intLeagueID' => $intLeagueID));
	}
	
	/**
	* GetBoxLeaguePersonalStats
	*
	* @param  Player ID    $intPlayerID  ID of the player
	* @param  Sport ID    $intSportID  ID of the FL Sport
	* @param  Club ID    $intClubID  ID of the FL Club
	* @return mixed
	*/
	public function GetBoxLeaguePersonalStats($intPlayerID,$intSportID,$intClubID) {
		return $this->_request('GetBoxLeaguePersonalStats',array('intPlayerID' => $intPlayerID,'intSportID'=>$intSportID,'intClubID'=>$intClubID));
	}
	
	/**
	* GetBoxLeaguePersonalStats
	*
	* @param  Competition ID    $intCompetitionID  ID of the player
	* @param  Divison ID    $intDivisionID  ID of the FL Sport
	* @return mixed
	*/
	public function GetBoxLeagueTable($intCompetitionID, $intDivisionID) {
		return $this->_request('GetBoxLeagueTable', array('intCompetitionID' => $intCompetitionID, 'intDivisionID' => $intDivisionID));
	}
	
	/**
	* GetCalendarEvents
	*
	* @param  Organisation ID    $intOrgID  ID of the organisation
	* @return mixed
	*/
	public function GetCalendarEvents($intOrgID) {
		return $this->_request('GetCalendarEvents', array('intOrgID'=>$intOrgID));
	}
	
	/**
	* GetCalendarEventsICal
	*
	* @param  Organisation ID    $intOrgID  ID of the organisation
	* @return mixed
	*/
	public function GetCalendarEventsICal($intOrgID) {
		return $this->_request('GetCalendarEventsICal', array('intOrgID'=>$intOrgID));	
	}
	
	/**
	* GetClub
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetClub($intClubID) {
		return $this->_request('GetClub', array('intClubID'=>$intClubID));
	}	
	
	/* == NEED TO DO BELOW HERE == */
	
	/**
	* GetClubExtended
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetClubExtended() {
		return $this->_request('GetClubExtended', array());
	}
	
	/**
	* GetClubsByLeague
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetClubsByLeague() {
		return $this->_request('GetClubsByLeague', array());
	}
	
	/**
	* GetClubsByMSO
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetClubsByMSO() {
		return $this->_request('GetClubsByMSO', array());
	}
	
	/**
	* GetCompetitionFormGuide
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetCompetitionFormGuide() {
		return $this->_request('GetCompetitionFormGuide', array());
	}
	
	/**
	* GetCompetitionsForTeam
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetCompetitionsForTeam() {
		return $this->_request('GetCompetitionsForTeam', array());
	}
 	
	/**
	* GetCupRounds
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetCupRounds($intDivisionID) {
		return $this->_request('GetCupRounds', array('intDivisionID'=>$intDivisionID));
	}
	
	/**
	* GetDivisionFormGuide
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetDivisionFormGuide() {
		return $this->_request('GetDivisionFormGuide', array());
	}
	
	/**
	* GetDivisionsByLeague
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetDivisionsByLeague($intLeagueID) {
		return $this->_request('GetDivisionsByLeague', array('intLeagueID'=>$intLeagueID));
	}
	
	/**
	* GetDivisionsByTeam
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetDivisionsByTeam($intDivisionID,$datStartDate,$datEndDate) {
		return $this->_request('GetDivisionsByTeam', array('intDivisionID'=>$intDivisionID,'datStartDate'=>$datStartDate,'datEndDate'=>$datEndDate));
	}
	
	/**
	* GetFacilityProperties
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFacilityProperties() {
		return $this->_request('GetFacilityProperties', array());
	}
	
	/**
	* GetFixtureHistory
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFixtureHistory() {
		return $this->_request('GetFixtureHistory', array());
	}
	
	/**
	* GetFixturesByDivision
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFixturesByDivision($intDivisionID, $datStartDate, $datEndDate) {
		return $this->_request('GetFixturesByDivision', array('intDivisionID'=>$intDivisionID,'datStartDate'=>$datStartDate,'datEndDate'=>$datEndDate));
	}
	
	/**
	* GetFixturesByDivisionInclBUCS
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFixturesByDivisionInclBUCS() {
		return $this->_request('GetFixturesByDivisionInclBUCS', array());
	}
	
	/**
	* GetFixturesByLeague
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFixturesByLeague($intLeagueID, $datStartDate, $datEndDate) {
		return $this->_request('GetFixturesByLeague', array('intLeagueID'=>$intLeagueID,'datStartDate'=>$datStartDate,'datEndDate'=>$datEndDate));
	}
	
	/**
	* GetFixturesByMSO
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFixturesByMSO() {
		return $this->_request('GetFixturesByMSO', array());
	}
	
	/* GS : modifeid on 10/3/14 for #399 : start/*
	
	/**
	* GetFixturesByTeamLegacy
	*
	*  @return formated html string
	*/
	
	public function GetFixturesByTeamLegacy() {
		global $fixtures_live_options;
		$intTeamID=1;
		$intDivisionID=0;
				
		$foobar = new FixturesLiveTransportHelper($fixtures_live_options['fl_apikey']);
		//$foobar = new FixturesLiveTransportHelper('BEAC7FCA-3593-47A3-AE33-7444CC3F58F0');  // correct
		
		$output= $foobar->_request('GetFixturesByTeam', array('intTeamID'=>$intTeamID,'intDivisionID'=>$intDivisionID));
		return $foobar->_formatTeamXML($output);
		
		
	}
	
	
	/**
	* function name GetFixturesByTeam
	* @param  $intTeamID fixutes teamid
	* @param  $intDivisionID fixtures divistion id
	* @return xml
	* 
	*/
	public function GetFixturesByTeam($intTeamID) {
		return $this->_request('GetFixturesByTeam', array('intTeamID'=>$intTeamID,'intDivisionID'=>0));
	}
	/* GS : modifeid on 10/3/14 for #399 : start/*
	
	/**
	* GetFixturesForCupRound
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetFixturesForCupRound($intRoundID) {
		return $this->_request('GetFixturesForCupRound', array('intRoundID'=>$intRoundID));
	}

	public function GetCupFixedSpotsForRound($intRoundID) {
		return $this->_request('GetCupFixedSpotsForRound', array('intRoundID'=>$intRoundID));
	}
	
	/**
	* GetLastError
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetLastError() {
		return $this->_request('GetLastError', array());
	}
	
	/**
	* GetLatestScores
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetLatestScores() {
		return $this->_request('GetLatestScores', array());
	}

	/**
	* GetLeaguePositionHistory
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetLeaguePositionHistory() {
		return $this->_request('GetLeaguePositionHistory', array());
	}
	
	/**
	* GetLeagueTable
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetLeagueTable($intDivisionID) {
		return $this->_request('GetLeagueTable', array('intDivisionID'=>$intDivisionID));
	}
	
	/**
	* GetLeaguesBySport
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetLeaguesBySport() {
		return $this->_request('GetLeaguesBySport', array());
	}

	/**
	* GetLeaguesBySportsOrg
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetLeaguesBySportsOrg() {
		return $this->_request('GetLeaguesBySportsOrg', array());
	}
	
	/**
	* GetMSOsByCountry
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetMSOsByCountry() {
		return $this->_request('GetMSOsByCountry', array());
	}
	
	/**
	* GetMapClubs
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetMapClubs() {
		return $this->_request('GetMapClubs', array());
	}
	
	/**
	* GetMapFacilities
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetMapFacilities() {
		return $this->_request('GetMapFacilities', array());
	}
	
	/**
	* GetMapPoints
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetMapPoints() {
		return $this->_request('GetMapPoints', array());
	}
	
	/**
	* GetPointsDeductions
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetPointsDeductions() {
		return $this->_request('GetPointsDeductions', array());
	}
	
	/**
	* GetScorersForLeague
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetScorersForLeague() {
		return $this->_request('GetScorersForLeague', array('intDivisionID'=>$intDivisionID));
	}
	
	/**
	* GetSports
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetSports() {
		return $this->_request('GetSports');
	}

	/**
	* GetCompetitionTypeByDivisionID
	*
	* @param  GetCompetitionTypeByDivisionID $intDivisionID  ID of the club
	* @return mixed
	*/
	public function GetCompetitionTypeByDivisionID($intDivisionID) {
		return $this->_request('GetCompetitionTypeByDivisionID', array('intDivisionID'=>$intDivisionID));
	}

	
	
	/**
	* GetTeams
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetTeams() {
		return $this->_request('GetTeams', array());
	}
	
	/**
	* GetTeamsForLeague
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function GetTeamsForLeague() {
		return $this->_request('GetTeamsForLeague', array());
	}
	
	/**
	* SearchClubs
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function SearchClubs() {
		return $this->_request('SearchClubs', array());
	}

	/**
	* SearchClubsStartingWith
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function SearchClubsStartingWith() {
		return $this->_request('SearchClubsStartingWith', array());
	}
	
	/**
	* SearchCurrentClubs
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function SearchCurrentClubs() {
		return $this->_request('SearchCurrentClubs', array());
	}
	
	/**
	* SearchMSOs
	*
	* @param  Club ID    $intClubID  ID of the club
	* @return mixed
	*/
	public function SearchMSOs() {
		return $this->_request('SearchMSOs', array());
	}

	/**
	* GetWebServiceMethodsForAccount
	*
	* @return mixed
	*/
	public function GetWebServiceMethodsForAccount() {
		return $this->_request('GetWebServiceMethodsForAccount');
	}

	/**
	* GetClubLocationsForLeague
	*
	* @return mixed
	*/
	public function GetClubLocationsForLeague($intLeagueID) {
		return $this->_request('GetClubLocationsForLeague', array('intLeagueID'=>$intLeagueID));
	}

	/**
	* GetTeamDetailsPublic
	*
	* @return mixed
	*/
	public function GetTeamDetailsPublic($intTeamID) {
		return $this->_request('GetTeamDetailsPublic', array('intTeamID'=>$intTeamID));
	}

	/**
	* GetTop10HockeyScorersForDivision
	*
	* @return mixed
	*/
	public function GetTop10HockeyScorersForDivision($intDivisionID) {
		return $this->_request('GetTop10HockeyScorersForDivision', array('intDivisionID'=>$intDivisionID));
	}


	/**
	* GetVenue
	*
	* @return mixed
	*/
	public function GetVenue($intVenueID) {
		return $this->_request('GetVenue', array('intVenueID'=>$intVenueID));
	}

	private function _request($method, $params=array()) {
		
		$this->log('METHOD BEING CALLED: ' . $method);

		// -- Build The Full End Point
		if($params) {
			foreach($params as $k => $v) {
				$this->url_params .= '&' . $k . '=' . $v;
			}
			$this->url_params = substr(	$this->url_params, 1);
			$call_url = $this->endPoint . $method . '?' . $this->url_params . '&strAccountKey=' . $this->AccountKey; 
		} else {
			$call_url = $this->endPoint . $method . '?strAccountKey=' . $this->AccountKey; 
		}
		
		$this->log('FULL ENDPOINt:' . $call_url);

		// -- Call Fixtures Live Server With Curl
		$ch = curl_init();
		$timeout = 10;
		curl_setopt($ch, CURLOPT_URL, $call_url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);

		$this->log('OUTPUT:' . print_r($data,true));

		// -- Check If It's a valid XML String If Not Tis has failed
		if( FALSE === @simplexml_load_string($data)) {
			return false;
		} else {
			if($this->mode == 'XML') {
				return $data;
			} else {
				return true;
			}
		}
	}

	private function log($msg) {
		$this->log .= '<li><b>Log @ ' . time() . ':</b> ' . $msg . '</li>';
	}

	
	private function __error() {
		die('INVALID XML STRING');
	}
}

?>