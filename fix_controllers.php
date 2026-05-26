<?php
$ctrlDir = __DIR__ . '/app/Http/Controllers/';
$files = glob($ctrlDir . '*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    // Only fix if the file has a create method
    if (strpos($content, 'public function create()') !== false) {
        // If it's compact('data') without any relations
        $content = preg_replace("/return view\('admin\.([a-z_]+)_form', compact\('data'\)\);/", "return view('admin.$1_form');", $content);
        
        // If it has relations, e.g., compact('data', 'jurusans')
        // We shouldn't match this with the above regex.
        // What if it's compact('data', 'jurusans')? 
        // The generator actually generated compact('data', 'jurusans') for those? Let's check the generator.
        // Generator did: compactStr was "'data', 'jurusans'".
        // str_replace("'data', ", "", "'data', 'jurusans'") -> "'jurusans'"
        // So for models WITH relations, it generated: compact('jurusans') which is CORRECT!
        // The bug ONLY affects models WITHOUT relations, where compactStr was just "'data'".
        // So the above regex is perfect.
        file_put_contents($file, $content);
    }
}
echo "Controllers fixed!";
