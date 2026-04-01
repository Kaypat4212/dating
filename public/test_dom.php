<?php
function testHTML($label, $html) {
    $dom = new DOMDocument();
    @$dom->loadHTML($html);
    $body = $dom->getElementsByTagName('body')->item(0);
    $count = 0;
    echo "\n=== $label ===\n";
    foreach ($body->childNodes as $child) {
        if ($child->nodeType == XML_ELEMENT_NODE) {
            if ($child->tagName === 'script') continue;
            $count++;
            echo 'BODY CHILD: ' . $child->tagName . ' class=' . $child->getAttribute('class') . "\n";
        }
    }
    echo "Body child count: $count\n";
}

// Test 1: link inside nested div
testHTML('link inside nested div', '<div class="fi-page"><div><link rel="stylesheet" href="test.css"><p>hello</p></div></div>');

// Test 2: style inside nested div
testHTML('style inside nested div', '<div class="fi-page"><div><style>body{color:red}</style><p>hello</p></div></div>');

// Test 3: style with body selector at top-level inside outer div
testHTML('style with body selector', '<div class="fi-page"><style>body{background:#0f0a1a}</style><div><p>hello</p></div></div>');

// Test 4: link at top-level inside outer div
testHTML('link at top-level inside outer div', '<div class="fi-page"><link rel="stylesheet" href="test.css"><div><p>hello</p></div></div>');

// Test 5: simulate actual activity-log structure  
testHTML('simulate activity-log', '<div class="fi-page"><div><link href="bootstrap.css" rel="stylesheet"><link rel="stylesheet" href="icons.css"><style>:root{--x:1}body{background:#000}</style><div id="toast-container"></div><div class="container-fluid"></div></div></div>');
