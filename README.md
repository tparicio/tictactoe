# Tic Tac Toe
## by Toni Paricio

Tic Tac Toe is a classical game for two players, who take turns marking the
squares in a 3x3 grid board with circles and crosses (O and X). The game ends
when some player get 3 in a line or all squares are occupied, in this case game
end at draw.

This game was developed with Symfony 3.4 as framework with PHP 7.0,
MariaDB 10.1.23 and Apache 2.4.25 in a Raspberry (ARM) with Raspbian 9. As IDE was used Atom 1.16.

### Game Modes
This game include two game modes:
* One vs One
* One vs Machines

#### One vs One
Two users playing by turns in the same computer.

#### One vs Machine
One player against the computer.

### Bitboards
Game logic uses bitboards to manage moves and include a simple engine for find
the machine moves.
A bitboard is a data structure for represent a board where each square is
represented by one bit, in this case bitboard will use 9 bits (3x3). Bit
operations (and, or, xor, shift, ...) are simple and quick.

For example, suposse we have a board like this:
```
X 0 ·  
· 0 ·  
X · 0  
```

A bitboard for circle pieces will be:
```
0b010010001 = 145
```

and for crosses:  
```
0b100000100 = 260
```

And a simple AND operation will return a new bitboard with all pieces in board:
```
145 & 260 = 405 = 0b110010101
```

A class was created in `src/Utils/Bitboard.php` for handle bitboard operations. All bitboard value will be converted into Bitboard class.

The bitboards will be stored in the database as integers.

### Rematch
After finish a game, in any mode, players can start again by click on rematch
button. Rematch will start a new game changing the opener.

### Stats
The stats section show game statistics.

