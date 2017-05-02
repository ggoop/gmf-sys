<?php

namespace Gmf\Sys\Passport\Http\Controllers;

use Gmf\Sys\Http\Controllers\Controller;
use Gmf\Sys\Passport\Client;
use Gmf\Sys\Passport\ClientRepository;
use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ClientController extends Controller {
	/**
	 * The client repository instance.
	 *
	 * @var ClientRepository
	 */
	protected $clients;

	/**
	 * The validation factory implementation.
	 *
	 * @var ValidationFactory
	 */
	protected $validation;

	/**
	 * Create a client controller instance.
	 *
	 * @param  ClientRepository  $clients
	 * @param  ValidationFactory  $validation
	 * @return void
	 */
	public function __construct(ClientRepository $clients,
		ValidationFactory $validation) {
		$this->clients = $clients;
		$this->validation = $validation;
	}

	/**
	 * Get all of the clients for the authenticated user.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function forUser(Request $request) {
		$userId = $request->user()->getKey();

		return $this->clients->activeForUser($userId)->makeVisible('secret');
	}
	public function show(Request $request, $clientId) {
		$data = Client::find($clientId)->makeVisible('secret');
		return $this->toJson($data);
	}

	/**
	 * Store a new client.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request) {
		$validator = $this->validation->make($request->all(), [
			'name' => 'required|max:255',
			'redirect' => 'required|url',
		]);

		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}

		$data = $this->clients->create(
			$request->user()->getKey(), $request->name, $request->redirect
		)->makeVisible('secret');

		return $this->toJson($data);
	}

	/**
	 * Update the given client.
	 *
	 * @param  Request  $request
	 * @param  string  $clientId
	 * @return Response
	 */
	public function update(Request $request, $clientId) {
		if (!$request->user()->clients->find($clientId)) {
			return new Response('', 404);
		}

		$validator = $this->validation->make($request->all(), [
			'name' => 'required|max:255',
			'redirect' => 'required|url',
		]);

		if ($validator->fails()) {
			return $this->toError($validator->errors());
		}

		$data = $this->clients->update(
			$request->user()->clients->find($clientId),
			$request->name, $request->redirect
		);
		return $this->toJson($data);
	}

	/**
	 * Delete the given client.
	 *
	 * @param  Request  $request
	 * @param  string  $clientId
	 * @return Response
	 */
	public function destroy(Request $request, $clientId) {
		if (!$request->user()->clients->find($clientId)) {
			return new Response('', 404);
		}
		$this->clients->delete(
			$request->user()->clients->find($clientId)
		);
		return $this->toJson(true);
	}
}
