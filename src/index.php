<?php

use DynamicsWebApi\Client;
use Ramsey\Uuid\Uuid;

require_once 'vendor/autoload.php';

$solutionFileInput = getenv('SOLUTION_FILE');

if (str_contains($solutionFileInput, '.zip')) {
	if (file_exists($solutionFileInput)) {
		$base64File = base64_encode(file_get_contents($solutionFileInput));
	} else {
		throw new Exception('Sorry, the file does not exist. We received ' . $solutionFileInput);
	}
} else {
	$base64File = getenv('SOLUTION_FILE');
}

echo 'The file we\'re working with is: ' . $base64File . PHP_EOL;
echo 'now checking into it' . PHP_EOL;
$decoded = base64_decode($base64File);
file_put_contents('temp.zip', $decoded);

// 3. Extract the contents of the ZIP file
$zip = new ZipArchive;
if ($zip->open('temp.zip') === TRUE) {
	$extractPath = 'extracted_files';
	mkdir($extractPath);
	$zip->extractTo($extractPath);
	$zip->close();
}

// 4. List the extracted files
$files = scandir($extractPath);
foreach ($files as $file) {
	if (!in_array($file, ['.', '..'])) {
		echo $file . PHP_EOL;
	}
}

// Optional: Cleanup
unlink('temp.zip');

$client = Client::createInstance();
$uuid = Uuid::uuid4();
$guid = $uuid->toString();
$client->request('/ImportSolution', 'POST', [
	'OverwriteUnmanagedCustomizations' => true,
	'CustomizationFile' => $base64File,
	'PublishWorkflows' => true,
	'ImportJobId' => $guid,
]);