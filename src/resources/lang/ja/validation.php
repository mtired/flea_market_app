<?php

return [

/*** バリデーションエラーの共通メッセージ定義 ***/

    'required' => ':attribute を入力してください',
    'email' => ':attribute はメール形式で入力してください',
    'min' => [
        'string' => ':attribute は :min 文字以上で入力してください',
    ],
    'max' => [
        'string' => ':attribute は :max 文字以下で入力してください',
    ],
    'unique' => 'この :attribute は既に使われています',


/*** 特定フィールド × ルールの個別指定（必要になったら追加） ***/
    'custom' => [
        'password.confirmed' => '確認用パスワードと一致しません'
    ],

/*** フィールド名を日本語に変換 ***/
    'attributes' => [
        'name' => 'お名前',
        'email' => 'メールアドレス',
        'password' => 'パスワード',
        'password_confirmation' => '確認用パスワード',
    ],
];