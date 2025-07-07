<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // Basic Information
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug',
            'sku' => 'nullable|string|max:100|unique:products,sku',
            'short_description' => 'nullable|string|max:500',
            'description' => 'nullable|string',
            
            // Categorization
            'product_category_id' => 'nullable|exists:product_categories,id',
            'service_id' => 'nullable|exists:services,id',
            'brand' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            
            // Pricing
            'price' => 'nullable|numeric|min:0|max:99999999999.99',
            'sale_price' => 'nullable|numeric|min:0|max:99999999999.99|lt:price',
            'currency' => 'nullable|string|max:3|in:IDR,USD,EUR',
            'price_type' => 'required|in:fixed,quote,contact',
            
            // Inventory
            'stock_quantity' => 'nullable|integer|min:0',
            'manage_stock' => 'nullable|boolean',
            'stock_status' => 'required|in:in_stock,out_of_stock,on_backorder',
            
            // Media
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048',
            
            // Specifications
            'specifications' => 'nullable|array',
            'specifications.*.name' => 'required_with:specifications|string|max:255',
            'specifications.*.value' => 'required_with:specifications|string|max:500',
            
            'technical_specs' => 'nullable|array',
            'technical_specs.*.name' => 'required_with:technical_specs|string|max:255',
            'technical_specs.*.value' => 'required_with:technical_specs|string|max:500',
            
            'dimensions' => 'nullable|array',
            'dimensions.length' => 'nullable|numeric|min:0',
            'dimensions.width' => 'nullable|numeric|min:0',
            'dimensions.height' => 'nullable|numeric|min:0',
            'dimensions.unit' => 'nullable|string|in:cm,m,mm,inch,ft',
            
            'weight' => 'nullable|numeric|min:0|max:99999.99',
            
            // Status & Settings
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            
            // SEO
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:255',
            'meta_keywords' => 'nullable|string|max:255',
            
            // Related Services
            'related_services' => 'nullable|array',
            'related_services.*' => 'exists:services,id',
            'service_relations' => 'nullable|array',
            'service_relations.*.service_id' => 'required_with:service_relations|exists:services,id',
            'service_relations.*.relation_type' => 'required_with:service_relations|in:compatible,recommended,required',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'slug.unique' => 'This URL slug is already taken.',
            'sku.unique' => 'This SKU is already taken.',
            'sku.max' => 'SKU cannot exceed 100 characters.',
            'product_category_id.exists' => 'Selected category is invalid.',
            'service_id.exists' => 'Selected service is invalid.',
            'price.numeric' => 'Price must be a valid number.',
            'price.min' => 'Price cannot be negative.',
            'price.max' => 'Price is too large.',
            'sale_price.lt' => 'Sale price must be less than regular price.',
            'currency.in' => 'Currency must be IDR, USD, or EUR.',
            'price_type.required' => 'Price type is required.',
            'price_type.in' => 'Price type must be fixed, quote, or contact.',
            'stock_quantity.integer' => 'Stock quantity must be a whole number.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'stock_status.required' => 'Stock status is required.',
            'stock_status.in' => 'Stock status must be in_stock, out_of_stock, or on_backorder.',
            'featured_image.image' => 'Featured image must be a valid image file.',
            'featured_image.mimes' => 'Featured image must be: jpeg, png, jpg, or webp.',
            'featured_image.max' => 'Featured image size cannot exceed 2MB.',
            'gallery.*.image' => 'Gallery images must be valid image files.',
            'gallery.*.mimes' => 'Gallery images must be: jpeg, png, jpg, or webp.',
            'gallery.*.max' => 'Gallery image size cannot exceed 2MB.',
            'weight.numeric' => 'Weight must be a valid number.',
            'weight.min' => 'Weight cannot be negative.',
            'status.required' => 'Product status is required.',
            'status.in' => 'Product status must be draft, published, or archived.',
            'sort_order.integer' => 'Sort order must be a whole number.',
            'sort_order.min' => 'Sort order cannot be negative.',
            'meta_title.max' => 'Meta title cannot exceed 255 characters.',
            'meta_description.max' => 'Meta description cannot exceed 255 characters.',
            'meta_keywords.max' => 'Meta keywords cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'product_category_id' => 'category',
            'service_id' => 'primary service',
            'is_featured' => 'featured status',
            'is_active' => 'active status',
            'meta_title' => 'meta title',
            'meta_description' => 'meta description',
            'meta_keywords' => 'meta keywords',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert checkbox values to boolean
        $this->merge([
            'is_featured' => $this->boolean('is_featured'),
            'is_active' => $this->boolean('is_active', true), // Default to true
            'manage_stock' => $this->boolean('manage_stock'),
        ]);

        // Clean up empty values
        if ($this->product_category_id === '') {
            $this->merge(['product_category_id' => null]);
        }

        if ($this->service_id === '') {
            $this->merge(['service_id' => null]);
        }

        if ($this->sort_order === '') {
            $this->merge(['sort_order' => 0]);
        }

        // Set default status if not provided
        if (!$this->status) {
            $this->merge(['status' => 'draft']);
        }

        // Set default price type if not provided
        if (!$this->price_type) {
            $this->merge(['price_type' => 'fixed']);
        }

        // Set default currency
        if (!$this->currency) {
            $this->merge(['currency' => 'IDR']);
        }
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Validate price requirements based on price_type
            if ($this->price_type === 'fixed' && !$this->price) {
                $validator->errors()->add('price', 'Price is required when price type is fixed.');
            }

            // Validate stock management logic
            if ($this->manage_stock && !is_numeric($this->stock_quantity)) {
                $validator->errors()->add('stock_quantity', 'Stock quantity is required when managing stock.');
            }

            // Validate dimensions consistency
            $dimensions = $this->dimensions ?? [];
            $hasDimensions = !empty($dimensions['length']) || !empty($dimensions['width']) || !empty($dimensions['height']);
            
            if ($hasDimensions && empty($dimensions['unit'])) {
                $validator->errors()->add('dimensions.unit', 'Unit is required when dimensions are provided.');
            }
        });
    }
}