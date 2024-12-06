<?php
require_once "../config/connection.php";

class FixtureController {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function getFixtures($tournamentVersionId) {
        $stmt = $this->con->prepare("SELECT f.*, ht.name as home_team, at.name as away_team 
                                     FROM fixtures f 
                                     JOIN teams ht ON f.home_team_id = ht.id 
                                     JOIN teams at ON f.away_team_id = at.id 
                                     WHERE f.tournament_version_id = ? 
                                     ORDER BY f.match_date, f.match_time");
        $stmt->bind_param("i", $tournamentVersionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateFixture($fixtureId, $homeTeamScore, $awayTeamScore, $homeTeamScorers, $awayTeamScorers, $yellowCards, $redCards) {
        $stmt = $this->con->prepare("UPDATE fixtures SET home_team_score = ?, away_team_score = ?, home_team_scorers = ?, away_team_scorers = ?, yellow_cards = ?, red_cards = ?, status = 'Completed' WHERE id = ?");
        $stmt->bind_param("iissssi", $homeTeamScore, $awayTeamScore, $homeTeamScorers, $awayTeamScorers, $yellowCards, $redCards, $fixtureId);
        return $stmt->execute();
    }

    public function getFixtureDetails($fixtureId) {
        $stmt = $this->con->prepare("SELECT tournament_version_id, home_team_id, away_team_id FROM fixtures WHERE id = ?");
        $stmt->bind_param("i", $fixtureId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
}

