<?php

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Modules\Category\Models\Category;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('can render the create form', function () {
    actingAs($this->user)
        ->get(route('category.create'))
        ->assertOk();
});

it('can render the edit form', function () {
    actingAs($this->user);

    $category = Category::factory()->create();

    actingAs($this->user)
        ->get(route('category.edit', $category))
        ->assertOk();
});

it('can create a category without icon', function () {
    actingAs($this->user);

    Livewire::test('category::form')
        ->set('name', 'Electronics')
        ->set('slug', 'electronics')
        ->set('description', 'All electronic items')
        ->set('sort_order', 1)
        ->call('save')
        ->assertRedirect(route('category.index'));

    expect(Category::where('slug', 'electronics')->exists())->toBeTrue();
});

it('can create a category with icon', function () {
    Storage::fake('local');

    actingAs($this->user);

    $file = UploadedFile::fake()->image('icon.png', 32, 32);

    Livewire::test('category::form')
        ->set('name', 'Electronics')
        ->set('slug', 'electronics')
        ->set('iconUpload', $file)
        ->call('save')
        ->assertRedirect(route('category.index'));

    $category = Category::where('slug', 'electronics')->first();

    expect($category)->not->toBeNull();
    expect($category->icon)->not->toBeNull();
    Storage::disk('local')->assertExists($category->icon);
});

it('can edit a category and upload new icon', function () {
    Storage::fake('local');

    actingAs($this->user);

    $category = Category::factory()->create();

    $file = UploadedFile::fake()->image('new-icon.png', 32, 32);

    Livewire::test('category::form', ['category' => $category])
        ->set('name', 'Updated Name')
        ->set('iconUpload', $file)
        ->call('save')
        ->assertRedirect(route('category.index'));

    $category->refresh();

    expect($category->name)->toBe('Updated Name');
    expect($category->icon)->not->toBeNull();
    Storage::disk('local')->assertExists($category->icon);
});

it('can remove icon from category', function () {
    Storage::fake('local');

    actingAs($this->user);

    $file = UploadedFile::fake()->image('icon.png', 32, 32);
    $path = Storage::disk('local')->putFile('categories', $file);

    $category = Category::factory()->create(['icon' => $path]);

    Livewire::test('category::form', ['category' => $category])
        ->call('removeIconAction')
        ->call('save')
        ->assertRedirect(route('category.index'));

    $category->refresh();

    expect($category->icon)->toBeNull();
    Storage::disk('local')->assertMissing($path);
});

it('validates required fields', function () {
    actingAs($this->user);

    Livewire::test('category::form')
        ->set('name', '')
        ->set('slug', '')
        ->call('save')
        ->assertHasErrors(['name', 'slug']);
});

it('validates icon max size', function () {
    Storage::fake('local');

    actingAs($this->user);

    $file = UploadedFile::fake()->image('big-icon.png')->size(2048); // 2 MB

    Livewire::test('category::form')
        ->set('name', 'Test')
        ->set('slug', 'test')
        ->set('iconUpload', $file)
        ->call('save')
        ->assertHasErrors(['iconUpload']);
});

it('validates slug uniqueness on create', function () {
    actingAs($this->user);

    Category::factory()->create(['slug' => 'existing-slug']);

    Livewire::test('category::form')
        ->set('name', 'Test')
        ->set('slug', 'existing-slug')
        ->call('save')
        ->assertHasErrors(['slug']);
});

it('validates slug uniqueness on edit (allows own slug)', function () {
    actingAs($this->user);

    $category = Category::factory()->create(['slug' => 'my-slug']);

    Livewire::test('category::form', ['category' => $category])
        ->set('name', 'Updated')
        ->set('slug', 'my-slug')
        ->call('save')
        ->assertRedirect(route('category.index'));
});

it('prevents self-referencing parent', function () {
    actingAs($this->user);

    $category = Category::factory()->create();

    Livewire::test('category::form', ['category' => $category])
        ->set('name', 'Test')
        ->set('slug', 'test')
        ->set('parent_id', $category->id)
        ->call('save')
        ->assertHasErrors(['parent_id']);
});

it('can access icon via route', function () {
    Storage::fake('local');

    actingAs($this->user);

    $file = UploadedFile::fake()->image('icon.png', 32, 32);
    $path = Storage::disk('local')->putFile('categories', $file);

    $category = Category::factory()->create(['icon' => $path]);

    actingAs($this->user)
        ->get(route('category.icon', $category))
        ->assertOk()
        ->assertHeader('Content-Type', 'image/png');
});

it('returns 404 when icon does not exist', function () {
    actingAs($this->user);

    $category = Category::factory()->create(['icon' => 'non-existent.png']);

    actingAs($this->user)
        ->get(route('category.icon', $category))
        ->assertNotFound();
});

it('returns 404 when category has no icon', function () {
    actingAs($this->user);

    $category = Category::factory()->create(['icon' => null]);

    actingAs($this->user)
        ->get(route('category.icon', $category))
        ->assertNotFound();
});
