<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

require_once "../config/connection.php";
require_once "FixtureController.php";
require_once "StandingController.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fixtureId = $_POST['fixtureId'];
    $homeTeamScore = $_POST['homeTeamScore'];
    $awayTeamScore = $_POST['awayTeamScore'];
    $homeTeamScorers = $_POST['homeTeamScorers'];
    $awayTeamScorers = $_POST['awayTeamScorers'];
    $yellowCards = $_POST['yellowCards'];
    $redCards = $_POST['redCards'];

    $fixtureController = new FixtureController($con);
    $standingController = new StandingController($con);

    // Start transaction
    $con->begin_transaction();

    try {
        // Update fixture
        $fixtureController->updateFixture($fixtureId, $homeTeamScore, $awayTeamScore, $homeTeamScorers, $awayTeamScorers, $yellowCards, $redCards);

        // Get fixture details
        $fixtureDetails = $fixtureController->getFixtureDetails($fixtureId);

        // Update standings
        $standingController->updateStandings($fixtureDetails['tournament_version_id'], $fixtureDetails['home_team_id'], $homeTeamScore, $awayTeamScore);
        $standingController->updateStandings($fixtureDetails['tournament_version_id'], $fixtureDetails['away_team_id'], $awayTeamScore, $homeTeamScore);

        $con->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $con->rollback();
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}

