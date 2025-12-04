# Admin Feature Setup Guide

## Overview

The admin feature allows you to designate specific users as admins. Admins will have access to an "Admin Dashboard" option in their profile dropdown.

## Setup Instructions

### 1. Set Admin Key in `.env`

Add a strong admin key to your `.env` file:

```env
ADMIN_KEY=your_super_secret_admin_key_here
```

Example:

```env
ADMIN_KEY=sk_admin_zanify_2025_secure_key
```

### 2. Run Database Migration

Run the migration to add the `admin_key` column to the users table:

```bash
php artisan migrate
```

### 3. Grant Admin Access to a User

To make a user an admin, update their `admin_key` in the database to match the `ADMIN_KEY` in `.env`:

**Option A: Using Tinker (Interactive)**

```bash
php artisan tinker
```

Then in the Tinker shell:

```php
$user = User::find(1); // Replace 1 with the user ID
$user->admin_key = config('app.admin_key');
$user->save();
```

**Option B: Using SQL**

```sql
UPDATE users SET admin_key = 'your_super_secret_admin_key_here' WHERE id = 1;
```

**Option C: During User Registration (if needed)**
When creating a user programmatically:

```php
User::create([
    'name' => 'Admin User',
    'email' => 'admin@example.com',
    'password' => Hash::make('password'),
    'admin_key' => config('app.admin_key'), // This makes them an admin
]);
```

## How It Works

1. When a user logs in, the navbar checks if they are an admin
2. If `user->admin_key === config('app.admin_key')` AND both are not empty, they are considered an admin
3. Admins will see the "Admin Dashboard" option in their profile dropdown menu
4. The admin key is stored in the `admin_key` column of the users table

## Security Notes

-   Keep your `ADMIN_KEY` in `.env` file (should be added to `.gitignore`)
-   Use a strong, unique key for `ADMIN_KEY`
-   Don't commit `.env` to version control
-   Only assign admin status to trusted users

## How to Revoke Admin Access

To remove admin status from a user:

```bash
php artisan tinker
```

```php
$user = User::find(1);
$user->admin_key = null;
$user->save();
```

Or via SQL:

```sql
UPDATE users SET admin_key = NULL WHERE id = 1;
```
