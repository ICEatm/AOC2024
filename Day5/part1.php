<?php

function parseInput($lines) {
    // Find the separator between rules and updates
    $rulesEnd = array_search('', $lines);
    $rules = array_slice($lines, 0, $rulesEnd);
    $updates = array_slice($lines, $rulesEnd + 1);
    
    // Create graph of ordering constraints
    $graph = [];
    foreach ($rules as $rule) {
        $parts = explode('|', $rule);
        $before = intval($parts[0]);
        $after = intval($parts[1]);
        
        if (!isset($graph[$before])) {
            $graph[$before] = [];
        }
        $graph[$before][] = $after;
    }
    
    return [$graph, $updates];
}

function isValidOrder($update, $graph) {
    // Convert update to integers
    $pages = array_map('intval', explode(',', $update));
    $pageSet = array_flip($pages);
    
    // Check each page's positioning against graph constraints
    foreach ($pages as $i => $page) {
        if (isset($graph[$page])) {
            foreach ($graph[$page] as $afterPage) {
                // If the constrained page exists in the update, it must be after the current page
                if (isset($pageSet[$afterPage])) {
                    $afterIndex = array_search($afterPage, $pages);
                    if ($afterIndex <= $i) {
                        return false;
                    }
                }
            }
        }
    }
    
    return true;
}

function solve($filename) {
    $lines = array_map('trim', file($filename, FILE_IGNORE_NEW_LINES));
    list($graph, $updates) = parseInput($lines);
    
    $totalMiddlePages = 0;
    foreach ($updates as $update) {
        if (isValidOrder($update, $graph)) {
            $pages = array_map('intval', explode(',', $update));
            $middleIndex = floor(count($pages) / 2);
            $totalMiddlePages += $pages[$middleIndex];
        }
    }
    
    return $totalMiddlePages;
}

echo solve("day5.txt") . PHP_EOL;
