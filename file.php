<?php

define('AJAX_SCRIPT', true);

require_once(dirname(dirname(dirname(__FILE__))) . '/config.php');
require_once($CFG->dirroot . '/lib/filelib.php');
require_once($CFG->dirroot.'/mod/lesson/locallib.php');
require_login();//this file will work if the user is login

$id        = required_param('id', PARAM_INT);//get id of lesson activity
$pageid    = optional_param('pageid', null, PARAM_INT); //get id of lesson page
$filename  = required_param('filename', PARAM_RAW);//get tab name of code
$codes     = required_param('codes', PARAM_RAW);//get codes of editor
$component = "mod_lesson";//component is always mod_lesson because of working in lesson
$filearea  = 'programming';//custom file area
$filepath  = '/';//root of programming file area
$cm        = get_coursemodule_from_id('lesson', $id, 0, false, MUST_EXIST);//get curent lesson instance
$context   = context_module::instance($cm->id);//get context of current lesson
if ($pageid == null) {
    $lesson = new lesson($DB->get_record('lesson', array('id' => $cm->instance), '*', MUST_EXIST));
    $pageid = $lesson->firstpageid;
}
$fs        = get_file_storage();//returns an object for working with files
$fileinfo  = array(
    'contextid' => $context->id, //////////////////////////////
    'component' => $component,   //
    'filearea'  => $filearea,    // an array for details of a file that we want to use
    'itemid'    => $USER->id,    //
    'filepath'  => $filepath,    //////////////////////////////     
    'filename'  =>  $id . '_' . $pageid .'_' . $filename . '.txt'); 

$file = $fs->get_file($fileinfo['contextid'], $fileinfo['component'], $fileinfo['filearea'], 
        $fileinfo['itemid'], $fileinfo['filepath'], $fileinfo['filename']);//get file using 'fileinfo' array

if ($file) {// if file exists
    $file->delete();//delets file
}
$fs->create_file_from_string($fileinfo, $codes);//creates the file with contents of editor code