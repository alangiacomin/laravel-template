# Development

Operational guidelines for daily development on this template.

## Start the local environment

To run the full development stack in parallel:

```bash
composer run dev
```

This command starts:

- Laravel server (`php artisan serve`)
- Reverb websocket (`php artisan reverb:start --port=9000`)
- queue listener (`php artisan queue:listen`)
- Vite dev server (`bun run dev`)

## Frontend

Commands available in `package.json`:

```bash
bun run dev
bun run dev:debug
bun run build
```

Alternatively, you can use NPM:

```bash
npm run dev
npm run build
```

## Testing

Run tests:

```bash
composer test
```

Coverage:

```bash
composer test-coverage
```

## Code quality

PHP formatting:

```bash
./vendor/bin/pint
```

For the frontend, ESLint configuration is already available (`eslint.config.js`): you can add dedicated scripts (`lint`,
`lint:fix`) if you want to standardize NPM/Bun commands as well.

## Recommended conventions

- Keep domain logic separated inside `app/`.
- Keep frontend/types code in `resources/` with consistent naming.
- Add tests in `tests/` for critical areas or non-trivial logic.
