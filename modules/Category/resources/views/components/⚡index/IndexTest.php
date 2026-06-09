<?php

use App\Models\User;
use Livewire\Livewire;
use Modules\Category\Models\Category;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render the index page', function () {
    actingAs($this->user)
        ->get(route('category.index'))
        ->assertOk();
});

it('can list categories', function () {
    $categories = Category::factory()->count(3)->create();

    actingAs($this->user);

    Livewire::test('category::index')
        ->assertSee($categories[0]->name)
        ->assertSee($categories[1]->name)
        ->assertSee($categories[2]->name);
});

it('can search categories', function () {
    Category::factory()->create(['name' => 'Electronics']);
    Category::factory()->create(['name' => 'Clothing']);

    actingAs($this->user);

    Livewire::test('category::index')
        ->set('search', 'Electronics')
        ->assertSee('Electronics')
        ->assertDontSee('Clothing');
});

it('can filter active only', function () {
    Category::factory()->create(['name' => 'Active Category', 'is_active' => true]);
    Category::factory()->create(['name' => 'Inactive Category', 'is_active' => false]);

    actingAs($this->user);

    Livewire::test('category::index')
        ->set('activeOnly', true)
        ->assertSee('Active Category')
        ->assertDontSee('Inactive Category');
});

it('can delete a category', function () {
    $category = Category::factory()->create();

    actingAs($this->user);

    Livewire::test('category::index')
        ->call('delete', $category->id)
        ->assertRedirect(route('category.index'));

    expect(Category::find($category->id))->toBeNull();
});
