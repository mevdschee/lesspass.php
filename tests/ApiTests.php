<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../lesspass.php';

class ApiTests extends TestCase
{
    public function testRenderPassword() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = [];
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('WHLpUL)e00[iHR+w', $generatedPassword);
    }

    public function testRenderPasswordNoSymbols() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = ['length'=>14,'counter'=>2,'symbols'=>false];
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('MBAsB7b1Prt8Sl', $generatedPassword);
    }

    public function testRenderPasswordOnlyDigits() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = ['length'=>6, 'counter'=>3, 'lowercase'=>false, 'uppercase'=>false, 'symbols'=>false];
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('117843', $generatedPassword);
    }

    public function testRenderPasswordNoNumbers() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $passwordProfile = ['length'=>14,'numbers'=>false];
        $generatedPassword = generatePassword($site, $login, $masterPassword, $passwordProfile);
        $this->assertEquals('sB>{qF}wN%/-fm', $generatedPassword);
    }

    public function testRenderPasswordWithDefaultOptions() {
        $site = 'example.org';
        $login = 'contact@example.org';
        $masterPassword = 'password';
        $generatedPassword = generatePassword($site, $login, $masterPassword);
        $this->assertEquals('WHLpUL)e00[iHR+w', $generatedPassword);
    }
}
