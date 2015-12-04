<?php
App::uses('MCompany', 'Model');

/**
 * MCompany Test Case
 */
class MCompanyTest extends CakeTestCase {

/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array(
		'app.m_company'
	);

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->MCompany = ClassRegistry::init('MCompany');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->MCompany);

		parent::tearDown();
	}

}
