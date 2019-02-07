# laravel-action [Русский](https://github.com/slexx1234/laravel-actions/blob/master/RU.md)

## Usage

### Problem

Most applications have multiple entry points. For example, a form for creating a user, an 
API for a mobile application, and there may even be a command for creating users.

Then there is the problem of repeating pieces of code.

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

I recently discovered an elegant solution to this problem by reading an article on [medium](https://medium.com/@remi_collin/keeping-your-laravel-applications-dry-with-single-action-classes-6a950ec54d1d).

Repetition is removed by adding new class:

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

### So what does the package do?

It adds one irreplaceable command `make:action`!

```
php artisan make:action Users/CreateUserAction
```

This generates the following code:

```php
<?php

namespace App\Actions\Users;

use App\User;
use Slexx\LaravelActions\Action;

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

You can install this package via composer:

```
composer require slexx/laravel-actions
```

After updating composer, add the service provider to the `providers` array in `config/app.php`

```
Slexx\LaravelActions\ActionsServiceProvider::class,
```

