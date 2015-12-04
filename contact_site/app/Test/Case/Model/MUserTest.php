<?php
App::uses('MUser', 'Model');

/**
 * MUser Test Case
 */
class MUserTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.m_user',
		'app.m_companies'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MUser = ClassRegistry::init('MUser');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MUser);

		parent::tearDown();
	}

}
