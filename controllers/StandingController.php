<?php
require_once "../config/connection.php";

class StandingController {
    private $con;

    public function __construct($con) {
        $this->con = $con;
    }

    public function getStandings($tournamentVersionId) {
        $stmt = $this->con->prepare("SELECT s.*, t.name as team_name 
                                     FROM standings s 
                                     JOIN teams t ON s.team_id = t.id 
                                     WHERE s.tournament_version_id = ?
                                     ORDER BY s.points DESC, s.goals_for - s.goals_against DESC");
        $stmt->bind_param("i", $tournamentVersionId);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function updateStandings($tournamentVersionId, $teamId, $goalsFor, $goalsAgainst) {
        $stmt = $this->con->prepare("SELECT * FROM standings WHERE tournament_version_id = ? AND team_id = ?");
        $stmt->bind_param("ii", $tournamentVersionId, $teamId);
        $stmt->execute();
        $result = $stmt->get_result();
        $standing = $result->fetch_assoc();

        $played = $standing['played'] + 1;
        $won = $standing['won'];
        $drawn = $standing['drawn'];
        $lost = $standing['lost'];
        $points = $standing['points'];

        if ($goalsFor > $goalsAgainst) {
            $won++;
            $points += 3;
        } elseif ($goalsFor == $goalsAgainst) {
            $drawn++;
            $points += 1;
        } else {
            $lost++;
        }

        $goals_for = $standing['goals_for'] + $goalsFor;
        $goals_against = $standing['goals_against'] + $goalsAgainst;

        $stmt = $this->con->prepare("UPDATE standings SET played = ?, won = ?, drawn = ?, lost = ?, goals_for = ?, goals_against = ?, points = ? WHERE tournament_version_id = ? AND team_id = ?");
        $stmt->bind_param("iiiiiiiii", $played, $won, $drawn, $lost, $goals_for, $goals_against, $points, $tournamentVersionId, $teamId);
        return $stmt->execute();
    }
}

