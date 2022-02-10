<?php

declare(strict_types=1);

use Logan\Jiangmen\DES;
use PHPUnit\Framework\TestCase;

class DESTest extends TestCase
{
    protected $iv = 'B9B8035F';
    protected $config = [
        'key'   => 'B9B8035F',
        'token' => '67f40e6cf56bd3411b5d43a3e2511a81',
    ];

    public function testEncrypt()
    {
        $idCode = '5113011990010181111';
        $instance = new DES($this->config['key'], 'DES-CBC', DES::OUTPUT_BASE64, $this->iv);
        $res = $instance->encrypt($idCode);
        $this->assertEquals("kjYNvdl1aQy30SIJg5dEdu5OabM6W8NX", $res);
    }

    public function testDecrypt()
    {
        $encryptString = "kjYNvdl1aQy30SIJg5dEdu5OabM6W8NX";
        $instance = new DES($this->config['key'], 'DES-CBC', DES::OUTPUT_BASE64, $this->iv);
        $res = $instance->decrypt($encryptString);
        $this->assertEquals("5113011990010181111", $res);
    }
}
