<?php

namespace App\Services\Auth;

use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;

class JsonGuard implements Guard {
  protected $request;
  protected $provider;
  protected $user;

  /**
   * Create a new authentication guard.
   */
  public function __construct(UserProvider $provider, Request $request) {
    $this->request = $request;
    $this->provider = $provider;
    $this->user = NULL;
  }

  /**
   * Determine if the current user is authenticated.
   */
  public function check() {
    //
  }

  /**
   * Determine if the current user is a guest.
   */
  public function guest() {
    //
  }

  /**
   * Get the currently authenticated user.
   */
  public function user() {
    if (!is_null($this->user)) {
      return $this->user;
    }
  }

  /**
   * Get the JSON params from the current request
   */
  public function getJsonParams() {
    //
  }

  /**
   * Get the ID for the currently authenticated user.
   */
  public function id() {
    //
  }

  /**
   * Validate a user's credentials.
   */
  public function validate(array $credentials = []) {
    //
  }

  /**
   * Set the current user.
   */
  public function setUser($user) {
    $this->user = $user;

    return $this;
  }

  /**
   * Determine if the guard has a user instance.
   */
  public function hasUser() {
  }
}
