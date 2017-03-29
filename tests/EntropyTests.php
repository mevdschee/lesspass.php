<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../lesspass.php';

class EntropyTests extends TestCase
{
    public function testCalcEntropyPbkdf2WithDefaultParams() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = getPasswordProfile([]);
        $entropy = calcEntropy($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('dc33d431bce2b01182c613382483ccdb0e2f66482cbba5e9d07dab34acc7eb1e', $entropy);
    }

    public function testCalcEntropyWithDifferentOptions() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = getPasswordProfile(['iterations'=>8192, 'keylen'=>16, 'digest'=>'sha512']);
        $entropy = calcEntropy($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('fff211c16a4e776b3574c6a5c91fd252', $entropy);
    }

    public function testCalcEntropyWithCounterOne() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = getPasswordProfile(['iterations'=>1,'keylen'=>16]);
        $entropy = calcEntropy($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('d3ec1e988dd0b3640c7491cd2c2a88b5', $entropy);
    }


    public function testCalcEntropyWithCounterTwo() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = getPasswordProfile(['iterations'=>1,'keylen'=>16,'counter'=>2]);
        $entropy = calcEntropy($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('ddfb1136260f930c21f6d72f6eddbd40', $entropy);
    }

    public function testConsumeEntropy() {
        list($value,$entropy) = consumeEntropy('', gmp_init(4 * 4 + 2), "abcd", 2);
        $this->assertEquals('ca', $value);
        $this->assertEquals(1, (int)$entropy);
    }
}