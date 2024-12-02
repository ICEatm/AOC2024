<?php

$aFileContent = file('./input.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
$aNumbers = array_map(function ($sLine) { return explode('   ', $sLine); }, $aFileContent);

$aFirstNumbers = array_column($aNumbers, 0);
$aSecondNumbers = array_column($aNumbers, 1);

/** Part 1: Calculate total distance */
sort($aFirstNumbers);
sort($aSecondNumbers);

$iTotalDistance = array_reduce(range(0, min(count($aFirstNumbers), count($aSecondNumbers)) - 1), function ($oCarry, $iNumber) use ($aFirstNumbers, $aSecondNumbers) {
    return $oCarry + abs($aFirstNumbers[$iNumber] - $aSecondNumbers[$iNumber]);
}, 0);

/** Part 2: Calculate Similarity Score */
$aSecondNumberOccurrences = array_count_values($aSecondNumbers);
$iSimilarityScore = array_reduce($aFirstNumbers, function ($oCarry, $iNumber) use ($aSecondNumberOccurrences) {
    return $oCarry + ($iNumber * ($aSecondNumberOccurrences[$iNumber] ?? 0));
}, 0);

/** Finished */
echo "Part 1 - Total Distance: $iTotalDistance" . PHP_EOL;
echo "Part 2 - Similarity Score: $iSimilarityScore";