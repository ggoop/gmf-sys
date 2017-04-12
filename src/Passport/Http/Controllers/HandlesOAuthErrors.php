<?php

namespace Gmf\Sys\Passport\Http\Controllers;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Http\Response;
use League\OAuth2\Server\Exception\OAuthServerException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Throwable;
use Zend\Diactoros\Response as Psr7Response;

trait HandlesOAuthErrors {
	/**
	 * Perform the given callback with exception handling.
	 *
	 * @param  \Closure  $callback
	 * @return Response
	 */
	protected function withErrorHandling($callback) {
		try {
			return $callback();
		} catch (OAuthServerException $e) {
			$this->exceptionHandler()->report($e);

			return $e->generateHttpResponse(new Psr7Response);
		} catch (Exception $e) {
			$this->exceptionHandler()->report($e);

			return new Response($e->getMessage(), 500);
		} catch (Throwable $e) {
			$this->exceptionHandler()->report(new FatalThrowableError($e));

			return new Response($e->getMessage(), 500);
		}
	}

	/**
	 * Get the exception handler instance.
	 *
	 * @return \Illuminate\Contracts\Debug\ExceptionHandler
	 */
	protected function exceptionHandler() {
		return Container::getInstance()->make(ExceptionHandler::class);
	}
}
