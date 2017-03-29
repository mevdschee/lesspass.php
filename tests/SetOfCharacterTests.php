<?php
use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../lesspass.php';

class SetOfCharacterTests extends TestCase
{
    function testGetDefaultSetOfCharacters() {
        $setOfCharacters = getSetOfCharacters();
        $this->assertEquals(
          'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~',
          $setOfCharacters
        );
        $this->assertEquals(26 * 2 + 10 + 32, strlen($setOfCharacters));
    }

    function testGetDefaultSetOfCharactersConcatRulesInOrder() {
        $setOfCharacters = getSetOfCharacters(['lowercase', 'uppercase', 'numbers']);
        $this->assertEquals('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789', $setOfCharacters);
        $this->assertEquals(26 * 2 + 10, strlen($setOfCharacters));
    }

    function testgetSetOfCharactersOnlyLowercase() {
        $setOfCharacters = getSetOfCharacters(['lowercase']);
        $this->assertEquals('abcdefghijklmnopqrstuvwxyz', $setOfCharacters);
        $this->assertEquals(26, strlen($setOfCharacters));
    }

    function testgetSetOfCharactersOnlyUppercase() {
        $setOfCharacters = getSetOfCharacters(['uppercase']);
        $this->assertEquals('ABCDEFGHIJKLMNOPQRSTUVWXYZ', $setOfCharacters);
        $this->assertEquals(26, strlen($setOfCharacters));
    }

    function testgetSetOfCharactersOnlyNumbers() {
        $setOfCharacters = getSetOfCharacters(['numbers']);
        $this->assertEquals('0123456789', $setOfCharacters);
        $this->assertEquals(10, strlen($setOfCharacters));
    }

    function testgetSetOfCharactersOnlySymbols() {
        $setOfCharacters = getSetOfCharacters(['symbols']);
        $this->assertEquals('!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~', $setOfCharacters);
        $this->assertEquals(32, strlen($setOfCharacters));
    }

    function testGenerateOneCharPerRule() {
        list($value,$entropy) = getOneCharPerRule(gmp_init(26 * 26), ['lowercase', 'uppercase']);
        $this->assertEquals('aA', $value);
        $this->assertEquals(2, strlen($value));
        $this->assertEquals(1, (int)$entropy);
    }

    function testConfiguredRules() {
        $this->assertEquals(['uppercase'], getConfiguredRules((object)['uppercase'=>true]));
        $this->assertEquals(['lowercase', 'uppercase'], getConfiguredRules((object)['uppercase'=>true, 'lowercase'=>true]));
        $this->assertEquals(['lowercase'], getConfiguredRules((object)['lowercase'=>true, 'symbols'=>false]));
        $this->assertEquals(['lowercase', 'uppercase', 'numbers', 'symbols'], getConfiguredRules((object)['lowercase'=>true, 'uppercase'=>true, 'symbols'=>true, 'numbers'=>true]));
    }
}
