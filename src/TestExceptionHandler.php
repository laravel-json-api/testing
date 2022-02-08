<?php
/*
 * Copyright 2022 Cloud Creativity Limited
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace LaravelJsonApi\Testing;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use LaravelJsonApi\Exceptions\ExceptionParser;

/**
 * Class TestExceptionHandler
 *
 * This exception handler is intended for testing JSON API packages
 * using the `orchestra/testbench` package. It ensures that JSON
 * API exceptions are rendered as they would in a "real" application
 * that has added the ExceptionParser renderer to their
 * rendering stack.
 *
 * Usage in a testbench test case is as follows:
 *
 * ```php
 * protected function resolveApplicationExceptionHandler($app)
 * {
 *   $app->singleton(
 *      \Illuminate\Contracts\Debug\ExceptionHandler::class,
 *      \LaravelJsonApi\Testing\TestExceptionHandler::class
 *   );
 * }
 * ```
 *
 */
class TestExceptionHandler extends ExceptionHandler
{

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(ExceptionParser::renderer());
    }

}
