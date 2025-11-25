<?php

$file = file_get_contents('resources/views/home.blade.php');
$lines = explode("\n", $file);
$stack = [];

foreach ($lines as $num => $line) {
    $lineNum = $num + 1;
    
    // Check for @if (but not @endif on same line)
    if (preg_match('/@if\s*\(/', $line) && strpos($line, '@endif') === false) {
        $stack[] = ['type' => 'if', 'line' => $lineNum, 'content' => trim($line)];
    }
    
    // Check for @foreach
    if (preg_match('/@foreach\s*\(/', $line)) {
        $stack[] = ['type' => 'foreach', 'line' => $lineNum, 'content' => trim($line)];
    }
    
    // Check for @endif
    if (preg_match('/@endif/', $line)) {
        if (empty($stack)) {
            echo "❌ ERROR: @endif without opening at line $lineNum\n";
        } else {
            $last = array_pop($stack);
            if ($last['type'] !== 'if') {
                echo "❌ ERROR: @endif closes @{$last['type']} at line $lineNum (opened at {$last['line']})\n";
            }
        }
    }
    
    // Check for @endforeach
    if (preg_match('/@endforeach/', $line)) {
        if (empty($stack)) {
            echo "❌ ERROR: @endforeach without opening at line $lineNum\n";
        } else {
            $last = array_pop($stack);
            if ($last['type'] !== 'foreach') {
                echo "❌ ERROR: @endforeach closes @{$last['type']} at line $lineNum (opened at {$last['line']})\n";
            }
        }
    }
}

echo "\n=== UNCLOSED DIRECTIVES ===\n";
if (empty($stack)) {
    echo "✅ None found - all directives are properly closed!\n";
} else {
    foreach ($stack as $item) {
        echo "❌ Line {$item['line']}: Unclosed @{$item['type']}\n";
        echo "   Content: {$item['content']}\n\n";
    }
}

echo "\n=== SUMMARY ===\n";
echo "Total @if: " . substr_count($file, '@if') . "\n";
echo "Total @endif: " . substr_count($file, '@endif') . "\n";
echo "Total @foreach: " . substr_count($file, '@foreach') . "\n";
echo "Total @endforeach: " . substr_count($file, '@endforeach') . "\n";

