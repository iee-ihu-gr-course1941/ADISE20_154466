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

  header('Content-type: application/json');
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
  
  // 'S': 'SPADES' = Μπαστούνια (♠), 
  // 'D': 'DIAMONDS' = Καρά (♦), 
  // 'H': 'HEARTS' = Καρδιές (♥), 
  // 'C': 'CLUBS' = Τριφύλλια (♣), 
  // 'A': 'ACE' = Άσσος, 
  // 'J': 'JACK' = Βαλές, 
  // 'Q': 'QUEEN' = Ντάμα, 
  // 'K': 'KING' = Ρήγας/Παπάς, 
  // '0': '10' 
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

function game_status() {
  global $mysqli_connection;

  $game_id = $_GET['game_id'];
  $deck_id = $_GET['deck_id'];

  $sql = 'SELECT * FROM game WHERE id = ? AND deck_id = ?';
  $stmt = $mysqli_connection->prepare($sql);
  $stmt->bind_param('ii', $game_id, $deck_id);
  $stmt->execute();
  $res = $stmt->get_result();
  $game_status = $res->fetch_row()['1'];
  header('Content-type: application/json');
  print json_encode([
    'game_status' => $game_status
  ], JSON_PRETTY_PRINT);
} 

function get_cards() {
  global $mysqli_connection;

  $game_id = $_GET['game_id'];
  $deck_id = $_GET['deck_id'];
  $deck_status = $_GET['deck_status'];
  $sql = 'SELECT * FROM round WHERE deck_id = ? AND game_id = ? AND deck_status = ?';
  $stmt = $mysqli_connection->prepare($sql);
  $stmt->bind_param('iis', $deck_id, $game_id, $deck_status);
  $stmt->execute();
  $res = $stmt->get_result();

  header('Content-type: application/json');
  while ($row = mysqli_fetch_array($res, MYSQLI_ASSOC)) {
    print json_encode([
      'card' => $row['deck_card']
    ], JSON_PRETTY_PRINT);
  }
}

function drop_card($input, $headers) {
  global $mysqli_connection;
  $game_id = $input['game_id'];
  $deck_id = $input['deck_id'];
  $deck_card = $input['deck_card'];
  $token = $headers['Authorization'];

  // fetch current board top card
  $deck_status = 'board_top';
  $sql = 'SELECT * FROM round WHERE deck_id = ? AND game_id = ? AND deck_status = ?';
	$stmt = $mysqli_connection->prepare($sql);
	$stmt->bind_param('iis', $deck_id, $game_id, $deck_status);
  $stmt->execute();
  $res = $stmt->get_result();
  $current_board_top_card = $res->fetch_row()['3'];

  // update deck_status for old board_top card
  $deck_status1 = 'board';
  $sql1 = 'UPDATE round SET deck_status = ? WHERE deck_id = ? AND game_id = ? AND deck_card = ?';
	$stmt1 = $mysqli_connection->prepare($sql1);
	$stmt1->bind_param('siis', $deck_status1, $deck_id, $game_id, $current_board_top_card);
  $stmt1->execute();

  // update deck_status to board_top for dropped card
  $sql2 = 'UPDATE round SET deck_status = ? WHERE deck_id = ? AND game_id = ? AND deck_card = ?';
	$stmt2 = $mysqli_connection->prepare($sql2);
	$stmt2->bind_param('siis', $deck_status, $deck_id, $game_id, $deck_card);
  $stmt2->execute();

  // update last_action for player
  $sql3 = 'UPDATE player SET last_action = now() WHERE token = ?';
  $stmt3 = $mysqli_connection->prepare($sql3);
	$stmt3->bind_param('s',  $token);
  $stmt3->execute();
}

function pick_card($input, $headers) {
  global $mysqli_connection;
  $game_id = $input['game_id'];
  $deck_id = $input['deck_id'];
  $deck_card = $input['deck_card'];
  $token = $headers['Authorization'];

  
  // fetch current board top card
  $deck_status = 'board_top';
  $sql = 'SELECT * FROM round WHERE deck_id = ? AND game_id = ? AND deck_status = ?';
  $stmt = $mysqli_connection->prepare($sql);
  $stmt->bind_param('iis', $deck_id, $game_id, $deck_status);
  $stmt->execute();
  $res = $stmt->get_result();
  $current_board_top_card = $res->fetch_row()['3'];

  // check if cards have same number
  $number_of_current_board_top_card = preg_replace('/[^0-9]/', '', $current_board_top_card);
  $number_of_deck_card = preg_replace('/[^0-9]/', '', $deck_card);
  if ($number_of_deck_card == $number_of_current_board_top_card) {
    // update board_top card deck_status to p{1,2}_stack
    $deck_status1 = 'p1_stack';
    $sql1 = 'UPDATE round SET deck_status = ? WHERE deck_id = ? AND game_id = ? AND deck_card = ?';
    $stmt1 = $mysqli_connection->prepare($sql1);
    $stmt1->bind_param('siis', $deck_status1, $deck_id, $game_id, $current_board_top_card);
    $stmt1->execute();

    // update deck_status to p_stack for 'dropped' card
    $deck_status2 = 'p1_stack';
    $sql2 = 'UPDATE round SET deck_status = ? WHERE deck_id = ? AND game_id = ? AND deck_card = ?';
    $stmt2 = $mysqli_connection->prepare($sql2);
    $stmt2->bind_param('siis', $deck_status2, $deck_id, $game_id, $deck_card);
    $stmt2->execute();

    // update last_action for player
    $sql3 = 'UPDATE player SET last_action = now() WHERE token = ?';
    $stmt3 = $mysqli_connection->prepare($sql3);
    $stmt3->bind_param('s',  $token);
    $stmt3->execute();

    // fetch random from board to set new board_top
    $deck_status4 = 'board';
    $sql4 = 'SELECT * FROM round WHERE deck_id = ? AND game_id = ? AND deck_status = ?';
    $stmt4 = $mysqli_connection->prepare($sql4);
    $stmt4->bind_param('iis', $deck_id, $game_id, $deck_status4);
    $stmt4->execute();
    $res4 = $stmt4->get_result();
    $random_board_top_card = $res4->fetch_row()['3']; // I should have column for board_tops in db

    // update board_top
    $deck_status5 = 'board_top';
    $sql5 = 'UPDATE round SET deck_status = ? WHERE deck_id = ? AND game_id = ? AND deck_card = ?';
    $stmt5 = $mysqli_connection->prepare($sql5);
    $stmt5->bind_param('siis', $deck_status5, $deck_id, $game_id, $random_board_top_card);
    $stmt5->execute();

    $sql6 = 'UPDATE player SET last_action = now() WHERE token = ?';
    $stmt6 = $mysqli_connection->prepare($sql6);
    $stmt6->bind_param('s',  $token);
    $stmt6->execute();
  } else {
    header('HTTP/1.1 400 Bad Request');
  }
}

?>