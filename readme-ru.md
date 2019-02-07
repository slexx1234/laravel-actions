# laravel-action [English](https://github.com/slexx1234/laravel-actions/blob/master/readme.md)

## Использование

### Проблема

Большинство приложений имеют несколько точек входа. Например, форма для создания пользователя,
API для мобильного приложения и даже может быть команда для создания пользователей.

Тогда возникает проблема повторения фрагментов кода.

```php
class UserController 
{
    public function create(CreateUserRequest $request) 
    {
        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        
        return redirect('users/' . $user->id);
    }
}


class UserApiController 
{
    public function create(CreateUserRequest $request) 
    {
        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        
        return new UserResponse($user);
    }
}
```

### Solution

Недавно я обнаружил элегантное решение этой проблемы, прочитав статью на [medium](https://medium.com/@remi_collin/keeping-your-laravel-applications-dry-with-single-action-classes-6a950ec54d1d).

Повторение удаляется добавлением нового класса:

```php
class UserCreateAction 
{
    public function execute(CreateUserRequest $request): User
    {
        $user = new User;
        $user->email = $request->email;
        $user->password = bcrypt($request->password);
        $user->save();
        
        return $user;
    }
}

class UserController 
{
    public function create(CreateUserRequest $request, CreateUserAction $action) 
    {
        return redirect('users/' . $action->execute($request)->id);
    }
}


class UserApiController 
{
    public function create(CreateUserRequest $request) 
    {
        return new UserResponse($action->execute($request));
    }
}
```

### Что же делает пакет?

Он добовляет одну комманду `make:action`!

```
php artisan make:action Users/CreateUserAction
```

Это сгенерирует следующий код:

```php
<?php

namespace App\Actions\Users;

use App\User;
use Slexx\Actions\Action;

class CreateUserAction extends Action
{
    /**
     * @param array $data
     * @return User
     */
    public function execute(array $data)
    {
        $user = User::create($data);

        return $user;
    }
}
```

## Install 

Установка через composer:

```
composer require slexx/laravel-actions
```

После обновления composer, добавте поставщика услуг в массив `providers` в файлу `config/app.php`

```
Slexx\Actions\ActionsServiceProvider::class,
```

