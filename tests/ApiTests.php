<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../lesspass.php';

class ApiTests extends TestCase
{
    private function getPasswordProfile($passwordProfile)
    {
        $defaultPasswordProfile = (object)[
            'lowercase'=>true,
            'uppercase'=>true,
            'numbers'=>true,
            'symbols'=>true,
            'digest'=>'sha256',
            'iterations'=>100000,
            'keylen'=>32,
            'length'=>16,
            'counter'=>1,
            'version'=>2
        ];
        return (object)array_merge((array)$defaultPasswordProfile,(array)$passwordProfile);
    }

    public function testRenderPassword()
    {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = $this->getPasswordProfile([]);
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('WHLpUL)e00[iHR+w', $generatedPassword);
    }

    public function testRenderPasswordNoSymbols()
    {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = $this->getPasswordProfile(['length'=>14,'counter'=>2,'symbols'=>false]);
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('MBAsB7b1Prt8Sl', $generatedPassword);
    }

    public function testRenderPasswordOnlyDigits()
    {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = $this->getPasswordProfile([
            'length'=>6,
            'counter'=>3,
            'lowercase'=>false,
            'uppercase'=>false,
            'numbers'=>true,
            'symbols'=>false
        ]);
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('117843', $generatedPassword);
    }

    public function testRenderPasswordNoNumbers()
    {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = $this->getPasswordProfile(['length'=>14,'numbers'=>false]);
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('sB>{qF}wN%/-fm', $generatedPassword);
    }

    public function testRenderPasswordWithDefaultOptions()
    {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = $this->getPasswordProfile([]);
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('WHLpUL)e00[iHR+w', $generatedPassword);
    }
}
