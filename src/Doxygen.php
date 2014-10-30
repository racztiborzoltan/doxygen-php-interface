<?php
namespace Z\Doxygen;

class Doxygen
{

    /**
     * Path to the doxygen
     * @var string
     */
    private $doxygen_path;

    private $temp_config_file_path;

    private $full_command_output;

    /**
     *
     * @var \Z\Doxygen\Config
     */
    private $config;

    public function __construct($doxygen_path, Config $config)
    {
        if (!is_executable($doxygen_path)) {
            throw new \InvalidArgumentException('Invalid Doxygen path or not executable!');
        }

        $this->doxygen_path = $doxygen_path;
        $this->setConfig($config);
    }

    /**
     * @return \Z\Doxygen\Config
     */
    public function getConfig()
    {
        return $this->config;
    }

    public function setConfig(Config $config)
    {
        $this->config = $config;
        return $this;
    }

    protected function _getTempConfigFilePath()
    {
        return tempnam(null, 'doxygen_temp_config-');;
    }

    private function _create_temp_config_file()
    {
        $this->temp_config_file_path = $this->_getTempConfigFilePath();
        file_put_contents($this->temp_config_file_path, (string)$this->config);
        return $this->temp_config_file_path;
    }

    public function _delete_temp_config_file()
    {
        unlink($this->temp_config_file_path);
    }

    public function run()
    {
        $temp_config_file_path = $this->_create_temp_config_file();

        $doxygen_command = $this->doxygen_path.' '.$temp_config_file_path;

        $handle = popen($doxygen_command.' 2>&1', 'r');
        $output = '';
        while ($result_line = fgets($handle)) {
            $output .= $result_line;
        }
        $this->full_command_output = $output;

        $this->_delete_temp_config_file();
    }

    public function getFullCommandOutput()
    {
        return $this->full_command_output;
    }

}
