This directory contains all the plugins for working with numbrs. If you write one send me an email, and I'm happy to code review it and put it in the default distribution if it is useful to many people.

Filenames : 
    code.php    The PHP code to run (only PHP supported for now)
    doc.txt     The documentation string to show to the user

Special Inputs :
    $c          Configuration array. The "Model" for MVC coding.
    $data       The data that was returned. Could be a single value or an array of [[timestamp, number],...)]. Only useful for format and operation plugins.
    $params     An array of parameters to your function. In the form of array(key => value).

Special Ouputs :
    $c          Configuration array
    $c['limit'] How many rows to select. Applicable to selection plugins.
    $c['singleValue']   Whether to only return the latest row as a single number instead of an array. Applicable to selection plugins.
    $data       The output data. Applicable to operations.
