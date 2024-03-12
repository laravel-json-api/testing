<?php
/*
 * Copyright 2024 Cloud Creativity Limited
 *
 * Use of this source code is governed by an MIT-style
 * license that can be found in the LICENSE file or at
 * https://opensource.org/licenses/MIT.
 */

declare(strict_types=1);

namespace LaravelJsonApi\Testing\Tests\Unit;

use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Foundation\Testing\Concerns\MakesHttpRequests;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Testing\TestResponse as IlluminateTestResponse;
use LaravelJsonApi\Testing\TestBuilder;
use LaravelJsonApi\Testing\TestResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MockTestCase extends TestCase
{
    use MakesHttpRequests;
}

class TestBuilderTest extends TestCase
{

    /**
     * @var MockObject|MockTestCase
     */
    private MockObject $mock;

    /**
     * @var TestBuilder
     */
    private TestBuilder $builder;

    /**
     * @var Response|MockObject
     */
    private Response $response;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->mock = $this->createMock(MockTestCase::class);
        $this->builder = new TestBuilder($this->mock);
        $this->response = $this->createMock(Response::class);
    }

    public function testGet(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', '/api/v1/posts', [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->get('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testPost(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $data = [
            'type' => 'posts',
            'attributes' => [
                'content' => '...',
                'slug' => 'hello-world',
                'title' => 'Hello World!',
            ],
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('POST', '/api/v1/posts', compact('data'), $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withData($data)
            ->post('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testPatch(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $data = [
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'content' => '...',
                'slug' => 'hello-world',
                'title' => 'Hello World!',
            ],
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('PATCH', '/api/v1/posts', compact('data'), $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withData($data)
            ->patch('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testPut(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $data = [
            'type' => 'posts',
            'id' => '123',
            'attributes' => [
                'content' => '...',
                'slug' => 'hello-world',
                'title' => 'Hello World!',
            ],
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('PUT', '/api/v1/posts', compact('data'), $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withData($data)
            ->put('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testDelete(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('DELETE', '/api/v1/posts', [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->delete('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * Test using data that is JSON serializable.
     *
     * When the data is JSON serializable, we just pass it through as we expect it to be
     * JSON encoded by the test request.
     *
     * @return void
     */
    public function testWithJsonSerializableData(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $mock = $this->createMock(\JsonSerializable::class);
        $mock->method('jsonSerialize')->willReturn([
            'type' => 'posts',
            'attributes' => [
                'content' => '...',
                'slug' => 'hello-world',
                'title' => 'Hello World!',
            ],
        ]);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('POST', '/api/v1/posts', ['data' => $mock], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withData($mock)
            ->post('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testWithIterableThatIsNotJsonSerializable(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $object = new class implements \IteratorAggregate {
            public function getIterator(): \Generator
            {
                yield from [
                    ['type' => 'posts', 'id' => '1'],
                ];
            }
        };

        $expected = Collection::make($object)->toArray();

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('POST', '/api/v1/posts', ['data' => $expected], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withData($object)
            ->post('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testWithNullData(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $data = null;

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('POST', '/api/v1/posts', compact('data'), $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withData($data)
            ->post('/api/v1/posts', ['X-Foo' => 'Bar']);

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testWithHeader(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', '/api/v1/posts', [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withHeader('X-Foo', 'Bar')
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testWithHeaders(): void
    {
        $headers = [
            'X-Foo' => 'Bar',
            'X-Bat' => 'Baz',
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', '/api/v1/posts', [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withHeaders(['X-Foo' => 'Bar', 'X-Bat' => 'Baz'])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testWithJson(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $json = ['meta' => ['foo' => 'bar']];

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('POST', '/api/v1/posts', $json, $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->withJson($json)
            ->post('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testWithPayload(): void
    {
        $payload = ['foo' => 'bar', 'baz' => 'bat'];

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/x-www-form-urlencoded',
            'CONTENT_LENGTH' => mb_strlen(Arr::query($payload), '8bit'),
        ];

        $this->mock
            ->expects($this->once())
            ->method('post')
            ->with('/api/v1/posts', $payload, $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->asFormUrlEncoded()
            ->withPayload($payload)
            ->post('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testContentType(): void
    {
        $payload = ['foo' => 'bar', 'baz' => 'bat'];

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'multipart/form-data; boundary=----WebKitFormBoundary' . Str::random(10),
            'CONTENT_LENGTH' => mb_strlen(Arr::query($payload), '8bit'),
        ];

        $this->mock
            ->expects($this->once())
            ->method('patch')
            ->with('/api/v1/posts/123', $payload, $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->contentType($headers['CONTENT_TYPE'])
            ->withPayload($payload)
            ->patch('/api/v1/posts/123');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testMultiPartFormData(): void
    {
        $payload = ['foo' => 'bar', 'baz' => 'bat'];

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_LENGTH' => mb_strlen(Arr::query($payload), '8bit'),
        ];

        $cb = $this->callback(function (array $actual) use ($headers) {
            $this->assertArrayHasKey('CONTENT_TYPE', $actual);
            $this->assertStringStartsWith('multipart/form-data; boundary=----WebKitFormBoundary', $actual['CONTENT_TYPE']);
            unset($actual['CONTENT_TYPE']);
            $this->assertEquals($headers, $actual);
            return true;
        });

        $this->mock
            ->expects($this->once())
            ->method('put')
            ->with('/api/v1/posts/123', $payload, $cb)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->asMultiPartFormData()
            ->withPayload($payload)
            ->put('/api/v1/posts/123');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testQuery(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'include' => 'author,tags',
            'page' => ['number' => '1', 'size' => '10'],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->query($query)
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testIncludePaths(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'include' => 'author,tags',
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->includePaths('author', 'tags')
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testSparseFields(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'fields' => [
                'posts' => 'title,slug,author',
                'users' => 'name',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->sparseFields('posts', ['title', 'slug', 'author'])
            ->sparseFields('users', ['name'])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testFilter(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'filter' => [
                'published' => 'true',
                'foo' => 'bar',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->filter(['published' => 'true', 'foo' => 'bar'])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @return array
     */
    public static function idProvider(): array
    {
        return [
            'int' => [static fn() => 1],
            'string' => [static fn() => '1'],
            'model' => [static function ($test) {
                $mock = $test->createMock(UrlRoutable::class);
                $mock->method('getRouteKey')->willReturn(1);
                return $mock;
            }],
        ];
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idProvider
     */
    public function testFilterId(\Closure $scenario): void
    {
        $value = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'filter' => [
                'published' => 'true',
                'author' => '1',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->filter(['published' => 'true', 'author' => $value])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idProvider
     */
    public function testFilterIdInQuery(\Closure $scenario): void
    {
        $value = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'filter' => [
                'published' => 'true',
                'author' => '1',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->query(['filter' => ['published' => 'true', 'author' => $value]])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @return array
     */
    public static function idsProvider(): array
    {
        return [
            'integers' => [static fn() => [1, 2, 3]],
            'strings' => [static fn() => ['1', '2', '3']],
            'models' => [
                static function ($test) {
                    $model1 = $test->createMock(UrlRoutable::class);
                    $model1->method('getRouteKey')->willReturn(1);

                    $model2 = $test->createMock(UrlRoutable::class);
                    $model2->method('getRouteKey')->willReturn(2);

                    $model3 = $test->createMock(UrlRoutable::class);
                    $model3->method('getRouteKey')->willReturn(3);

                    return [$model1, $model2, $model3];
                },
            ],
        ];
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idsProvider
     */
    public function testFilterIds(\Closure $scenario): void
    {
        $values = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'filter' => [
                'published' => 'true',
                'id' => ['1', '2', '3'],
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->filter(['published' => 'true', 'id' => $values])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idsProvider
     */
    public function testFilterIdsWithCollection(\Closure $scenario): void
    {
        $values = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'filter' => [
                'published' => 'true',
                'ids' => ['1', '2', '3'],
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->filter(['published' => 'true', 'ids' => new Collection($values)])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idsProvider
     */
    public function testFilterIdsInQuery(\Closure $scenario): void
    {
        $values = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'filter' => [
                'published' => 'true',
                'ids' => ['1', '2', '3'],
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->query(['filter' => ['published' => 'true', 'ids' => $values]])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testSort(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'sort' => '-publishedAt,title',
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->sort('-publishedAt', 'title')
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    public function testPage(): void
    {
        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'page' => [
                'number' => '2',
                'size' => '10',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->page(['number' => '2', 'size' => '10'])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idProvider
     */
    public function testPageId(\Closure $scenario): void
    {
        $value = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'page' => [
                'after' => '1',
                'size' => '10',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->page(['after' => $value, 'size' => '10'])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }

    /**
     * @param \Closure $scenario
     * @return void
     * @dataProvider idProvider
     */
    public function testPageIdInQuery(\Closure $scenario): void
    {
        $value = $scenario($this);

        $headers = [
            'Accept' => 'application/vnd.api+json',
            'CONTENT_TYPE' => 'application/vnd.api+json',
        ];

        $query = [
            'page' => [
                'after' => '1',
                'size' => '10',
            ],
        ];

        $expected = '/api/v1/posts?' . Arr::query($query);

        $this->mock
            ->expects($this->once())
            ->method('json')
            ->with('GET', $expected, [], $headers)
            ->willReturn(new IlluminateTestResponse($this->response));

        $response = $this->builder
            ->expects('posts')
            ->query(['page' => ['after' => $value, 'size' => '10']])
            ->get('/api/v1/posts');

        $this->assertEquals(new TestResponse($this->response, 'posts'), $response);
    }
}
