# ADISE20_154466 | Αβησσυνία card game

## Το παχνίδι

Η Αβησσυνία είναι ένα χαρτοπαίγνιο που παίζουν συνήθως τέσσερεις παίκτες, ο καθένας για λογαριασμό του. 
Δεν αποκλείεται όμως οι παίκτες να σχηματίζουν δύο ομάδες (από δύο παίκτες η καθεμία) ή να παιχθεί από δύο μόνο παίκτες. Σε αυτές τις περιπτώσεις όμως ο παράγοντας τύχη αυξάνεται εις βάρος της ικανότητας του παίκτη. Σκοπός του παιγνιδιού είναι να μαζέψει ο παίκτης όσα περισσότερα φύλλα μπορεί.
Ο αριθμός των φύλλων που θα μαζέψει ισοδυναμεί με τους πόντους που θα κερδίσει στη συγκεκριμένη παρτίδα. Εξαίρεση αποτελούν οι βαλέδες που ισοδυναμούν με τρεις πόντους ο καθένας.

## Πως παίζεται

Χρησιμοποιείται μία τράπουλα των 52 φύλλων και αρχικά μοιράζεται κάθε παίκτης από έξι φύλλα, ενώ αφήνονται στη μέση του τραπεζιού τέσσερα, το ένα επάνω στο άλλο, φτιάχνοντας μία στοίβα (κεντρική στοίβα).
Κάθε παίκτης παίζει με την σειρά, με φορά από δεξιά προς τα αριστερά, από ένα φύλλο που κρατάει στα χέρια του (ξεκινώντας από αυτόν που βρίσκεται δεξιά της «μάνας» - του παίκτη δηλαδή που μοιράζει τα φύλλα και παίζει τελευταίος) το οποίο τοποθετεί επάνω στη στοίβα.

Όταν ξεκινά η παρτίδα ο παίκτης που παίζει πρώτος (π.χ. ο Α) έχει δύο επιλογές:
α) να ρίξει κάποιο από τα φύλλα που έχει στο χέρι του στην κεντρική στοίβα
β) να μαζέψει το φύλλο που βρίσκεται στην κορυφή της κεντρικής στοίβας με κάποιο φύλλο που κρατάει στο χέρι όμοιας αξίας

Και από τη στιγμή που κάποιος παίκτης ξεκίνησε δική του στοίβα, προστίθενται στις επιλογές των παικτών εκτός από τις: α) και β) που προαναφέραμε, και άλλες δύο:
γ) αν κρατάει στο χέρι του φύλλο όμοιο με τα φύλλα που βρίσκονται στην κορυφή της στοίβας ενός άλλου παίκτη, τότε μπορεί να το ρίξει και να πάρει τα φύλλα από την στοίβα του συγκεκριμένου παίκτη και να τα τοποθετήσει στην δική του στοίβα
δ) αν κρατάει στο χέρι του φύλλο που είναι ίδιο με αυτά που έχει επάνω στην δική του στοίβα, μπορεί να ρίξει το φύλλο και να το προσθέσει σε αυτήν

## Project url

[Avissinia](https://users.iee.ihu.gr/~it154466/ADISE20_154466/)


## Περιγραφή API

#### Register a player

```
POST /register/

{
    "username": "{username}",
    "password": "{password}"
}
```

#### Login a player

```
POST /login/

{
    "username": "{username}",
    "password": "{password}"
}
```

#### Check if a user is authorized

```
POST /authorized/

Headers: Authorization
key: Authorization, value: {token}
```

#### Get player 'profile'

```
GET /profile/

Headers: Authorization
key: Authorization, value: {token}
```

#### Set a new game

```
POST /new-game/

{
    "p1": "{token}",
    "p2": "{token}"
}
```

#### Start a new game

```
POST /start-game/

{
    "p1": "{token}",
    "p2": "{token}",
    "game_id": {game_id},
    "deck_id": {deck_id}
}
```

#### Get cards of a player or 'board'

```
GET /get-card/

Query params: game_id: {game_id}, game_id: {deck_id}, deck_status: {"deck_status"}

e.g. /get-cards?game_id={int}&deck_id={int}&deck_status={string}

```

#### Drop a card to the 'board'

```
PUT /get-card/
Headers: Authorization
key: Authorization, value: {token}
{
    "game_id": {game_id},
    "deck_id": {deck_id},
    "deck_card": "{player's hand card}"
}

```

## Περιγραφή DB

### player

| Name | Type  | Description |
| - | - | - |
| `id` | `int(20)` | PK |
| `username` | `varchar(255)` |  |
| `password` | `varchar(255)` |  |
| `token` | `varchar(255)` |  |
| `last_action` | `timestamp` |  |

### deck

| Name | Type  | Description
| - | - | - |
| `id` | `int(20)` | PK |
| `game_id` | `smallint(5)` | FK |

### game

| Name | Type  | Description
| - | - | - |
| `id` | `int(11)` | PK |
| `game_status` | `enum('pending', 'ingame', 'win', 'lose', 'aborted')` |  |
| `player1_id` | `smallint(5)` | FK |
| `player2_id` | `smallint(5)` | FK |
| `deck_id` | `smallint(5)` | FK |

### round

| Name | Type  | Description
| - | - | - |
| `id` | `int(11)` | PK |
| `deck_id` | `smallint(5)` | FK |
| `game_id` | `smallint(5)` | FK |
| `deck_card` | `enum('AS', '2S', '3S', '4S', '5S', '6S', '7S', '8S', '9S', '0S', 'JS', 'QS', 'KS', 'AD', '2D', '3D', '4D', '5D', '6D', '7D', '8D', '9D', '0D', 'JD', 'QD', 'KD','AC', '2C', '3C', '4C', '5C', '6C', '7C', '8C', '9C', '0C', 'JC', 'QC', 'KC','AH', '2H', '3H', '4H', '5H', '6H', '7H', '8H', '9H', '0H', 'JH', 'QH', 'KH')` |  |
| `deck_status` | `enum('deck', 'board', 'board_top', 'p1_hand', 'p2_hand')` |  |
