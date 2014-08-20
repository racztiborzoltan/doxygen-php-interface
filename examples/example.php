<?php
use Z\Doxygen\Config;
use Z\Doxygen\Doxygen;
require_once '../vendor/autoload.php';


/**
 * Recursively delete a directory that is not empty
 * Original source: http://hu1.php.net/manual/en/function.rmdir.php#98622
 *
 * @param string $dir
 */
function rrmdir($dir) {
    if (is_dir($dir)) {
        $objects = scandir($dir);
        foreach ($objects as $object) {
            if ($object != "." && $object != "..") {
                if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
            }
        }
        reset($objects);
        rmdir($dir);
    }
}



if (isset($_POST['submit'])){
    set_time_limit(0);

    $output_directory = __DIR__.'/../temp/output';

    rrmdir($output_directory);

    $config = new Config(file_get_contents('.doxygen.small.conf'));

    // string config value with spaces:
    $config->setValue('PROJECT_NAME', '"Doxygen PHP Interface"');
    // string config value without spaces:
    $config->setValue('PROJECT_NUMBER', '1.0.0');
    $config->setValue('OUTPUT_DIRECTORY', $output_directory);
    $config->setValue('INPUT', __DIR__.'/../src');
    // YES or NO config value setting:
    $config->setValue('RECURSIVE', true);
    // list item config value setting:
    $config->setValue('EXCLUDE_PATTERNS', array(
        'PATTERN_1',
        'PATTERN_2',
        'PATTERN_3',
    ));

    $doxygen = new Doxygen('P:\PortableApps\doxygen\doxygen.exe', $config);
    $doxygen->run();

    $output = $doxygen->getFullCommandOutput();
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Example for Doxygen PHP Interface</title>
    <style type="text/css">
        input[type="text"], textarea{
            min-width: 80%;
        }
    </style>
</head>
<body>

    <h1>Example for Doxygen Interface</h1>

    <form method="post">

        <label>
            Path to the Doxygen:
            <br />
            <input name="doxygen_path" type="text" value=""/>
        </label>
        <br /><br />

        <input name="submit" type="submit" />
    </form>

    <?if (isset($output)):?>
        <br />
        <label>
            Output: <br />
            <textarea rows="5" cols="10" style="width: 100%;resize: vertical;"><?=$output?></textarea>
        </label>
    <?endif?>

</body>
</html>
