# Quick Create Pages
WordPress plugin for quickly adding a hierarchy of posts or pages when creating a new site.

## Developer documentation

Watch for changes - `npx mix watch`

Create production build - `npx mix --production`

## Hooks
```php
apply_filters( 'qcp/insert_post_args', array $args )
```

The arguments used for the `wp_insert_post()` function when creating new posts.

```php
apply_filters( 'qcp/js_config', array $args )
```

Arguments passed to the JavaScript app file, including available post types and localised strings.

```php
apply_filters( 'qcp/post_types', array $args )
```

Array of post types available to add using the plugin.
