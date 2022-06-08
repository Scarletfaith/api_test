<?php

namespace App\Http\Requests\Api;

use App\Contracts\Api\CreateTaskModelInterface;
use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest implements CreateTaskModelInterface
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
     * @return array
     */
    public function rules()
    {
        return [
            'status' => ['nullable', 'string'],
            'priority' => ['required', 'integer'],
            'title' => ['required', 'string'],
            'description' => ['required', 'string'],
            'parent_id' => ['required', 'integer'],
            'user_id' => ['required', 'integer'],
            'finished_at' => ['nullable', 'string']
        ];
    }

    public function getStatus(): string
    {
        return $this->input('status');
    }

    public function getPriority(): int
    {
        return $this->input('priority');
    }

    public function getTitle(): string
    {
        return $this->input('title');
    }

    public function getDescription(): string
    {
        return $this->input('description');
    }

    public function getParentId(): int
    {
        return $this->input('parent_id');
    }

    public function getUserId(): int
    {
        return $this->input('user_id');
    }

    public function getFinished(): ?string
    {
        return $this->input('finished_at');
    }
}
