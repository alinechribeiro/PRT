<?php

use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract

class TestCase extends BaseTestCase {
	/**
	 * Creates the application.
	 *
	 * @return \Laravel\Lumen\Application
	 */
	public function createApplication() {
		return require __DIR__ .'/../bootstrap/app.php';
	}
	public function test_list_all_users_call_responds() {
		$token    = 'Bearer alinechribeiro:protected';
		$response = $this->json('GET', '/api/listusers', [], ['HTTP_Authorization' => $token]);
		$this->assertEquals(200, $this->response->status());
	}
	public function test_create_user_call_responds() {
		$token      = 'Bearer alinechribeiro:protected';
		$num        = rand(5, 100);
		$parameters = [
			'forename' => 'John',
			'surname'  => 'Doe',
			'username' => 'user_'.$num,
			'darkmode' => 1,
			'token'    => 'anythinghere'
		];
		$response = $this->json('POST', '/api/createuser', $parameters, ['HTTP_Authorization' => 'Bearer alinechribeiro:protected']);
		$this->assertEquals(201, $this->response->status());
	}
	public function test_delete_user_call_responds() {
		$token    = 'Bearer alinechribeiro:protected';
		$last     = DB::table('users')->latest('id')->first();
		$response = $this->json('GET', '/api/deleteuser/'.$last->id, [], ['HTTP_Authorization' => $token]);
		$this->assertEquals(200, $this->response->status());
	}
	public function test_search_user_call_responds() {
		$token      = 'Bearer alinechribeiro:protected';
		$parameters = [
			'identifier'      => 'forename',
			'identifierField' => 'John',
			'fields'          => ["surname"]
		];
		$response = $this->json('POST', '/api/searchuser/', $parameters, ['HTTP_Authorization' => $token]);
		$this->assertEquals(200, $this->response->status());
	}
	public function test_update_user_call_responds() {
		$token      = 'Bearer alinechribeiro:protected';
		$parameters = [
			'id'       => '5',
			'forename' => 'Joaquim'
		];
		$response = $this->json('POST', '/api/updateuser/', $parameters, ['HTTP_Authorization' => $token]);
		$this->assertEquals(200, $this->response->status());
	}
}
