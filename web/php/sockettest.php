<?php

class C3MALight{

	private 
/**
 * for binary switched lights the following commands apply:
 * ollpera | read actual status of all lamps reponse looks like:
 *
 * inputSize: 8
 * receiced: ollpera
 * states 11001111
 * ACK
 *
 * ollpew<n><h|l> | write status <l> (low, off) or <h> (high, on) 
 * 		  | to lamp with id <n>. Response same as ollpera
 */


/**
 * for rgb switched lights the following commands apply:
 * dmx write <channel> <value> | where channel starts with 1 and
 * 			       | and value is numeric uint_8
 * dmx fill <start> <end> <value> | fills everything with a value
 * 
 * Return values of each command: not relevant, "ch>" indicates
 * that new command can be send
 * 
 * dmx show | returns actual status of all values (channels) as HEX
 * values. 
 */
$fp = fsockopen("10.23.42.140", 2001, $errno, $errstr, 3);
if (!$fp) {
    echo "$errstr ($errno)<br />\n";
} else {
    fwrite($fp, "ollpew1h\n");
    while (!feof($fp)) {
        echo fgets($fp, 2048);
    }
    fclose($fp);
}


}
