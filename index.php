<?php

$testdir = '/Users/floyd/vagrant/chroma/sites/archive1.freshjones.local/public';
$testfile = 'test_output.txt';

file_put_contents($testdir . '/' . $testfile, '');

$dest = '/Users/floyd/Desktop/ArchiveDestination';

$string = '';

if(!is_dir($dest))
{
	mkdir($dest);
	$string = 'making directory' . "\n";
}

if($string)
{
file_put_contents($testdir . '/' . $testfile, $string, FILE_APPEND);
}

if(isset($argv) && !empty($argv) && count($argv) > 1)
{

	array_shift($argv);

	file_put_contents($testdir . '/' . $testfile, print_r($argv,true), FILE_APPEND);

	foreach($argv AS $dir)
	{

		$data = array();

		$segments = explode('/',$dir);
		$name = array_pop($segments);

		$data['name'] = $name;
		$data['path'] = $dir;

		if(!is_dir($dir))
		{
			
			$data['contents'] = $dir;

			file_put_contents($testdir . '/' . $testfile, $dir . " is a file \n", FILE_APPEND);
		
		} else {


			$contents = array();

			dirToArray($dir, '', $contents);

			$contents = implode("\n", $contents);

			$data['contents'] = $contents;

			file_put_contents($testdir . '/' . $testfile, print_r($data,true), FILE_APPEND);

			tarArchive($data);


			
		}
		
	}

}


function tarArchive($data)
{

	$dest = '/Users/floyd/Desktop/ArchiveDestination/' .  strtolower($data['name']) . '.tar.gz';

	$archive = new PharData($dest);
	$archive->buildFromDirectory($data['path']);
	$archive->compress(Phar::GZ); 

	//return true;
}

function dirToArray($org, $dir, &$result) 
{

   $cdir = preg_grep('/^([^.])/', scandir($org . DIRECTORY_SEPARATOR .$dir));

   $dirsNotAllowed = array
   		(
      		".",
      		"..",
      		"AppleShare PDS",
      		"Desktop DB",
      		"Desktop DF",
      		"Norton FS Data",
      		"Norton FS Index",
      		"TheVolumeSettingsFolder"
      	);

    $filesNotAllowed = array('.DS_Store');

    foreach ($cdir as $key => $value)
    {
		if (!in_array($value,$dirsNotAllowed))
		{

			if (is_dir($org . DIRECTORY_SEPARATOR . $dir . DIRECTORY_SEPARATOR . $value))
		 	{
		    	dirToArray($org, $dir . DIRECTORY_SEPARATOR . $value, $result);
			}
			else
			{

			 	if(!in_array($value,$filesNotAllowed))
			 	{
			 		
			 		$result[] = trim($dir . DIRECTORY_SEPARATOR . $value,'/');

			 	} else {
			 		
			 		unlink($dir . DIRECTORY_SEPARATOR . $value);
			 		
			 	}
		    
			}

		}

   	}

} 