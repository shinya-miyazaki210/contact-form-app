<?php

namespace App\Http\Requests\Api\V1;

class StoreContactRequest extends \App\Http\Requests\StoreContactRequest
{
    public function messages(): array
    {
        return array_merge(parent::messages(), [
            'gender.in' => '性別の値が不正です',
            'tel.regex' => '電話番号はハイフンなしの10〜11桁で入力してください',
            'category_id.exists' => '選択されたカテゴリが存在しません',
            'tag_ids.*.exists' => '選択されたタグが存在しません',
        ]);
    }
}
