<?php

namespace HeadlessLaravel\Formations\Tests;

use HeadlessLaravel\Formations\Http\Controllers\Controller;
use HeadlessLaravel\Formations\Manager;
use HeadlessLaravel\Formations\Tests\Fixtures\Models\Post;
use HeadlessLaravel\Formations\Tests\Fixtures\PostFormation;
use HeadlessLaravel\Formations\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ResourceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->authUser();

        config()->set('formations.mode', 'api');
    }

    public function test_resource_singleton()
    {
        $resources = app(Manager::class)->all();

        $this->assertEquals('posts', $resources[0]['resource']);
        $this->assertEquals(PostFormation::class, $resources[0]['formation']);
    }

    public function test_resource_terms()
    {
        $controller = app(Controller::class);
        $controller->current['resource'] = 'product-lines';

        $this->assertEquals('ProductLine', $controller->terms('resource.studly'));
        $this->assertEquals('ProductLines', $controller->terms('resource.studlyPlural'));
        $this->assertEquals('product_line', $controller->terms('resource.snake'));
        $this->assertEquals('product_lines', $controller->terms('resource.snakePlural'));
        $this->assertEquals('product-line', $controller->terms('resource.slug'));
        $this->assertEquals('product-lines', $controller->terms('resource.slugPlural'));
        $this->assertEquals('productLine', $controller->terms('resource.camel'));
        $this->assertEquals('productLines', $controller->terms('resource.camelPlural'));
    }

    public function test_indexing_a_resource()
    {
        $post = Post::factory()->create();

        $this->get('posts')
            ->assertOk()
            ->assertJsonCount(1, 'posts')
            ->assertJsonPath('posts.0.id', $post->id);
    }

    public function test_searching_a_resource_index()
    {
        Post::factory()->create();

        $post = Post::factory()->create(['title' => 'Find me']);

        $this->get('posts?search=find')
            ->assertOk()
            ->assertJsonCount(1, 'posts')
            ->assertJsonPath('posts.0.id', $post->id);
    }

    public function test_creating_a_resource()
    {
        $this->get('posts/new')
            ->assertOk()
            ->assertJsonPath('extra', 'populated from extra method');
    }

    public function test_storing_a_resource()
    {
        $this->post('posts/new', [
            'title' => 'Blog title',
        ]);

        $this->assertEquals('Blog title', Post::first()->title);
    }

    public function test_showing_a_resource()
    {
        $post = Post::factory()->create();

        $this->get("posts/$post->id")
            ->assertOk()
            ->assertJsonPath('post.id', $post->id);
    }

    public function test_showing_a_deleted_resource()
    {
        $post = Post::factory()->create();

        $post->delete();

        $this->get("posts/$post->id")
            ->assertOk()
            ->assertJsonPath('post.id', $post->id);
    }

    public function test_editing_a_resource()
    {
        $post = Post::factory()->create();

        $this->get("posts/$post->id/edit")
            ->assertOk()
            ->assertJsonPath('post.id', $post->id)
            ->assertJsonPath('override', 'populated from override method');
    }

    public function test_updating_a_resource()
    {
        $post = Post::factory()->create();

        $this->put("posts/$post->id/edit", [
            'title' => 'new title goes here',
        ])->assertOk();

        $this->assertEquals(
            'new title goes here',
            $post->fresh()->title
        );
    }

    public function test_deleting_a_resource()
    {
        $post = Post::factory()->create();

        $this->delete("posts/$post->id")->assertOk();

        $this->assertCount(0, Post::all());
        $this->assertCount(1, Post::withTrashed()->get());
    }

    public function test_restoring_a_resource()
    {
        $post = Post::factory()->create();

        $post->delete();

        $this->put("posts/$post->id/restore")->assertOk();

        $this->assertCount(1, Post::all());
    }

    public function test_force_deleting_a_resource()
    {
        $post = Post::factory()->create();

        $post->delete();

        $this->delete("/posts/$post->id/force-delete")->assertOk();

        $this->assertEquals(0, Post::withTrashed()->count());
    }
}
