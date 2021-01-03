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
  deck_card varchar(10) DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;