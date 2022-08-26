<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Arr;
use PhpParser\ErrorHandler\Collecting;

class ProductsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        $model = $this->route('product');
        return [
            'name' => ['bail', 'required', 'string', 'max:255'],
            'slug' => ['bail', 'required', 'string', 'max:255', Rule::unique(Product::class)->ignore($model->id ?? null)],
            'description' => ['bail', 'required', 'string'],
            'costPrice' => ['bail', 'required', 'integer'],
            'price' => ['bail', 'required', 'integer'],
            'featured' => ['bail','boolean'],
            'showOnSlider' => ['bail', 'boolean'],
            'active' => ['bail', 'boolean'],
            'categoryId' => ['bail','required', Rule::exists(Category::class, 'id')->whereNull('parent_id')],
            'subCategoryId' => ['bail', 'nullable', Rule::exists(Category::class, 'id')->whereNotNull('parent_id')],
        ];
    }

    public function saveData(): array
    {
        $data = $this->safe()->only(['name', 'slug', 'description', 'price', 'featured', 'active']);

        $data['cost_price'] = $this->costPrice;
        $data['show_on_slider'] = $this->showOnSlider;

        return $data;
    }

    public function categoryIds(): Collection
    {
        return collect([$this->categoryId, $this->subCategoryId])->filter()->values();
    }
}
