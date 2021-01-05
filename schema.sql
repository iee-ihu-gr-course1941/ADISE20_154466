DROP TABLE IF EXISTS player;
CREATE TABLE player (
  id INT NOT NULL AUTO_INCREMENT,
  username varchar(20) DEFAULT NULL,
  password varchar(255) DEFAULT NULL,
  token varchar(255) DEFAULT NULL,
  last_action timestamp NULL DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS deck;
CREATE TABLE deck (
  id INT NOT NULL AUTO_INCREMENT,
  game_id SMALLINT UNSIGNED NOT NULL REFERENCES player(id),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS game;
CREATE TABLE game (
  id INT NOT NULL AUTO_INCREMENT,
  game_status ENUM ('pending', 'ingame', 'win','lose', 'aborted'),  
  player1_id SMALLINT UNSIGNED NOT NULL REFERENCES player(id),
  player2_id SMALLINT UNSIGNED NOT NULL REFERENCES player(id),
  deck_id SMALLINT UNSIGNED NOT NULL REFERENCES deck(id),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS round;
CREATE TABLE round (
  id INT NOT NULL AUTO_INCREMENT,
  deck_id SMALLINT UNSIGNED NOT NULL REFERENCES deck(id),
  game_id SMALLINT UNSIGNED NOT NULL REFERENCES deck(id),
  deck_card ENUM (
    'AS', '2S', '3S', '4S', '5S', '6S', '7S', '8S', '9S', '0S', 'JS', 'QS', 'KS',
    'AD', '2D', '3D', '4D', '5D', '6D', '7D', '8D', '9D', '0D', 'JD', 'QD', 'KD',
    'AC', '2C', '3C', '4C', '5C', '6C', '7C', '8C', '9C', '0C', 'JC', 'QC', 'KC',
    'AH', '2H', '3H', '4H', '5H', '6H', '7H', '8H', '9H', '0H', 'JH', 'QH', 'KH'
  ),
  deck_status ENUM ('deck', 'board_top', 'board', 'p1_hand', 'p2_hand', 'p1_stack', 'p2_stack'),
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;