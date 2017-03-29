<?php
$characterSubsets = (object)[
  'lowercase'=>'abcdefghijklmnopqrstuvwxyz',
  'uppercase'=>'ABCDEFGHIJKLMNOPQRSTUVWXYZ',
  'numbers'=>'0123456789',
  'symbols'=>'!"#$%&\'()*+,-./:;<=>?@[\\]^_`{|}~'
];

function getPasswordProfile($passwordProfile) {
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

function generatePassword($site, $login, $masterPassword, $passwordProfile=null) {
  $passwordProfile = getPasswordProfile($passwordProfile);
  $entropy = calcEntropy($site, $login, $masterPassword, $passwordProfile);
  return renderPassword($entropy, $passwordProfile);
}

function calcEntropy($site, $login, $masterPassword, $passwordProfile) {
  $salt = $site . $login . dechex($passwordProfile->counter);
  return hash_pbkdf2($passwordProfile->digest, $masterPassword, $salt, $passwordProfile->iterations, $passwordProfile->keylen*2);
}

function getSetOfCharacters($rules=null) {
  global $characterSubsets;
  if (!$rules) {
    return $characterSubsets->lowercase . $characterSubsets->uppercase . $characterSubsets->numbers . $characterSubsets->symbols;
  }
  $setOfChars = '';
  foreach ($rules as $rule) {
    $setOfChars .= $characterSubsets->$rule;
  }
  return $setOfChars;
}

function consumeEntropy($generatedPassword, $quotient, $setOfCharacters, $maxLength) {
  if (strlen($generatedPassword) >= $maxLength) {
    return [$generatedPassword, $quotient];
  }
  list($quotient,$remainder) = gmp_div_qr($quotient, strlen($setOfCharacters));
  $generatedPassword .= $setOfCharacters[(int)$remainder];
  return consumeEntropy($generatedPassword, $quotient, $setOfCharacters, $maxLength);
}

function insertStringPseudoRandomly($generatedPassword, $entropy, $string) {
  for ($i = 0; $i < strlen($string); $i++) {
    list($quotient,$remainder) = gmp_div_qr($entropy, strlen($generatedPassword));
    $generatedPassword = substr($generatedPassword, 0, (int)$remainder) . $string[$i] . substr($generatedPassword, (int)$remainder);
    $entropy = $quotient;
  }
  return $generatedPassword;
}

function getOneCharPerRule($entropy, $rules) {
  global $characterSubsets;
  $oneCharPerRules = '';
  foreach ($rules as $rule) {
    list($value,$entropy) = consumeEntropy('', $entropy, $characterSubsets->$rule, 1);
    $oneCharPerRules .= $value;
  }
  return [$oneCharPerRules, $entropy];
}

function getConfiguredRules($passwordProfile) {
  return array_merge(array_filter(['lowercase', 'uppercase', 'numbers', 'symbols'], function ($rule) use ($passwordProfile) {
    return isset($passwordProfile->$rule) && $passwordProfile->$rule;
  }));
}

function renderPassword($entropy, $passwordProfile) {
  $rules = getConfiguredRules($passwordProfile);
  $setOfCharacters = getSetOfCharacters($rules);
  list($password,$passwordEntropy) = consumeEntropy('', gmp_init($entropy,16), $setOfCharacters, $passwordProfile->length - count($rules));
  list($charactersToAdd,$characterEntropy) = getOneCharPerRule($passwordEntropy, $rules);
  return insertStringPseudoRandomly($password, $characterEntropy, $charactersToAdd);
}