<?php

/**
 * Logging class:
 * - contains log_file, log_open and log_write methods
 * - log_file sets path and name of log file
 * - log_write will write message to the log file
 * - first call of the lwrite will open log file implicitly
 * - message is written with the following format: hh:mm:ss (script name) message
 */

class Logging
{
    // define default log file
    private $log_file = '/tmp/logfile.log';
    // define file pointer
    private $fp = null;
    // set log file (path and name)
    public function log_file($path)
    {
        $this->log_file = $path;
    }
    // write message to the log file
    public function log_write($message)
    {
        // if file pointer doesn't exist, then open log file
        if (!$this->fp) $this->log_open();
        // define script name
        $script_name = pathinfo($_SERVER['PHP_SELF'], PATHINFO_FILENAME);
        // define current time
        $time = date('H:i:s');
        // convert to string if array
        if (is_array($message)) $message = var_export($message, true);
        // write current time, script name and message to the log file
        fwrite($this->fp, "$time ($script_name) $message\n");
    }
    // open log file
    private function log_open()
    {
        // define log file path and name
        $lfile = $this->log_file;
        // define the current date (it will be appended to the log file name)
        $today = date('Y-m-d');
        // open log file for writing only; place the file pointer at the end of the file
        // if the file does not exist, attempt to create it
        $this->fp = fopen($lfile . '_' . $today, 'a') or exit("Can't open $lfile!");
    }
}

// adds the scores for a friend activity to the main friend list
function add_scores($friend_list, $scores)
{
    foreach ($scores as $friend_id => $score)
    {
        // make sure the facebook user scored is actually a friend
        if (array_key_exists($friend_id, $friend_list))
            $friend_list[$friend_id]['score'] += $score;
    }
    return $friend_list;
}

// takes the array of friends and sorts it based on their scores
function sort_by_score($friend_list)
{
    $sort_array = array();
    foreach ($friend_list as $id => $info)
    {
        $sort_array[$id] = $info['score'];
    }
    arsort($sort_array);

    $sorted_friend_list = array();
    foreach ($sort_array as $id => $score)
    {
        // only include friends who have a score
        if ($score > 0)
            $sorted_friend_list[$id] = array('name' => $friend_list[$id]['name'], 'score' => round($score));
    }

    return $sorted_friend_list;
}
/// sort by workplace
function sort_by_workplace($friend_list)
{
    
    $price = array();
    foreach ($friend_list as $key => $row)
    {
        $price[$key] = $row['empName'];
    }
    array_multisort($price, SORT_DESC, $friend_list);


    return $friend_list;
}

function unique_chars($string) {
   return count_chars(strtolower(str_replace(' ', '', $string)), 3);
}

function compare_strings($a, $b) {
    $index = similar_text(unique_chars($a), unique_chars($b), $percent);
    return array('index' => $index, 'percent' => $percent);
}
