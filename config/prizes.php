<?php

return [
    // When there are 3 winners
    'jackpot3' => env('PRIZE_JACKPOT3', 0.35),
    'first3' => env('PRIZE_FIRST3', 0.25),
    'second3' => env('PRIZE_SECOND3', 0.10),

    // When there are 2 winners
    'jackpot2' => env('PRIZE_JACKPOT2', 0.4),
    'first2' => env('PRIZE_FIRST2', 0.3),
    'second2' => env('PRIZE_SECOND2', 0),

    // When there is 1 winner
    'jackpot1' => env('PRIZE_JACKPOT1', 0.7),
    'second1' => env('PRIZE_SECOND1', 0),
    'first1' => env('PRIZE_FIRST1', 0),

    // Consolation
    'consolation' => env('PRIZE_CONSOLATION', 0.3)

];