This directory contains all the plugins for working with numbrs. If you wish to write one, first do it as a "remote" plugin. If it is in PHP you can contact me, and I'm happy to code review it and put it in the default distribution if it is useful to many people.

Filenames : 
    code.php    The PHP code to run (only PHP supported for now)
    doc.txt     The documentation string to show to the user
    params.txt  A complete example parameter list

Special Inputs :
    $c          Configuration array. The "Model" for MVC coding
    $data       The data that was returned. Could be a single value or an array of [[timestamp, number],...)]. Only useful for format and operation plugins
    $params     An array of parameters to your function. In the form of array(key => value)

Special Ouputs :
    $c          Configuration array
    $data       The output data. Applicable to operations

Configuration :
    $c['ops']           Segmented operations into [name, [[key1, val1], ..]]
    $c['name']          The base name of the numbr

    $c['plugins']       The set of enabled plugins split into type 
    $c['headers']       Headers to be printed to the browser
    $c['code']          The canonical code for this graph
    $c['sql']           Selection information
    $c['sql']['where']  Strings to be joined into the SQL where clause. Defaults to 'numbr = :name'
    $c['sql']['orderby']    ORDERBY string. Defaults to 'timestamp DESC'
    $c['sql']['params'] PDO parameters. Defaults to 'array("name" => $this->c['name'], "limit" => array(PHP_INT_MAX, PDO::PARAM_INT))'
    $c['numbr']         The static data about the numbr from the database
    $c['singleValue']   Whether to only return the latest row as a single number instead of an array. Applicable to selection plugins
