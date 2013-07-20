<?php
/**
 * A simple logging class.
 *
 * This logger provides an easy way to format logging.
 * For best possible results, you should experiment with various
 * widths for type and divider depending on the length of your
 * longest message type.
 *
 * @version     1.0
 * @author      Pierre-Emmanuel Lévesque
 * @email       pierre.e.levesque@gmail.com
 * @copyright   Copyright 2011, Pierre-Emmanuel Lévesque
 * @license     MIT License - @see LICENSE.md
 */
class loggy {

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
	 * If an array is used, the divider will not be displayed.
	 * This is to ensure that a width is always set for align_right_seperator.
	 *
	 * @var  int/array  [array(int) to hide divider]
	 */
	public $divider_width;

	/**
	 * Align right seperator
	 *
	 * Message text entered after this seperator will be aligned right.
	 * If NULL, all message text will be aligned left.
	 *
	 * @var  string/NULL
	 */
	public $align_right_seperator;

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
	 * @param   int/array      divider width
	 * @param   string/FALSE   align right seperator
	 * @param   string/FALSE   date format (@see http://php.net/manual/en/function.date.php)
	 * @param   string/FALSE   timezone identifier (@see http://www.php.net/manual/en/timezones.php)
	 * @return  none
	 */
	public function __construct(
		$write_enabled = TRUE,
		$type_width = 10,
		$divider_width = 80,
		$align_right_seperator = FALSE,
		$date_format = 'D F j, G:i:s T, Y',
		$timezone_identifier = 'UTC'
	) {
		$this->write_enabled = $write_enabled;
		$this->type_width = $type_width;
		$this->divider_width = $divider_width;
		$this->align_right_seperator = $align_right_seperator;
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
	 * @param   string  types to clear  [empty array clears all types]
	 * @return  none
	 */
	public function clear($types = array())
	{
		// Make sure types is an array.
		! is_array($types) AND $types = (array) $types;

		// All types are allowed.
		if (empty($types))
		{
			$this->items = array();
		}
		// Only specific types are allowed.
		else
		{
			foreach ($this->items as $key => $item)
			{
				foreach ($types as $type)
				{
					if ($item['type'] == $type)
					{
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
	 * @param   array  types to get  [empty array returns all types]
	 * @return  array  items
	 */
	public function get($types = array())
	{
		// Make sure types is an array.
		! is_array($types) AND $types = (array) $types;

		// All types are allowed.
		if (empty($types))
		{
			$items = $this->items;
		}
		// Only specific types are allowed.
		else
		{
			$items = array();

			foreach ($this->items as $item)
			{
				if (in_array($item['type'], $types))
				{
					$items[] = $item;
				}
			}
		}

		return $items;
	}

	/**
	 * Counts logged items
	 *
	 * @param   array  types to count  [empty array counts all types]
	 * @return  int    count
	 */
	public function count($types = array()) 
	{
		// Make sure types is an array.
		! is_array($types) AND $types = (array) $types;

		// All types are allowed.
		if (empty($types))
		{
			$count = count($this->items);
		}
		// Only specific types are allowed.
		else
		{
			$count = 0;
			foreach ($this->items as $item)
			{
				if (in_array($item['type'], $types))
				{
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
	 * @param   array   types to write  [empty array writes all types]
	 * @return  bool    written
	 */
	public function write($filename, $types = array())
	{
		$written = FALSE;

		if ($this->write_enabled)
		{
			// Get all the items to write.
			$items = $this->get($types);

			// Proceed if we have items to log and if the log file can be opened.
			if (count($items) > 0 AND $handle = fopen($filename, 'a'))
			{
				// Initialize the log.
				$log = '';

				// Set the divider.
				is_int($this->divider_width) AND $log .= str_repeat('-', $this->divider_width) . "\n";

				// Set the date.
				if ($this->date_format)
				{
					$this->timezone_identifier AND date_default_timezone_set($this->timezone_identifier);
					$log .= date($this->date_format)."\n";
				}

				// Log each item.
				foreach ($items as $item)
				{
					// Initialize indent.
					$indent = 0;

					// Set the type.
					if ($this->type_width)
					{
						$log .= $item['type'];
						$width_diff = $this->type_width - strlen($item['type']);
						$width_diff > 0 AND $log .= str_repeat(' ', $width_diff);
						$log .= ' | ';
						$indent += $this->type_width + 3;
					}

					// Force the message into an array.
					$message = (array) $item['message'];

					// Break up the message if it has right alignment.
					! empty($this->align_right_seperator) AND $message = explode($this->align_right_seperator, $message[0]);

					// Set the left aligned message.
					$log .= $message[0];

					// Set the right aligned message.
					if (isset($message[1]))
					{
						$indent += strlen($message[0]) + strlen($message[1]);
						$divider_width = (array) $this->divider_width;
						$align_width = $divider_width[0] - $indent;
						$align_width > 0 AND $log .= str_repeat(' ', $align_width);
						$log .= $message[1];
					}

					// Do a carriage return.
					$log .= "\n";
				}

				// Try to write the log to file.
				fwrite($handle, $log) AND $written = TRUE;

				// Close the file.
				fclose($handle);
			}
		}

		return $written;
	}

} // END loggy
