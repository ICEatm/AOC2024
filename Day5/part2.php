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

function isValidOrder($pages, $graph) {
    // Convert pages to integers if not already
    $pages = is_string($pages) ? array_map('intval', explode(',', $pages)) : $pages;
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

function correctOrder($pages, $graph) {
    $pages = is_string($pages) ? array_map('intval', explode(',', $pages)) : $pages;
    
    // Topological sort-like approach
    $inDegree = array_fill_keys($pages, 0);
    $adjacencyList = [];
    
    // Build constraint graph for this specific update
    foreach ($pages as $i => $page) {
        if (isset($graph[$page])) {
            foreach ($graph[$page] as $afterPage) {
                if (in_array($afterPage, $pages)) {
                    $adjacencyList[$page][] = $afterPage;
                    $inDegree[$afterPage]++;
                }
            }
        }
    }
    
    // Find initial nodes with no incoming edges
    $queue = [];
    foreach ($pages as $page) {
        if ($inDegree[$page] == 0) {
            $queue[] = $page;
        }
    }
    
    $result = [];
    while (!empty($queue)) {
        // Sort queue to maintain relative order for pages with 0 in-degree
        sort($queue);
        $current = array_shift($queue);
        $result[] = $current;
        
        // Remove current node and update in-degrees
        if (isset($adjacencyList[$current])) {
            foreach ($adjacencyList[$current] as $neighbor) {
                $inDegree[$neighbor]--;
                if ($inDegree[$neighbor] == 0) {
                    $queue[] = $neighbor;
                }
            }
        }
    }
    
    // If not all nodes processed, fallback to original order
    return count($result) == count($pages) ? $result : $pages;
}

function solve($filename) {
    $lines = array_map('trim', file($filename, FILE_IGNORE_NEW_LINES));
    list($graph, $updates) = parseInput($lines);
    
    // Calculate middle pages of incorrectly-ordered updates
    $totalMiddlePages = 0;
    foreach ($updates as $update) {
        $pages = array_map('intval', explode(',', $update));
        
        if (!isValidOrder($pages, $graph)) {
            $correctedPages = correctOrder($pages, $graph);
            $middleIndex = floor(count($correctedPages) / 2);
            $totalMiddlePages += $correctedPages[$middleIndex];
        }
    }
    
    return $totalMiddlePages;
}

echo solve("day5.txt") . PHP_EOL;
