<?php
namespace PharIo\FileSystem;

use PHPUnit\Framework\TestCase;

/**
 * @covers \PharIo\FileSystem\Filename
 */
class FilenameTest extends TestCase {

    public function testCanBeConvertedToString() {
        $this->assertEquals(
            'abc',
            (string)(new Filename('abc'))
        );
    }

    public function testFileExistsReturnsFalseOnMissingFile() {
        $name = new Filename('/does/not/exist');
        $this->assertFalse($name->exists());
    }

    public function testFileExistsReturnsTrueOnExistingFile() {
        $name = new Filename(__FILE__);
        $this->assertTrue($name->exists());
    }

    public function testInvalidTypeForFilenameThrowsException() {
        $this->expectException(\InvalidArgumentException::class);
        new Filename(new \stdClass);
    }

    public function testReadThrowsExceptionIfFileDoesNotExist() {
        $name = new Filename('/does/not/exist');
        $this->expectException(\RuntimeException::class);
        $name->read();
    }

    public function testReadReturnsExpectedFile() {
        $name = new Filename(__DIR__ . '/fixtures/file.txt');
        $expectedFile = new File($name, 'foo');
        $this->assertEquals($expectedFile, $name->read());
    }

    public function testReturnsExpectedFilenameWithoutExtension() {
        $filename = new Filename(__DIR__ . '/foo/bar.txt');
        $expected = new Filename(__DIR__ . '/foo/bar');

        $this->assertEquals($expected, $filename->withoutExtension());
    }

    public function testIsWritable() {
        $filename = new Filename(__DIR__ . '/foo/bar.txt');
        $linkFilename = new Filename(__DIR__ . '/foo/link');

        $this->assertFalse($filename->exists());
        $this->assertTrue($filename->isWritable());

        try {
            touch($filename->asString());
            $this->assertTrue($filename->exists());
            $this->assertTrue($filename->isWritable());

            // Make file non writable
            chmod($filename->asString(), 0000);
            $this->assertFalse($filename->isWritable());

            // Create link to non writable file
            link($filename->asString(), $linkFilename->asString());
            $this->assertFalse($linkFilename->isWritable());

            // Make file writable
            chmod($filename->asString(), 0644);
            $this->assertTrue($linkFilename->isWritable());
        } finally {
            unlink($filename->asString());
            unlink($linkFilename->asString());
        }
    }

    public function testRename() {
        $filename = new Filename(__DIR__ . '/foo/bar.txt');
        $newFilename = new Filename(__DIR__ . '/foo/bar2.txt');

        $this->assertFalse($filename->exists());
        $this->assertFalse($newFilename->exists());
        $this->assertTrue($filename->isWritable());

        try {
            touch($filename->asString());
            $this->assertTrue($filename->exists());
            $this->assertTrue($filename->isWritable());

            $renamed = $filename->rename('bar2.txt');
            $this->assertEquals($renamed->asString(), $newFilename->asString());
            $this->assertTrue($newFilename->exists());
            $this->assertFalse($filename->exists());
        } finally {
            @unlink($filename->asString());
            @unlink($newFilename->asString());
        }
    }

    public function testFailedRename() {
        $filename = new Filename(__DIR__ . '/foo/bar.txt');

        $this->assertFalse($filename->exists());
        $this->assertTrue($filename->isWritable());

        try {
            touch($filename->asString());
            $this->assertTrue($filename->exists());
            $this->assertTrue($filename->isWritable());

            // Make parent directory non writable
            $mode = fileperms($filename->getDirectory());
            chmod($filename->getDirectory(), 0000);

            $renamed = $filename->rename('bar2.txt');
            $this->assertFalse($renamed);
        } finally {
            if (isset($mode)) {
                chmod($filename->getDirectory(), $mode);
            }
            unlink($filename->asString());
        }
    }

}
