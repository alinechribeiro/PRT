<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;

class UserController extends Controller {
	/**
	 * In order for this to work on the header authorization field set a Bearer token in the format username:token
	 * Token value should be set during the endpoint. It is recommended the server to be https to send the header encrypted.
	 *
	 **/
	public function isAuthorizedAdmin($token) {
		// check if username provided by token is authorized to create user / exists on the database
		$auth_fields   = explode(':', $token);
		$auth_username = $auth_fields[0];
		$auth_pwd      = $auth_fields[1];
		// returns array. It would be the admin user with permission to create other users
		$user_authorized = User::where([
				['username', '=', $auth_username],
				['token', '=', $auth_pwd],
			])->get();

		if (count($user_authorized) == 0) {
			return false;
		} else {
			return true;
		}
	}
	public function showAllUsers(Request $request) {
		// retrieve username:token
		$token = $request->bearerToken();
		// check if admin user is authorized
		if ($this->isAuthorizedAdmin($token)) {
			// check if $id is on database
			return response()->json(User::all());
		} else {
			// if token not valid, admin user not authorized. Return error message and status
			return response('User not authorized', 404);
		}
	}

	public function create(Request $request) {
		// retrieve username:token
		$token = $request->bearerToken();
		// check if admin user is authorized
		if ($this->isAuthorizedAdmin($token)) {
			// validate fields from request
			$this->validate($request, [
					'forename' => 'required|min:1|max:50',
					'surname'  => 'required|min:1|max:50',
					'username' => 'required|unique:users|min:6|max:20',
					'darkmode' => 'required',
					'token'    => 'required|min:8'
				]);
			// create user
			$user = User::create($request->all());

			// return json data and status
			return response()->json($user, 201);
		} else {
			// if token not valid, admin user not authorized. Return error message and status
			return response('User not authorized', 404);
		}
	}

	public function update(Request $request) {
		// retrieve username:token
		$token = $request->bearerToken();
		// check if admin user is authorized
		if ($this->isAuthorizedAdmin($token)) {
			// retrieve and validate fields where id is required from request
			$this->validate($request, ['id' => 'required']);
			$id = $request->get('id');
			// update forename only based on the $id
			$forename       = $request->get('forename');
			$user           = User::findOrFail($id);
			$user->forename = $forename;
			$user->save();
			// return user data and status
			return response()->json($user, 200);
		} else {
			// if token not valid, admin user not authorized. Return error message and status
			return response('User not authorized', 404);
		}
	}
	// This method gets the darkmode based on $id of the user and toggle
	public function toggleDarkmode($id, Request $request) {
		// retrieve username:token
		$token = $request->bearerToken();
		// check if admin user is authorized
		if ($this->isAuthorizedAdmin($token)) {
			// check if $id is on database
			$user = User::findOrFail($id);
			// toggle darkmode
			$user->darkmode = ($user->darkmode == 0)?1:0;
			$user->save();

			return response()->json($user, 201);
		} else {
			// if token not valid, admin user not authorized. Return error message and status
			return response('User not authorized', 404);
		}
	}

	public function delete($id, Request $request) {
		// retrieve username:token
		$token = $request->bearerToken();
		// check if admin user is authorized
		if ($this->isAuthorizedAdmin($token)) {
			// check if $id is on database
			$user = User::findOrFail($id);
			$user->delete();
			return response('Deleted Successfully', 200);
		} else {
			// if token not valid, admin user not authorized. Return error message and status
			return response('User not authorized', 404);
		}
	}

	// Reusable method for the existing endpoint
	// This method tests if the request path is for User search and reuses all the components as much as possible.
	public function getUserRequest(Request $request) {
		// retrieve username:token
		$token = $request->bearerToken();
		// check if admin user is authorized
		if ($this->isAuthorizedAdmin($token)) {
			/* The following fields are mandatory to be sent on the request.
			 *  identifier: string. > Column to be searched e.g.: 'forename'.
			 *  identifierField: string. > Value to search for e.g.: 'Tom'.
			 *  fields: array. > Which fields will be returned on the response e.g.: ['surname', 'darkmode'] or use ['*'] for all.
			 */
			$requiredFields = ['identifier' => 'required', 'identifierField' => 'required', 'fields' => 'required'];

			// Validates if required fields are present in the request
			$this->validate($request, $requiredFields);

			// Populating the request fields into the variables for code clarity.
			$identifier      = $request->get('identifier');
			$identifierField = $request->get('identifierField');
			$fields          = $request->get('fields');
			$path            = $request->path();

			// Create the User object.
			$obj = new User;

			// Get all the fields from the Model based on the object.
			$filterColumns = $obj->getFillable();

			// Initialization
			$status = 'not found';
			$data   = [];

			// Loop through the fillable values and perform wildcard search in the DB field.
			foreach ($filterColumns as $column) {
				if ($identifier == $column) {
					$value = $identifierField;

					$data = $obj::where($column, 'LIKE', "%{$value}%")->get($fields);

					// Change status if data lenght > 0
					if (count($data) > 0) {
						$status = 'found';
					}

					// Return status and data as per the design.
					return response()->json([
							'status' => $status,
							'data'   => $data
						]);
				}
			}

			// if request field was not found among the filter columns
			return response()->json([
					'status' => $status,
					'data'   => []
				]);
		} else {
			// if token not valid, admin user not authorized. Return error message and status
			return response('User not authorized', 404);
		}
	}
}