<?php

namespace Puzzle\MediaBundle;

/**
 * 
 * @author AGNES Gnagne Cedric <cecenho55@gmail.com>
 *
 */
final class MediaEvents
{
    const CREATE_FILE = "media.create_file";
    const COPY_FILE = "media.copy_file";
    const RENAME_FILE = "media.rename_file";
    const REMOVE_FILE = "media.remove_file";
    
    const CREATE_FOLDER = "media.create_folder";
    const RENAME_FOLDER = "media.rename_folder";
    const REMOVE_FOLDER = "media.remove_folder";
    const ADD_FILES_TO_FOLDER = "media.add_files_to_folder";
    const REMOVE_FILES_TO_FOLDER = "rmedia.emove_files_to_folder";
}