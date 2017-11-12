<?php

namespace nategay\manage_staging_email_wpe;

class SettingsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Sanitize options before setting in DB
     *
     * @param options_array array
     * @param valid_email bool
     * @param $expect array
     *
     * @dataProvider dataSetPluginOptions
     */
    public function testSetPluginOptions($options_array, $valid_email, $expect)
    {
        //$this->markTestSkipped('Skipping');

        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Settings')
            ->setMethods(array('getPluginOptions', 'checkForValidEmail', 'setOptionsInDb'))
            ->getMock();

        $mock->method('getPluginOptions')
            ->will($this->returnValue(array(
                'email_preference' => 'admin',
                'custom_address' => '',
            )));

        $mock->method('checkForValidEmail')
            ->will($this->returnValue($valid_email));

        $mock->method('setOptionsInDb')
            ->will($this->returnValue(true));

        $result = $mock->setPluginOptions($options_array);

        $this->assertEquals($result, $expect);
    }

    public function dataSetPluginOptions()
    {
        return array(
            array(
                array(
                    'email_preference' => 'admin',
                    'custom_address' => '',
                ),
                true,
                array(
                    'status' => true,
                    'message' => 'Saved email preference.',
                ),
            ),
            array(
                array(
                    'email_preference' => 'admin',
                    'custom_address' => '',
                ),
                false,
                array(
                    'status' => true,
                    'message' => 'Saved email preference.',
                ),
            ),
            array(
                array(
                    'email_preference' => 'custom',
                    'custom_address' => '',
                ),
                true,
                array(
                    'status' => true,
                    'message' => 'Saved email preference.',
                ),
            ),
            array(
                array(
                    'email_preference' => 'custom',
                    'custom_address' => '',
                ),
                false,
                array(
                    'status' => false,
                    'message' => 'Please enter a valid email.',
                ),
            ),
        );
    }
}
