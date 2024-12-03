<?php

/**
 * Extract and calculate the total for all mul() operations.
 *
 * @param string $sFileContent The input file content.
 * @return int Total result of all mul() operations.
 */
function calculatePart1(string $sFileContent): int
{
    preg_match_all('/mul\(\d+,\d+\)/', $sFileContent, $aMatches);

    return array_reduce(
        $aMatches[0],
        function ($iCarry, $sMatch) {
            $aNumbers = extractNumbersFromMul($sMatch);
            return $iCarry + ($aNumbers[0] * $aNumbers[1]);
        },
        0
    );
}

/**
 * Extract, handle instructions, and calculate the total for enabled mul() operations.
 *
 * @param string $sFileContent The input file content.
 * @return int Total result of enabled mul() operations based on do()/don't() instructions.
 */
function calculatePart2(string $sFileContent): int
{
    preg_match_all('/mul\(\d+,\d+\)|do\(\)|don\'t\(\)/', $sFileContent, $aMatches);
    $aInstructions = $aMatches[0];

    $bMulEnabled = true;

    return array_reduce(
        $aInstructions,
        function ($iCarry, $sInstruction) use (&$bMulEnabled) {
            if ($sInstruction === 'do()') {
                $bMulEnabled = true;
            } elseif ($sInstruction === "don't()") {
                $bMulEnabled = false;
            } elseif (strpos($sInstruction, 'mul(') === 0 && $bMulEnabled) {
                $aNumbers = extractNumbersFromMul($sInstruction);
                return $iCarry + ($aNumbers[0] * $aNumbers[1]);
            }
            return $iCarry;
        },
        0
    );
}

/**
 * Extract numbers from a mul() instruction.
 *
 * @param string $sMatch The mul() instruction.
 * @return array<int> Array of two numbers extracted from the instruction.
 */
function extractNumbersFromMul(string $sMatch): array
{
    return explode(',', str_replace(['mul(', ')'], '', $sMatch));
}

$sFileContent = file_get_contents('day3.txt');

echo 'Part 1: ' . calculatePart1($sFileContent) . PHP_EOL;
echo 'Part 2: ' . calculatePart2($sFileContent) . PHP_EOL;
