# Arco starter (Laravel)

A Laravel 13 app with Inertia, React and TypeScript, ready to deploy to AWS Lambda with [Arco](https://github.com/multi-api/arco). It is the template behind "Start from a template" in Arco Cloud.

It needs no database. The home page is a dashboard with one card per Arco Cloud feature, so you can add resources in Arco Cloud one at a time and watch each card come alive. Login stays off until the app has a managed database.

## What is inside

- Laravel 13 from the official React starter kit (Inertia, React, TypeScript, Tailwind).
- `laravel/octane`, `multiapi/arco-laravel` and `multiapi/arco` for the Lambda runtime and deploys.
- `multiapi/laravel-aurora-dsql`, so a managed Aurora DSQL database works as soon as you add one.
- `arco.php`, the Arco config: PHP 8.5, a console function, `starter:migrate` as a deploy command, and a `staging` environment.

## Run it locally

```bash
composer setup
composer dev
```

`composer setup` installs dependencies, creates `.env`, generates the app key, creates a SQLite database and runs the migrations, then builds the front end. `composer dev` starts the server, a queue listener, the log tail and Vite.

Locally `.env` sets `DB_CONNECTION=sqlite`, so the app counts as having a database and the register and login pages work. Queue, storage and cache cards show "not set up" until Arco provides those resources, because locally the app uses the `sync` queue, local disk and array cache.

Run the tests with `php artisan test`.

## Deploy with Arco Cloud

Create an app from this template in Arco Cloud. Cloud makes a copy of this repository in your GitHub account and rewrites the app name and the region in `arco.php`. It does not put queues, storage, cache or variables in `arco.php`: Cloud supplies them through its own config layer when you add them in Resources.

- The `staging` environment follows the `staging` branch, which is the default branch of your copy. Pushing to it redeploys.
- `starter:migrate` runs on every deploy. It skips migrations until a database exists, so the first deploy works without one.
- To turn on login, add a managed database in Arco Cloud and choose **Save and deploy**. The next deploy sets `DB_CONNECTION`, runs the migrations, and the register and login pages appear.

You can also deploy this app with the Arco CLI alone, without Arco Cloud. See the [multiapi/arco README](https://github.com/multi-api/arco#readme).

## Dashboard cards and Cloud features

| Card | What it does | Arco Cloud feature |
| --- | --- | --- |
| Queue | Dispatches a job; its result shows up on the card | Resources → Queue |
| Storage | Uploads a small file and lists the files on the shared disk | Resources → Storage |
| Cache | A counter that increments in the shared cache | Resources → Cache |
| Logs | Writes a log line or throws a test exception | Logs |
| Database / login | Explains login, and links to register and sign in once a database exists | Resources → Database |
| Environment | Shows `APP_ENV`, the Arco environment, the PHP version and the region | Environment |

A card for a resource you have not added says so and points to Arco Cloud → Resources.

## CI

`.github/workflows/tests.yml` builds the front end and runs `php artisan test` with PHP 8.5 on every pull request, or by hand (*Run workflow* in the Actions tab). It does not run on push, so a repository generated from this template spends no Actions minutes on the commits Arco Cloud makes while setting it up. `lint.yml` runs the code style checks.
