var game   = null;
var player = 1;
var turn   = 1;
var lock   = false;

$(document).ready(function() {
    game   = $('#board').data('game');
    turn   = $('#board').data('turn');
    player = $('#board').data('player');
    lock   = !turn;

    /*
     * bind events on board cells to show a ghost in empty cells
     * or show a not valid cell background in not empty cells
     */
    $('table#board td').mouseenter(function() {
      // check cell is not empty
      if (lock || $(this).hasClass('player1') || $(this).hasClass('player2')) {
        return;
      }
      // if cell is not empty add a ghost for this player
      $(this).addClass('ghost').addClass('player'+turn);
    }).mouseout(function() {
        // check cell is empty (have ghost) to remove ghost
        if ($(this).hasClass('ghost'))
          $(this).removeClass('ghost').removeClass('player'+turn);
  });

  /*
   * bind click events for send moves to server
   */
  $('table#board td').click(function() {
    // check player is really on turn
    if (!lock && (player == turn || player == 3)) {
      var bitboard = $(this).data('bitboard');
      console.log('bitboard');
      sendMove(bitboard);
    }
  });
});

/**
 * send move to server to validate and get other player move
 *
 * @param  int bitboard square where move
 */
var sendMove = function(bitboard) {
  console.log('sendMove', game, player, bitboard);
  $.ajax({
    url : '/index.php/game/play/move',
    type : 'post',
    dataType : 'json',
    data : {
      game     : game,
      player   : turn,
      bitboard : bitboard
    },
    beforeSend : function() {
      lock = true;
    },
    success : function(response) {
      console.log(response);
      if (response.result) {
        makeMove(bitboard);

        // if game is against machine server response with machine move too
        if (response.move) {
          lock = true;
          makeMove(response.move);
        }

        // if game is ended update with winner (winner 3 -> draw)
        if (response.winner) {
          gameWinner(response.winner, response.line);
        }
      }
      if (response.alert) {
        showAlert(response.alert);
      }
    },
    error : function(xhr, ajaxOptions, thrownError) {
      console.log(xhr, ajaxOptions, thrownError);
    }
  });
};

/**
 * make move by update board
 *
 * @param  int bitboard bitboard cell to update
 */
var makeMove = function(bitboard) {
  // get square for this bitboard
  var $cell = $('#board').find("[data-bitboard='"+bitboard+"']").first();
  // update square to show player piece and remove ghost
  $cell.addClass('player'+turn).removeClass('ghost');
  // update turn and remove lock
  turn = turn == 2 ? 1 : 2;
  setTurn(turn);
  lock = false;
};

/**
 * game is finished
 * show alerts and change effects
 *
 * @param  inter winner game winner
 *         1 : player1 wins
 *         2 : player2 wins
 *         3 : draw
 */
var gameWinner = function(winner, line) {
    console.log('winner', winner);
    lock = true;
    // show winner alert
    $('#player'+winner+'_wins').show().removeAttr('hidden');
    $('#buttons_game_end').show().removeAttr('hidden');
    // add winner color to winner
    $('#player'+winner+'_info').addClass('winner');
    // remove turn color from player in turn
    $('.player_info.turn').removeClass('turn');

    if (line) {
      $('#line_win').removeAttr('hidden').addClass(line).fadeIn();
    }
};

/**
 * change turn and update players
 *
 * @param  integer player player who get turn
 */
var setTurn = function(player) {
  // remove turn color from player in turn
  $('.player_info.turn').removeClass('turn');
  // set turn to player in tourn
  $('#player'+player+'_info').addClass('turn');
};
