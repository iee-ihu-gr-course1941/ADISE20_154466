<?php

function new_game($input) {
  global $mysqli_connection;

  $player1_id = find_players_id_by_token($input['p1']);
  $player2_id = find_players_id_by_token($input['p2']);
  $game_status = 'pending';
  $deck_id = 0;

  // we can check if another game exists before insert

  $sql = 'INSERT INTO game (game_status, player1_id, player2_id, deck_id) VALUES (?, ?, ?, ?)';
  $stmt = $mysqli_connection->prepare($sql);
  $stmt->bind_param('siii', $game_status, $player1_id, $player2_id, $deck_id);
  $stmt->execute();

  $sql1 = 'SELECT * FROM game WHERE player1_id = ? AND player2_id = ?';
  $stmt1 = $mysqli_connection->prepare($sql1);
  $stmt1->bind_param('ss', $player1_id, $player2_id);
  $stmt1->execute();
  $res1 = $stmt1->get_result();
  $game_id = $res1->fetch_row()[0];


  $sql2 = 'INSERT INTO deck (game_id) VALUES (?)';
  $stmt2 = $mysqli_connection->prepare($sql2);
  $stmt2->bind_param('i', $game_id);
  $stmt2->execute();

  $sql3 = 'SELECT * FROM deck WHERE game_id = ?';
  $stmt3 = $mysqli_connection->prepare($sql3);
  $stmt3->bind_param('i', $game_id);
  $stmt3->execute();
  $res3 = $stmt3->get_result();
  $deck_id = $res3->fetch_row()[0];

  $sql4 = 'UPDATE game SET deck_id = ? WHERE id = ?';
	$stmt4 = $mysqli_connection->prepare($sql4);
	$stmt4->bind_param('ii', $deck_id, $game_id);
  $stmt4->execute();

  
  $sql5 = 'INSERT INTO round (deck_id, game_id, deck_card) VALUES (?, ?, ?)';
  $stmt5 = $mysqli_connection->prepare($sql5);
  $deck_card = '';
  $stmt5->bind_param('iis', $deck_id, $game_id, $deck_card);
  $stmt5->execute();

  print json_encode([
    'game_id' => $game_id, 
    'deck_id' => $deck_id,
    'player1_id' => $player1_id,
    'player2_id' => $player2_id,
    'message' => 'New game was set.'
  ]);
}

?>