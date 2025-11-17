<?php

// Read composer.json to get plugin namespace
$composerFile = __DIR__ . '/../composer.json';
$composerData = json_decode(file_get_contents($composerFile), true);

// Get the first PSR-4 namespace (your plugin namespace)
$pluginNamespace = null;
if (isset($composerData['autoload']['psr-4'])) {
	$pluginNamespace = array_key_first($composerData['autoload']['psr-4']);
	$pluginNamespace = rtrim($pluginNamespace, '\\');
}

if (!$pluginNamespace) {
	die("Error: Could not find PSR-4 namespace in composer.json\n");
}

echo "Using plugin namespace: {$pluginNamespace}\n";

// Get base namespace (e.g., X3P0 from X3P0\Breadcrumbs)
$parts = explode('\\', $pluginNamespace);
$baseNamespace = $parts[0];

echo "Looking for {$baseNamespace} packages to prefix...\n";

$vendorFolder = 'x3p0-dev';
$vendorDir = __DIR__ . '/../vendor';
$outputDir = __DIR__ . '/../packages';

// Clean output directory
if (is_dir($outputDir)) {
	deleteDirectory($outputDir);
}
mkdir($outputDir);

// Only copy YOUR packages (e.g., vendor/x3p0/*)
$yourVendorDir = $vendorDir . '/' . $vendorFolder;

if (!is_dir($yourVendorDir)) {
	die("Error: No packages found in vendor/" . $vendorFolder . "/\n");
}

// Copy only your packages
$targetDir = $outputDir . '/' . $vendorFolder;
copyDirectory($yourVendorDir, $targetDir);

// Process all PHP files in copied packages
$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($outputDir, FilesystemIterator::SKIP_DOTS)
);

foreach ($iterator as $file) {
	if ($file->isFile() && $file->getExtension() === 'php') {
		$contents = file_get_contents($file->getPathname());

		// Replace base namespace with plugin namespace
		// e.g., X3P0\ -> X3P0\Breadcrumbs\ (but not X3P0\Breadcrumbs\)
		$pattern = '/\\b' . preg_quote($baseNamespace, '/') . '\\\\(?!' . preg_quote(substr($pluginNamespace, strlen($baseNamespace) + 1), '/') . ')/';
		$replacement = $pluginNamespace . '\\';

		$contents = preg_replace($pattern, $replacement, $contents);

		file_put_contents($file->getPathname(), $contents);
	}
}

echo "Dependencies prefixed successfully!\n";
echo "Only {$baseNamespace} packages were copied and prefixed.\n";
echo "Run 'composer dump-autoload' to update the autoloader.\n";

function copyDirectory($src, $dst): void
{
	$dir = opendir($src);
	@mkdir($dst, 0755, true);
	while (($file = readdir($dir)) !== false) {
		if ($file != '.' && $file != '..') {
			if (is_dir($src . '/' . $file)) {
				copyDirectory($src . '/' . $file, $dst . '/' . $file);
			} else {
				copy($src . '/' . $file, $dst . '/' . $file);
			}
		}
	}
	closedir($dir);
}

function deleteDirectory($dir): void
{
	if (!is_dir($dir)) return;
	$files = array_diff(scandir($dir), ['.', '..']);
	foreach ($files as $file) {
		$path = $dir . '/' . $file;
		is_dir($path) ? deleteDirectory($path) : unlink($path);
	}
	rmdir($dir);
}
