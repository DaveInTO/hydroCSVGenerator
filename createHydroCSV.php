<?php
require('vendor/autoload.php');
use League\Csv\Writer;
use League\Csv\Reader;

if(count($argv)!=3) {
	echo "Usage $argv[0] [Directory With Hydro CSV Files] [destinationfilename]\n";
	die;
}


$iterator = new RecursiveIteratorIterator(
	new RecursiveDirectoryIterator($argv[1], RecursiveDirectoryIterator::SKIP_DOTS),
	RecursiveIteratorIterator::SELF_FIRST
);
$fullCsv=[];
$cnt=0;
$CSV = Writer::createFromPath($argv[2], 'w+');
foreach ($iterator as $item) {
	$path = $item->getPathname();
	if ($item->isFile()) {
		echo "File: {$path}\n";
		$pathInfo=pathinfo($path);
		preg_match("/.*([\d]{4}-\d\d-\d\d)/", $pathInfo['filename'], $matches);


		$reader = Reader::createFromPath($path, 'r');
		$reader->setHeaderOffset(1);
		$records = $reader->getRecords();

		foreach ($records as $offset => $record) {
			if(count($record)>1) {
				$record = array_merge(['Date'=>$matches[1]], $record);
				if($cnt==0) {
					$hydroHeaders=array_keys($record);
					$CSV->insertOne($hydroHeaders);

				} else {
					$CSV->insertOne($record);
				}
				$cnt++;
			}
		}
	}
}
