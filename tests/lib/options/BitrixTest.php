<?php

namespace marvin255\bxmailer\tests\options;

use marvin255\bxmailer\tests\BaseTestCase;
use marvin255\bxmailer\options\Bitrix;

class BitrixTest extends BaseTestCase
{
    public function testConstructException()
    {
        $this->setExpectedException('\marvin255\bxmailer\Exception');
        $options = new Bitrix('');
    }

    public function testGet()
    {
        $moduleId = 'module_' . mt_rand();
        \Bitrix\Main\Config\Option::$options[$moduleId] = $options = [
            'option_1' => 'value_1_' . mt_rand(),
            'option_2' => 'value_1_' . mt_rand(),
            'option_3' => 'value_1_' . mt_rand(),
        ];
        $defaultOption = 'default_' . mt_rand();

        $optionsBag = new Bitrix($moduleId);

        $this->assertSame($options['option_1'], $optionsBag->get('option_1'));
        $this->assertSame($options['option_2'], $optionsBag->get('option_2'));
        $this->assertSame($options['option_3'], $optionsBag->get('option_3'));
        $this->assertSame($defaultOption, $optionsBag->get('default', $defaultOption));
    }

    public function testGetInt()
    {
        $moduleId = 'module_' . mt_rand();
        \Bitrix\Main\Config\Option::$options[$moduleId] = $options = [
            'option_1' => '1',
            'option_2' => 2,
            'option_3' => null,
        ];
        $defaultOption = '123';

        $optionsBag = new Bitrix($moduleId);

        $this->assertSame(1, $optionsBag->getInt('option_1'));
        $this->assertSame(2, $optionsBag->getInt('option_2'));
        $this->assertSame(0, $optionsBag->getInt('option_3'));
        $this->assertSame(123, $optionsBag->getInt('default', $defaultOption));
    }

    public function testGetBool()
    {
        $moduleId = 'module_' . mt_rand();
        \Bitrix\Main\Config\Option::$options[$moduleId] = $options = [
            'option_1' => '1',
            'option_2' => '',
            'option_3' => null,
            'option_4' => false,
        ];
        $defaultOption = 1;

        $optionsBag = new Bitrix($moduleId);

        $this->assertSame(true, $optionsBag->getBool('option_1'));
        $this->assertSame(false, $optionsBag->getBool('option_2'));
        $this->assertSame(false, $optionsBag->getBool('option_3'));
        $this->assertSame(false, $optionsBag->getBool('option_4'));
        $this->assertSame(true, $optionsBag->getBool('default', $defaultOption));
    }
}
