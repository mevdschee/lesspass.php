<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../lesspass.php';

class RenderPasswordTests extends TestCase
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

    function testRenderPasswordUseRemainderOfLongDivisionBetweenEntropyAndSetOfCharsLengthAsAnIndex() {
        $entropy = 'dc33d431bce2b01182c613382483ccdb0e2f66482cbba5e9d07dab34acc7eb1e';
        $passwordProfile = $this->getPasswordProfile([]);
        $this->assertEquals('W', renderPassword($entropy, $passwordProfile)[0]);
    }

    function testRenderPasswordUseQuotientAsSecondEntropyRecursively() {
        $entropy = 'dc33d431bce2b01182c613382483ccdb0e2f66482cbba5e9d07dab34acc7eb1e';
        $passwordProfile = $this->getPasswordProfile([]);
        $this->assertEquals('H', renderPassword($entropy, $passwordProfile)[1]);
    }

    function testRenderPasswordHasDefaultLengthOfSixteen() {
        $entropy = 'dc33d431bce2b01182c613382483ccdb0e2f66482cbba5e9d07dab34acc7eb1e';
        $passwordProfile = $this->getPasswordProfile([]);
        $this->assertEquals(16, strlen(renderPassword($entropy, $passwordProfile)));
    }

    function testRenderPasswordCanSpecifyLength() {
        $entropy = 'dc33d431bce2b01182c613382483ccdb0e2f66482cbba5e9d07dab34acc7eb1e';
        $passwordProfile = $this->getPasswordProfile(['length'=>20]);
        $this->assertEquals(20, strlen(renderPassword($entropy, $passwordProfile)));
    }

    function testIncludeOneCharPerSetOfCharacters() {
        $password = insertStringPseudoRandomly('123456', gmp_init(7 * 6 + 2), 'uT');
        $this->assertEquals('T12u3456', $password);
    }

    function testRenderPasswordReturnAtLeastOneCharInEveryCharacterSet() {
        $entropy = 'dc33d431bce2b01182c613382483ccdb0e2f66482cbba5e9d07dab34acc7eb1e';
        $passwordProfile = $this->getPasswordProfile(['length'=>6]);
        $generatedPassword = renderPassword($entropy, $passwordProfile);
        $passwordLength = strlen($generatedPassword);
        $lowercaseOk = false;
        $uppercaseOk = false;
        $numbersOk = false;
        $symbolsOk = false;
        while ($passwordLength--) {
            if (strpos('abcdefghijklmnopqrstuvwxyz',$generatedPassword[$passwordLength]) !== false) {
                $lowercaseOk = true;
            }
            if (strpos('ABCDEFGHIJKLMNOPQRSTUVWXYZ',$generatedPassword[$passwordLength]) !== false) {
                $uppercaseOk = true;
            }
            if (strpos('0123456789',$generatedPassword[$passwordLength]) !== false) {
                $numbersOk = true;
            }
            if (strpos('!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~',$generatedPassword[$passwordLength]) !== false) {
                $symbolsOk = true;
            }
        }
        $this->assertEquals(6, strlen($generatedPassword));
        $this->assertEquals(true, $lowercaseOk && $uppercaseOk && $numbersOk && $symbolsOk, 'there is not at least one char in every characters set');
    }
}
