<?php
$ctrlDir = __DIR__ . '/app/Http/Controllers/';
$files = glob($ctrlDir . '*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Find edit method and check if it lacks compact
    if (preg_match('/public function edit\([^)]+\)\s*\{[^}]*return view\(\'admin\.([a-z_]+)_form\'\);/s', $content)) {
        // It's missing compact('data') because of my previous bad regex.
        $content = preg_replace(
            '/public function edit\(([^)]+)\)\s*\{\s*\$data = [^;]+;\s*return view\(\'admin\.([a-z_]+)_form\'\);/s',
            'public function edit($1)
    {
        $data = \\App\\Models\\$2::findOrFail($1);
        return view(\'admin.$2_form\', compact(\'data\'));',
            $content
        );
        // Wait, the Model name is uppercase (e.g. Guru). 
        // A safer way is just to replace `return view('admin.xxx_form');` inside edit with `return view('admin.xxx_form', compact('data'));`
        // Since I only broke the ones that had NO relations (where it was exactly `return view('admin.xxx_form');`)
        
        // Let's do a more precise replace:
        // Match public function edit up to its return statement.
    }
}

// Easier script:
foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Replace `return view('admin.xxx_form');` with `return view('admin.xxx_form', compact('data'));` ONLY inside `edit` method.
    // Actually, I can just replace `return view('admin.xxx_form');` if it's preceded by `$data = ` in the method body.
    $parts = explode('public function edit', $content);
    if (count($parts) > 1) {
        $editPart = $parts[1];
        // If it doesn't have compact('data') but does have $data =
        if (strpos($editPart, 'compact(') === false && preg_match("/return view\('admin\.([a-z_]+)_form'\);/", $editPart, $matches)) {
            $viewStr = $matches[0]; // return view('admin.xxx_form');
            $newViewStr = str_replace("');", "', compact('data'));", $viewStr);
            $parts[1] = str_replace($viewStr, $newViewStr, $editPart);
            $content = implode('public function edit', $parts);
            file_put_contents($file, $content);
            echo "Fixed edit in " . basename($file) . "\n";
        }
    }
}
echo "Done fixing edits!\n";
