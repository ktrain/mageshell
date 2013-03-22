<?php

class Mageshell
{
    protected $_prompt = 'magesh> ';

    public function setPrompt($prompt)
    {
        $this->_prompt = $prompt;
        return $this;
    }

    public function start()
    {
        $histfile = '.magesh_history';
        if (PHP_OS == 'Linux') {
            readline_read_history($histfile);
        }

        do {
            $input = $this->_getInput($this->_prompt);
            if ($input === false) {
                break;
            }
            $cmd = $this->_formatInput($input);

            if (PHP_OS == 'Linux') {
                readline_add_history($input);
                readline_write_history($histfile);
            }

            echo "\n";
            $result = null;
            try {
                $result = eval($cmd);
            } catch (Exception $e) {
                echo $e->getMessage() . "\n\n" . $e->getTraceAsString() . "\n\n";
            }

            $output = $this->_formatOutput($result);
            echo "Result: " . $output . "\n\n";
        } while (true);

        return true;
    }

    protected function _getInput()
    {
        if (PHP_OS == 'Linux') {
            $input = readline($this->_prompt);
        } else {
            echo $this->_prompt;
            $input = fgets(STDIN);
        }

        return $input;
    }

    /**
     * If input is non-empty, ensure that there's a semicolon at the end.
     */
    protected function _formatInput($input)
    {
        if (!$input) {
            return 'return;';
        }

        return rtrim('return (' . rtrim($input), ';') . ');';
    }

    protected function _getPrintableData($obj)
    {
        if ($obj instanceof Varien_Object) {
            $data = $obj->getData();
        } elseif ($obj instanceof Varien_Data_Collection) {
            $data = array(
                'COLLECTION CONTENTS NOT PRINTED' => 'Do getItems(), toArray(), etc.'
            );
        } elseif (is_array($obj)) {
            $data = $obj;
        } else {
            return $obj;
        }

        foreach ($data as $i => $item) {
            if (is_object($item)) {
                $data [$i] = "**CLASS: " . get_class($item) . "**";
            }
            if (is_array($item)) {
                $subitem_entry = "**ARRAY[";
                // clean inner array for objects and arrays so we dont print too much
                foreach ($item as &$subitem) {
                    if (is_object($subitem)) {
                        $subitem = "**CLASS: " . get_class($subitem) . "**";
                    }
                    if (is_array($subitem)) {
                        $subitem = "**ARRAY[**hidden**]**";
                    }
                }
                // add the inner array data separated by commas
                $data [$i] .= implode(", ", $item);
                $data [$i] .= "]**";
            }
        }

        return $data;
    }

    protected function _formatOutput($result)
    {
        if (is_object($result)) {
            $classString = '[' . get_class($result) . ']';
            $data = $this->_getPrintableData($result);
            return $classString . "\nData:\n" . print_r($data, true);
        } else if (is_array($result)) {
            return print_r($result, true);
        }

        return $result;
    }
}

