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

  print json_encode([
    'game_id' => $game_id, 
    'deck_id' => $deck_id,
    'player1_id' => $player1_id,
    'player2_id' => $player2_id,
    'message' => 'New game was set.'
  ]);
}

function start_game($input) {
  global $mysqli_connection;

  $game_id = $input['game_id'];
  $game_status = 'ingame';
  $sql = 'UPDATE game SET game_status = ? WHERE id = ?';
	$stmt = $mysqli_connection->prepare($sql);
	$stmt->bind_param('si', $game_status, $game_id);
  $stmt->execute();
  
  // 'S': 'SPADES', 'D': 'DIAMONDS', 'H': 'HEARTS', 'C': 'CLUBS', 
  // '0': '10',
  // 'A': 'ACE', 'J': 'JACK', 'Q': 'QUEEN', 'K': 'KING', 
  $deck = array(
    'AS', '2S', '3S', '4S', '5S', '6S', '7S', '8S', '9S', '0S', 'JS', 'QS', 'KS',
    'AD', '2D', '3D', '4D', '5D', '6D', '7D', '8D', '9D', '0D', 'JD', 'QD', 'KD',
    'AC', '2C', '3C', '4C', '5C', '6C', '7C', '8C', '9C', '0C', 'JC', 'QC', 'KC',
    'AH', '2H', '3H', '4H', '5H', '6H', '7H', '8H', '9H', '0H', 'JH', 'QH', 'KH'
  );
  shuffle($deck);
  $deck_id = $input['deck_id'];
  while ($deck_card = array_pop($deck)) {
    if (count($deck) >= 46) {
      $deck_status1 = 'p1_hand';
      $sql1 = 'INSERT INTO round (deck_id, game_id, deck_card, deck_status) VALUES (?, ?, ?, ?)';
      $stmt1 = $mysqli_connection->prepare($sql1);
      $stmt1->bind_param('iiss', $deck_id, $game_id, $deck_card, $deck_status1);
      $stmt1->execute();
    } else if (count($deck) >= 40) {
      $deck_status2 = 'p2_hand';
      $sql2 = 'INSERT INTO round (deck_id, game_id, deck_card, deck_status) VALUES (?, ?, ?, ?)';
      $stmt2 = $mysqli_connection->prepare($sql2);
      $stmt2->bind_param('iiss', $deck_id, $game_id, $deck_card, $deck_status2);
      $stmt2->execute();
    } else if (count($deck) >= 37) {
      $deck_status3 = 'board';
      $sql3 = 'INSERT INTO round (deck_id, game_id, deck_card, deck_status) VALUES (?, ?, ?, ?)';
      $stmt3 = $mysqli_connection->prepare($sql3);
      $stmt3->bind_param('iiss', $deck_id, $game_id, $deck_card, $deck_status3);
      $stmt3->execute();
    } else if (count($deck) >= 36 ) {
      $deck_status4 = 'board_top';
      $sql4 = 'INSERT INTO round (deck_id, game_id, deck_card, deck_status) VALUES (?, ?, ?, ?)';
      $stmt4 = $mysqli_connection->prepare($sql4);
      $stmt4->bind_param('iiss', $deck_id, $game_id, $deck_card, $deck_status4);
      $stmt4->execute();
    } else {
      $deck_status5 = 'deck';
      $sql5 = 'INSERT INTO round (deck_id, game_id, deck_card, deck_status) VALUES (?, ?, ?, ?)';
      $stmt5 = $mysqli_connection->prepare($sql5);
      $stmt5->bind_param('iiss', $deck_id, $game_id, $deck_card, $deck_status5);
      $stmt5->execute();
    }
  }
}

?>