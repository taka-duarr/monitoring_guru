<?php
require __DIR__ . '/vendor/autoload.php';
$ref = new ReflectionClass('Filament\Forms\Components\TextInput');
echo "TextInput extends: " . $ref->getParentClass()->getName() . "\n";
$p = $ref->getParentClass();
while ($p = $p->getParentClass()) {
    echo "  extends: " . $p->getName() . "\n";
}
