<?php

$aFileContent = file('./input_day2.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

function isSafeWithRemoval(string $sReport): bool  {
    $aLevels = array_map('intval', explode(' ', trim($sReport)));

    if (isSafe($aLevels)) {
        return true;
    }

    // Check if removing a single item marks the report as safe
    for ($i = 0; $i < count($aLevels); $i++) {
        $aModifiedLevels = $aLevels;
        unset($aModifiedLevels[$i]);

        $aModifiedLevels = array_values($aModifiedLevels);
        if (isSafe($aModifiedLevels)) {
            return true;
        }
    }

    return false;
}

function isSafe(array $aLevels): bool {
    $aDiffs = [];

    // Calculate  differences between adjacent levels
    for ($i = 0; $i < count($aLevels) - 1; $i++) {
        $aDiffs[] = abs($aLevels[$i] - $aLevels[$i + 1]);
    }

    // Check if all differences are between 1 and 3
    $aValidDifferences = array_reduce($aDiffs, function ($oCarry, $iDiff) {
        return $oCarry && ($iDiff >= 1 && $iDiff <= 3);
    }, true);

    // Check if the levels are increasing or decreasing
    $bAllIncreasing = true;
    $bAllDecreasing = true;

    for ($i = 0; $i < count($aLevels) - 1; $i++) {
        if ($aLevels[$i] >= $aLevels[$i + 1]) {
            $bAllIncreasing = false;
        }

        if ($aLevels[$i] <= $aLevels[$i + 1]) {
            $bAllDecreasing = false;
        }
    }

    // Report is valid if both conditions are met
    return ($bAllIncreasing || $bAllDecreasing) && $aValidDifferences;
}

$iSafeCount = 0;
foreach ($aFileContent as $sLine) {
    if (isSafeWithRemoval($sLine)) {
        $iSafeCount++;
    }
}

// Output valid reports
echo "Day 2 - Total valid reports: $iSafeCount" . PHP_EOL;