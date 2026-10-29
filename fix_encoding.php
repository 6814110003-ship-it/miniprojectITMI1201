<?php
$dir = "resources/views";
$rii = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$fixed = 0;
foreach ($rii as $file) {
    if ($file->isDir()) continue;
    $path = $file->getPathname();
    if (substr($path, -5) !== ".blade.php") continue;
    $content = file_get_contents($path);
    // detect double-encoding marker (Ã or Â pattern typical of double-encoded UTF-8)
    if (strpos($content, "Ã") !== false || strpos($content, "Â¸") !== false || strpos($content, "Ã Â¸") !== false) {
        // undo layer 2: UTF-8 -> ISO-8859-1
        $c1 = mb_convert_encoding($content, "ISO-8859-1", "UTF-8");
        // undo layer 1: UTF-8 -> ISO-8859-1 again
        $c2 = mb_convert_encoding($c1, "ISO-8859-1", "UTF-8");
        file_put_contents($path, $c2);
        echo "FIXED: $path\n";
        $fixed++;
    }
}
echo "Total fixed: $fixed\n";

