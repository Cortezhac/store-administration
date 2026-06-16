<?php

namespace Modules\Category\Services;

use Illuminate\Support\Facades\Storage;
use Modules\Category\Forms\CreateForm;
use Modules\Category\Models\Category;

class CRUDService
{
    public function create(CreateForm $form, ?string $iconPath = null): Category
    {
        if ($iconPath) {
            $form->icon = $iconPath;
        }

        return Category::create($form->all());
    }

    public function update(Category $category, CreateForm $form, ?string $iconPath = null, bool $removeIcon = false): Category
    {
        $data = $form->except('categoryId');

        if ($iconPath !== null || $removeIcon) {
            if ($category->icon) {
                Storage::disk('local')->delete($category->icon);
            }

            $data['icon'] = $removeIcon ? null : $iconPath;
        } else {
            // Keep existing icon — exclude from data to avoid overwriting with null
            unset($data['icon']);
        }

        $category->update($data);

        return $category;
    }

    public function delete(Category $category): bool
    {
        if ($category->icon) {
            Storage::disk('local')->delete($category->icon);
        }

        return $category->delete();
    }
}
