<?php
namespace Puzzle\UserBundle;

final class UserEvents
{
    const USER_CREATE = 'user.create';
    const USER_PASSWORD = 'user.update_password';
	
	const ADMIN_USER_CREATE = 'admin.user.create';
	const ADMIN_USER_UPDATE = 'admin.user.update';
	const ADMIN_USER_REMOVE = 'admin.user.remove';
}