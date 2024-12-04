<?php

$input = fopen('input.txt', 'r');

$grid = [];

// Load the grid from input file
while (!feof($input)) {
    $line = fgets($input);
    $grid[] = str_split(trim($line));
}

fclose($input);

// Variables for counts
$wordSearch = 'XMAS'; // Word to search in Part 1
$directions = [
    [0, 1],   // Horizontal right ➡️
    [0, -1],  // Horizontal left ⬅️
    [1, 0],   // Vertical down ⬇️
    [-1, 0],  // Vertical up ⬆️
    [1, 1],   // Diagonal ↘️
    [-1, 1],  // Diagonal ↗️
    [1, -1],  // Diagonal ↙️
    [-1, -1]  // Diagonal ↖️
];

$wordCount = 0;
$patternCount = 0;

// Function to search for a word in a specific direction
function findWord($grid, $word, $startRow, $startCol, $direction) {
    $rows = count($grid);
    $cols = count($grid[0]);
    $wordLength = strlen($word);
    $dRow = $direction[0];
    $dCol = $direction[1];

    for ($i = 0; $i < $wordLength; $i++) {
        $row = $startRow + $i * $dRow;
        $col = $startCol + $i * $dCol;

        // Check bounds and match
        if ($row < 0 || $row >= $rows || $col < 0 || $col >= $cols || $grid[$row][$col] !== $word[$i]) {
            return false;
        }
    }

    return true;
}

// Part 1: Word Search in all 8 directions
for ($i = 0; $i < count($grid); $i++) {
    for ($j = 0; $j < count($grid[$i]); $j++) {
        // Only start searching if the cell matches the first letter of the word
        if ($grid[$i][$j] === $wordSearch[0]) {
            foreach ($directions as $direction) {
                if (findWord($grid, $wordSearch, $i, $j, $direction)) {
                    $wordCount++;
                }
            }
        }
    }
}

// Part 2: Specific X-MAS/SAM-X Pattern Matching
for ($i = 0; $i < count($grid); $i++) {
    for ($j = 0; $j < count($grid[$i]); $j++) {
        // Check for patterns only if positions in the grid exist
        if (isset(
            $grid[$i][$j + 2],        // Top right
            $grid[$i + 1][$j + 1],    // Middle
            $grid[$i + 2][$j],        // Bottom left
            $grid[$i + 2][$j + 2]     // Bottom right
        )) {
            if ($grid[$i][$j] === 'M') {
                // M.S | M.M
                // .A. | .A.
                // M.S | S.S
                if (
                    ($grid[$i][$j + 2] === 'S' && $grid[$i + 1][$j + 1] === 'A' &&
                     $grid[$i + 2][$j] === 'M' && $grid[$i + 2][$j + 2] === 'S') ||
                    ($grid[$i][$j + 2] === 'M' && $grid[$i + 1][$j + 1] === 'A' &&
                     $grid[$i + 2][$j] === 'S' && $grid[$i + 2][$j + 2] === 'S')
                ) {
                    $patternCount++;
                }
            } elseif ($grid[$i][$j] === 'S') {
                // S.S | S.M
                // .A. | .A.
                // M.M | S.M
                if (
                    ($grid[$i][$j + 2] === 'S' && $grid[$i + 1][$j + 1] === 'A' &&
                     $grid[$i + 2][$j] === 'M' && $grid[$i + 2][$j + 2] === 'M') ||
                    ($grid[$i][$j + 2] === 'M' && $grid[$i + 1][$j + 1] === 'A' &&
                     $grid[$i + 2][$j] === 'S' && $grid[$i + 2][$j + 2] === 'M')
                ) {
                    $patternCount++;
                }
            }
        }
    }
}

echo "Part 1: Word Search - '$wordSearch' found $wordCount times.\n";
echo "Part 2: Pattern Match - Found $patternCount X-MAS/SAM-X patterns.\n";