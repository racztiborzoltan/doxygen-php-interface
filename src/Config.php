<?php
namespace Z\Doxygen;

class Config
{

    private $content = null;

    private $values = array();

    private $original_values = array();

    public function __construct($config_content)
    {
        $this->content = (string)$config_content;
        $this->process();
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($config_content)
    {
        $this->content = $config_content;
    }

    public function getValue($value_name)
    {
        $value_name = (string)$value_name;
        if (isset($this->values[$value_name])) {
            return $this->values[$value_name];
        }
        return null;
    }

    public function getValues()
    {
        return $this->values;
    }

    public function setValue($value_name, $value)
    {
        $value_name = (string)$value_name;
        $this->values[$value_name] = $value;
    }

    public function getChanges()
    {
        $value_names = array_merge(array_keys($this->values), array_keys($this->original_values));

        $changes = array();
        foreach ($value_names as $value_name) {
            if (!isset($this->values[$value_name]) || $this->values[$value_name] !== $this->original_values[$value_name]) {
                $changes[$value_name] = $this->values[$value_name];
            }
        }
        return $changes;
    }

    protected function process()
    {
        $temp = explode("\n", $this->content);
        for ($i = 0; $i<count($temp); $i++) {
            $line = $temp[$i];
            $line = trim($line);

            if (empty($line)) {
                continue;
            }

            if ($line[0] == '#') {
                continue;
            }

            if (strpos($line, '+=') !== false) {
                $parse = explode('+=', $line);
                foreach ($parse as $key => $value) {
                    $parse[$key] = trim($value);
                }
                if (count($parse) == 2) {
                    if (!is_array($this->values[$parse[0]])) {
                        $this->values[$parse[0]] = array();
                    }
                    $this->values[$parse[0]][] = $parse[1];
                }
                continue;
            }

            if (strpos($line, '=') !== false) {
                $parse = explode('=', $line);
                foreach ($parse as $key => $value) {
                    $parse[$key] = trim($value);
                }
                if (count($parse) == 2) {
                    $this->setValue($parse[0], $parse[1]);
                }
            }
        }

        $this->original_values = $this->values;
    }

    /**
     *
     * @return string
     */
    public function render()
    {
        $str = '';
        foreach ($this->values as $value_name => $value) {
            if (is_bool($value)) {
                $value = $value ? 'YES': 'NO';
            }
            if (is_numeric($value)) {
                $value = (int)$value;
            }
//             if (is_string($value)) {
//                 if (!empty($value)) {
//                     if (preg_match('#\s#', $value)) {
//                         $value = '"' . $value . '"';
//                     }
//                 }
//             }

            if (is_array($value)) {
                $temp = '';
                foreach ($value as $value_item) {
                    $temp .= str_pad($value_name, 22) . ' += ' . $value_item . PHP_EOL;
                }
                $str .= $temp;
            } else {
                $str .= str_pad($value_name, 22) . ' = ' . $value . PHP_EOL;
            }

        }
        return $str;
    }

    public function __toString()
    {
        return $this->render();
    }

}
