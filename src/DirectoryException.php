<?php declare(strict_types = 1);
namespace PharIo\FileSystem;

class DirectoryException extends Exception  {

    const InvalidMode = 1;
    const CreateFailed = 2;
    const ChmodFailed = 3;
    const InvalidType = 4;

}
