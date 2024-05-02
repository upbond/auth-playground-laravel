![Auth0 Laravel SDK](https://cdn.auth0.com/website/sdks/banners/laravel-auth0-banner.png)

:books: [Documentation](#documentation) — :rocket: [Getting Started](#getting-started) — :round_pushpin: [Routes](#demonstration-routes) — :wrench: [Default Changes](#changes-to-the-default-laravel-application)

This is a sample project demonstrating how to integrate [the Auth0 Laravel SDK](https://github.com/auth0/laravel-auth0) into a Laravel 9 application. For Laravel 10 applications, the integration steps are identical.

## Documentation

Guidance on integrating Auth0 into your Laravel application can be found here:

- [Auth0 Laravel SDK Readme](https://github.com/auth0/laravel-auth0/blob/master/README.md)
- [Auth0 Laravel SDK Session Authentication Quickstart](https://auth0.com/docs/quickstart/webapp/laravel)
- [Auth0 Laravel SDK Token Authorization Quickstart](https://auth0.com/docs/quickstart/backend/laravel)

You may also find the following documentation from the SDK's GitHub repository useful:

- [docs/Configuration](https://github.com/auth0/laravel-auth0/blob/master/docs/Configuration.md)
- [docs/Events](https://github.com/auth0/laravel-auth0/blob/master/docs/Events.md)
- [docs/Installation](https://github.com/auth0/laravel-auth0/blob/master/docs/Installation.md)
- [docs/Management](https://github.com/auth0/laravel-auth0/blob/master/docs/Management.md)
- [docs/Users](https://github.com/auth0/laravel-auth0/blob/master/docs/Users.md)

## Getting Started

Use Composer to install the dependencies and prepare env variables:

```bash
composer install
cp .env.dev .env
```

Run the application:

```
php artisan serve --host localhost --port 8000
```

Open in browser http://localhost:8000

## User Info Related
### Middleware 
Middleware contains the function to get userinfo and verify JWT. Defined on [Userinfo.php](app/Http/Middleware/Userinfo.php)

### APIs
API that defined a /api/userinfo for GET and POST is on [api.php](/routes/api.php)
- Route::get('/userinfo'...
- Route::post('/userinfo'...

### Call userinfo API service
Contain on [Utils.php](app/Http/Helpers/Utils.php)
- `getUserInfoUrl` function is to get userinfo URL from .well-known/openid-configuration
- `getUserInfo` is to get userinfo from userinfo API that related with mysql database


## Demonstration Routes

This sample includes a few demonstration routes to help you get started.

## Feedback

We appreciate your feedback! Please create an issue in this repository or reach out to us on [Community](https://community.auth0.com/).

## Vulnerability Reporting

Please do not report security vulnerabilities on the public GitHub issue tracker. The [Responsible Disclosure Program](https://auth0.com/whitehat) details the procedure for disclosing security issues.

## What is Auth0?

Auth0 helps you to easily:

- implement authentication with multiple identity providers, including social (e.g., Google, Facebook, Microsoft, LinkedIn, GitHub, Twitter, etc), or enterprise (e.g., Windows Azure AD, Google Apps, Active Directory, ADFS, SAML, etc.)
- log in users with username/password databases, passwordless, or multi-factor authentication
- link multiple user accounts together
- generate signed JSON Web Tokens to authorize your API calls and flow the user identity securely
- access demographics and analytics detailing how, when, and where users are logging in
- enrich user profiles from other data sources using customizable JavaScript rules

[Why Auth0?](https://auth0.com/why-auth0)

## License

This project is licensed under the MIT license. See the [LICENSE](./LICENSE) file for more info.
