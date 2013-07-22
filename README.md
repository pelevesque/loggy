# loggy

## About

loggy is a simple PHP logging class.

It helps you log messages to a file.

## Usage

### Initialization

First, create some blank files to store your logs. Make sure they are writable.

    // Create a default version of loggy.
    // default = $loggy->(TRUE, 20, 80, 'D F j, G:i:s T, Y', 'UTC);
    $loggy = new loggy();

    // Create a custom version of loggy.
    $write_enabled = TRUE; // Enable writing to files.
    $type_width = 30; // Width allocated to the message type.
    $divider_width = 100; // Width allocated to the divider.
    $date_format = 'D F j, G:i:s T'; // http://php.net/manual/en/function.date.php
    $timezone_identifier = 'Asia/Toykyo'; // http://www.php.net/manual/en/timezones.php
    $loggy = new loggy(
        $write_enabled,
        $type_width,
        $divider_width,
        $date_format,
        $timezone_identifier
    );

    // Options can also be set after initialization.
    $loggy->write_enabled = FALSE;
    $loggy->type_width = 35;
    $loggy->divider_width = 50;
    $loggy->date_format = 'D F j';
    $loggy->timezone_identifier = 'UTC';

### Modifying The Logs

    // Add a message to the log.
    $type = 'error';
    $message = 'Parsing failed';
    $loggy->add($type, $message);

    // Clear the log.
    $loggy->clear();

    // Clear one type.
    $loggy->clear('error');

    // Clear many types.
    $loggy->clear(array('error', 'feature', 'warning'));

### Returning Log Information

    // Get the log.
    $log = $loggy->get();

    // Get one logged type.
    $log = $loggy->get('error');

    // Get many logged types.
    $log = $loggy->get(array('error', 'feature', 'warning'));

    // Count all the logged items.
    $num_logged_items = $loggy->count();

    // Count only one type of logged items.
    #num_errors = $log = $loggy->get('error');

    // Get many types of logged items,
    $num_items = $loggy->get(array('error', 'feature', 'warning'));

### Writing Log To File

    // Write the entire logs at the end of the desired file.
    $filename = 'logs.txt';
    $loggy->write($filename);

    // Write one type at the end of the desired file.
    $filename = 'logs.txt';
    $type = 'error';
    $loggy->write($filename, $type);

    // Write many types at the end of the desired file.
    $filename = 'logs.txt';
    $types = array('error', 'feature', 'warning');
    $loggy->write($filename, $types);
