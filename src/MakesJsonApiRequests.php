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

trait MakesJsonApiRequests
{

    /**
     * Test a JSON API URI.
     *
     * @param string|null $expects
     *      the expected resource type.
     * @return TestBuilder
     */
    protected function jsonApi(string $expects = null): TestBuilder
    {
        $tester = new TestBuilder($this);

        if ($expects) {
            $tester->expects($expects);
        }

        return $tester;
    }

}
