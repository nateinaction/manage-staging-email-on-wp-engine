<?php

namespace nategay\manage_staging_email_wpe;

class SettingsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test setPluginOptions()
     *
     * @param $options_array array
     * @param $valid_email bool
     * @param $expect array
     *
     * @dataProvider dataSetPluginOptions
     */
    public function testSetPluginOptions($options_array, $valid_email, $expect)
    {
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

    /**
     * Test getPluginOptions()
     *
     * @param $options_array bool|array False if does not exist else array
     * @param $expect array
     *
     * @dataProvider dataGetPluginOptions
     */
    public function testGetPluginOptions($options_array, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Settings')
            ->setMethods(array('getOptionsFromDb'))
            ->getMock();

        $mock->method('getOptionsFromDb')->will($this->returnValue($options_array));

        $this->assertEquals($mock->getPluginOptions(), $expect);
    }

    public function dataGetPluginOptions()
    {
        return array(
            array(
                false,
                array(
                    'email_preference' => 'admin',
                    'custom_address' => '',
                ),
            ),
            array(
                array(
                    'email_preference' => 'custom',
                    'custom_address' => 'poke@poke.com',
                ),
                array(
                    'email_preference' => 'custom',
                    'custom_address' => 'poke@poke.com',
                ),
            ),
        );
    }

    /**
     * Test getSelection()
     *
     * @param $options_array array
     * @param $expect string
     *
     * @dataProvider dataGetSelection
     */
    public function testGetSelection($options_array, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Settings')
            ->setMethods(array('getPluginOptions'))
            ->getMock();
        $mock->method('getPluginOptions')->will($this->returnValue($options_array));
        $this->assertEquals($mock->getSelection(), $expect);
    }
    public function dataGetSelection()
    {
        return array(
            array(
                array(
                    'email_preference' => 'admin',
                    'custom_address' => '',
                ),
                'admin',
            ),
            array(
                array(
                    'email_preference' => 'blah',
                    'custom_address' => 'poke@poke.com',
                ),
                'blah',
            ),
        );
    }

    /**
     * Test getCustomAddress()
     *
     * @param $options_array array
     * @param $expect string
     *
     * @dataProvider dataGetCustomAddress
     */
    public function testGetCustomAddress($options_array, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Settings')
            ->setMethods(array('getPluginOptions'))
            ->getMock();
        $mock->method('getPluginOptions')->will($this->returnValue($options_array));
        $this->assertEquals($mock->getCustomAddress(), $expect);
    }
    
    public function dataGetCustomAddress()
    {
        return array(
            array(
                array(
                    'email_preference' => 'admin',
                    'custom_address' => '',
                ),
                '',
            ),
            array(
                array(
                    'email_preference' => 'blah',
                    'custom_address' => 'poke@poke.com',
                ),
                'poke@poke.com',
            ),
        );
    }

    /**
     * Test getPreferredAddress()
     *
     * @param $selection string Selected option from plugin options
     * @param $admin_email string Admin email from DB
     * @param $custom_address string Custom email from plugin options
     * @param $expect string
     *
     * @dataProvider dataGetPreferredAddress
     */
    public function testGetPreferredAddress($selection, $admin_email, $custom_address, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Settings')
            ->setMethods(array('getSelection', 'getAdminEmail', 'getCustomAddress'))
            ->getMock();
        $mock->method('getSelection')->will($this->returnValue($selection));
        $mock->method('getAdminEmail')->will($this->returnValue($admin_email));
        $mock->method('getCustomAddress')->will($this->returnValue($custom_address));

        $this->assertEquals($mock->getPreferredAddress(), $expect);
    }

    public function dataGetPreferredAddress()
    {
        return array(
            array(
                'admin',
                'admin@email.com',
                'poke@poke.com',
                'admin@email.com',
            ),
            array(
                'custom',
                'admin@email.com',
                'poke@poke.com',
                'poke@poke.com',
            ),
            array(
                'abc123',
                'admin@email.com',
                'poke@poke.com',
                'admin@email.com',
            ),
        );
    }
}
