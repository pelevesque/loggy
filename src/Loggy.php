<?php
/**
 * A simple logging class.
 *
 * This logger provides an easy way to format logging.
 * For best possible results, experiment with various type
 * widths depending on the length of your longest message type.
 *
 * @author      Pierre-Emmanuel Lévesque
 * @email       pierre.e.levesque@gmail.com
 * @copyright   Copyright 2011-2013, Pierre-Emmanuel Lévesque
 * @license     MIT License - @see LICENSE.md
 */

namespace Pel\Helper;

class Loggy
{
    /**
     * Allow writing to file
     *
     * @var bool
     */
    public $write_enabled;

    /**
     * Type width
     *
     * If FALSE, the type will not be displayed.
     *
     * @var  int/FALSE
     */
    public $type_width;

    /**
     * Divider width
     *
     * If FALSE, the divider will not be displayed.
     *
     * @var  int/FALSE
     */
    public $divider_width;

    /**
     * Date format
     *
     * If FALSE, the date will not be displayed.
     *
     * @see http://php.net/manual/en/function.date.php
     *
     * @var  string/FALSE
     */
    public $date_format;

    /**
     * Timezone identifier
     *
     * If FALSE, no timezone identifier will be set.
     *
     * @see http://www.php.net/manual/en/timezones.php
     *
     * @var  string/FALSE
     */
    public $timezone_identifier;

    /**
     * Logged items
     *
     * @var  array
     */
    public $items = array();

    /**
     * Constructor
     *
     * @param   bool           write enabled
     * @param   int/FALSE      type width
     * @param   int/FALSE      divider width
     * @param   string/FALSE   date format (@see http://php.net/manual/en/function.date.php)
     * @param   string/FALSE   timezone identifier (@see http://www.php.net/manual/en/timezones.php)
     * @return  none
     */
    public function __construct(
        $write_enabled = TRUE,
        $type_width = 20,
        $divider_width = 80,
        $date_format = 'D F j, G:i:s T, Y',
        $timezone_identifier = 'UTC'
    ) {
        $this->write_enabled = $write_enabled;
        $this->type_width = $type_width;
        $this->divider_width = $divider_width;
        $this->date_format = $date_format;
        $this->timezone_identifier = $timezone_identifier;
    }

    /**
     * Adds an item to the log
     *
     * @param   string  type
     * @param   string  message
     * @return  none
     */
    public function add($type, $message)
    {
        $this->items[] = array('type' => $type, 'message' => $message);
    }

    /**
     * Clears the log
     *
     * @param   string  types to clear [empty array clears all types]
     * @return  none
     */
    public function clear($types = array())
    {
        // Make sure types is an array.
        ! is_array($types) AND $types = (array) $types;

        // All types are allowed.
        if (empty($types)) {
            $this->items = array();
        // Only specific types are allowed.
        } else {
            foreach ($this->items as $key => $item) {
                foreach ($types as $type) {
                    if ($item['type'] == $type) {
                        unset($this->items[$key]);
                    }
                }
            }
        }

        // Reset the array keys.
        $this->items = array_values($this->items);
    }

    /**
     * Gets the items in the log
     *
     * @param   array  types to get [empty array returns all types]
     * @return  array  items
     */
    public function get($types = array())
    {
        // Make sure types is an array.
        ! is_array($types) AND $types = (array) $types;

        // All types are allowed.
        if (empty($types)) {
            $items = $this->items;
        // Only specific types are allowed.
        } else {
            $items = array();

            foreach ($this->items as $item) {
                if (in_array($item['type'], $types)) {
                    $items[] = $item;
                }
            }
        }

        return $items;
    }

    /**
     * Counts logged items
     *
     * @param   array  types to count [empty array counts all types]
     * @return  int    count
     */
    public function count($types = array()) 
    {
        // Make sure types is an array.
        ! is_array($types) AND $types = (array) $types;

        // All types are allowed.
        if (empty($types)) {
            $count = count($this->items);
        // Only specific types are allowed.
        } else {
            $count = 0;

            foreach ($this->items as $item) {
                if (in_array($item['type'], $types)) {
                    $count++;
                }
            }
        }

        return $count;
    }

    /**
     * Writes the logged items to file
     *
     * @param   string  filename
     * @param   array   types to write [empty array writes all types]
     * @return  bool    written
     */
    public function write($filename, $types = array())
    {
        $written = FALSE;

        if ($this->write_enabled) {

            // Get all the items to write.
            $items = $this->get($types);

            // Proceed if we have items to log and if the log file can be opened.
            if (count($items) > 0 AND $handle = fopen($filename, 'a')) {

                // Initialize the log.
                $log = '';

                // Set the divider.
                if ($this->divider_width) {
                    $log .= str_repeat('-', $this->divider_width) . "\n";
                }

                // Set the date.
                if ($this->date_format) {
                    if ($this->timezone_identifier) {
                        date_default_timezone_set($this->timezone_identifier);
                    }

                    $log .= date($this->date_format)."\n";
                }

                // Log each item.
                foreach ($items as $item) {

                    // Initialize indent.
                    $indent = 0;

                    // Set the type.
                    if ($this->type_width) {
                        $log .= $item['type'];
                        $width_diff = $this->type_width - strlen($item['type']);
                        $width_diff > 0 AND $log .= str_repeat(' ', $width_diff);
                        $log .= ' | ';
                        $indent += $this->type_width + 3;
                    }

                    // Set the message.
                    $log .= $message . "\n";
                }

                // Try to write the log to file.
                if (fwrite($handle, $log)) {
                    $written = TRUE;
                }

                // Close the file.
                fclose($handle);
            }
        }

        return $written;
    }
}
