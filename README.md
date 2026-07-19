# JBZoo / Assets

[![CI](https://github.com/JBZoo/Assets/actions/workflows/main.yml/badge.svg?branch=master)](https://github.com/JBZoo/Assets/actions/workflows/main.yml?query=branch%3Amaster)
[![Coverage Status](https://coveralls.io/repos/github/JBZoo/Assets/badge.svg?branch=master)](https://coveralls.io/github/JBZoo/Assets?branch=master)
[![Psalm Coverage](https://shepherd.dev/github/JBZoo/Assets/coverage.svg)](https://shepherd.dev/github/JBZoo/Assets)
[![Psalm Level](https://shepherd.dev/github/JBZoo/Assets/level.svg)](https://shepherd.dev/github/JBZoo/Assets)
[![CodeFactor](https://www.codefactor.io/repository/github/jbzoo/assets/badge)](https://www.codefactor.io/repository/github/jbzoo/assets/issues)

[![Stable Version](https://poser.pugx.org/jbzoo/assets/version)](https://packagist.org/packages/jbzoo/assets/)
[![Total Downloads](https://poser.pugx.org/jbzoo/assets/downloads)](https://packagist.org/packages/jbzoo/assets/stats)
[![Dependents](https://poser.pugx.org/jbzoo/assets/dependents)](https://packagist.org/packages/jbzoo/assets/dependents?order_by=downloads)
[![GitHub License](https://img.shields.io/github/license/jbzoo/assets)](https://github.com/JBZoo/Assets/blob/master/LICENSE)

A PHP library for managing asset files (JavaScript, CSS, LESS, JSX) with dependency resolution, compression, and merging capabilities. The library provides automatic type detection, dependency management, and a flexible build system for modern web applications.

## Installation

```bash
composer require jbzoo/assets
```

## Quick Start

```php
use JBZoo\Assets\Manager;
use JBZoo\Path\Path;

// Initialize path resolver and asset manager
$path = new Path();
$path->set('assets', '/path/to/your/assets');

$manager = new Manager($path);

// Register and queue assets
$manager
    ->register('jquery', 'assets/js/jquery.js')
    ->register('bootstrap-css', 'assets/css/bootstrap.css')
    ->register('app', 'assets/js/app.js', ['jquery']) // depends on jquery
    ->add('bootstrap-css')
    ->add('app');

// Build assets with dependency resolution
$assets = $manager->build();

// Output: Array with organized asset types
// $assets['js'] contains JavaScript files
// $assets['css'] contains CSS files
```

## Supported Asset Types

### File-based Assets

The library automatically detects asset types by file extension:

```php
// JavaScript files (.js)
$manager->register('script', 'assets/js/script.js');

// CSS files (.css)
$manager->register('styles', 'assets/css/styles.css');

// LESS files (.less)
$manager->register('theme', 'assets/less/theme.less');

// JSX files (.jsx)
$manager->register('component', 'assets/jsx/component.jsx');
```

### Code-based Assets

Include inline code directly:

```php
use JBZoo\Assets\Asset\AbstractAsset;

// Inline CSS
$manager->register('inline-css', 'div { color: red; }', [], [
    'type' => AbstractAsset::TYPE_CSS_CODE
]);

// Inline JavaScript
$manager->register('inline-js', 'console.log("Hello World");', [], [
    'type' => AbstractAsset::TYPE_JS_CODE
]);

// Code with HTML tags (automatically stripped)
$manager->register('styled', '<style>body { margin: 0; }</style>', [], [
    'type' => AbstractAsset::TYPE_CSS_CODE
]);
```

### External Assets

```php
// CDN resources
$manager->register('jquery-cdn', 'https://code.jquery.com/jquery-3.6.0.min.js');

// External stylesheets
$manager->register('bootstrap-cdn', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
```

### Callback Assets

Execute custom logic during the build process:

```php
// Simple callback
$manager->register('analytics', function() {
    return 'ga("send", "pageview");';
});

// Multiple callbacks in collection
$manager->add('init-scripts', [
    function() { return 'console.log("Script 1");'; },
    function() { return 'console.log("Script 2");'; },
    function() { return 'console.log("Done");'; }
]);
```

## Dependency Management

### Basic Dependencies

```php
// jQuery must load before Bootstrap JavaScript
$manager
    ->register('jquery', 'assets/js/jquery.js')
    ->register('bootstrap-js', 'assets/js/bootstrap.js', ['jquery'])
    ->add('bootstrap-js'); // jQuery will be included automatically
```

### Complex Dependency Trees

```php
$manager
    ->register('utilities', 'assets/js/utils.js')
    ->register('jquery', 'assets/js/jquery.js')
    ->register('plugin', 'assets/js/plugin.js', ['jquery', 'utilities'])
    ->register('app', 'assets/js/app.js', ['plugin'])
    ->add('app');

// Build order: utilities, jquery, plugin, app
```

### Circular Dependency Detection

```php
try {
    $manager
        ->register('a', 'a.js', ['b'])
        ->register('b', 'b.js', ['a']) // Circular dependency
        ->add('a');

    $manager->build(); // Throws exception
} catch (\JBZoo\Assets\Exception $e) {
    echo "Circular dependency detected: " . $e->getMessage();
}
```

## Path Resolution

### Virtual Paths

```php
$path = new Path();
$path->set('assets', '/var/www/assets');
$path->set('vendor', '/var/www/vendor');

$manager = new Manager($path);

// Use virtual paths
$manager->register('app', 'assets:js/app.js');        // Resolves to /var/www/assets/js/app.js
$manager->register('lib', 'vendor:lib/script.js');    // Resolves to /var/www/vendor/lib/script.js
```

### Relative and Absolute Paths

```php
// Relative to current working directory
$manager->register('local', 'js/local.js');

// Absolute path
$manager->register('system', '/usr/share/js/system.js');
```

## Configuration Options

### Debug Mode

```php
$manager = new Manager($path, ['debug' => true]);

// Or set later
$manager->setParam('debug', true);
```

### Strict Mode

```php
$manager = new Manager($path, ['strict_mode' => true]);

// Throws exceptions for missing files
$manager->register('missing', 'assets/js/missing.js');
$manager->add('missing');
$manager->build(); // Exception thrown
```

### LESS Compilation Options

```php
$manager = new Manager($path, [
    'less' => [
        'compress' => true,
        'sourceMap' => false
    ]
]);
```

## Building and Output

### Basic Build

```php
$assets = $manager->build();

// Result structure:
[
    'js'         => ['path/to/script1.js', 'path/to/script2.js'],
    'js_code'    => ['console.log("inline");'],
    'jsx'        => ['path/to/component.jsx'],
    'jsx_code'   => ['ReactDOM.render(...);'],
    'css'        => ['path/to/styles.css'],
    'css_code'   => ['body { margin: 0; }'],
    'callback'   => [42, 'result', ...]  // Callback return values
]
```

### Processing Build Results

```php
$assets = $manager->build();

// Generate HTML tags
foreach ($assets['css'] as $cssFile) {
    echo '<link rel="stylesheet" href="' . $cssFile . '">' . PHP_EOL;
}

foreach ($assets['js'] as $jsFile) {
    echo '<script src="' . $jsFile . '"></script>' . PHP_EOL;
}

// Include inline styles
if (!empty($assets['css_code'])) {
    echo '<style>' . implode("\n", $assets['css_code']) . '</style>';
}

// Include inline scripts
if (!empty($assets['js_code'])) {
    echo '<script>' . implode("\n", $assets['js_code']) . '</script>';
}
```

## Asset Collections

### Grouping Related Assets

```php
// Register a collection of related assets
$manager->add('ui-components', [
    'assets/css/buttons.css',
    'assets/css/modals.css',
    'assets/js/components.js'
]);
```

### Managing Asset Queues

```php
// Add assets to queue
$manager
    ->add('jquery')
    ->add('bootstrap')
    ->add('app');

// Remove from queue (but keep registered)
$manager->remove('bootstrap');

// Unregister completely
$manager->unregister('app');
```

## Advanced Usage

### Custom Asset Types

```php
// Register custom asset type
$manager->getFactory()->registerType('custom', CustomAssetClass::class);

// Use custom type
$manager->register('special', 'data', [], ['type' => 'custom']);
```

### Conditional Asset Loading

```php
$isProduction = $_ENV['APP_ENV'] === 'production';

if ($isProduction) {
    $manager->register('analytics', 'assets/js/analytics.min.js');
} else {
    $manager->register('debug-tools', 'assets/js/debug.js');
}
```

### Asset Preprocessing

```php
$manager->register('processed', function() use ($dataProcessor) {
    $data = $dataProcessor->compile();
    return "window.appData = " . json_encode($data) . ";";
});
```

## Error Handling

```php
try {
    $manager
        ->register('app', 'missing-file.js')
        ->add('app');

    $assets = $manager->build();
} catch (\JBZoo\Assets\Exception $e) {
    // Handle missing files, circular dependencies, etc.
    error_log("Asset error: " . $e->getMessage());
}
```

## Requirements

- PHP 8.2 or higher
- JBZoo Utils package
- JBZoo Path package
- JBZoo Data package
- JBZoo Less package (for LESS file support)

## Testing

```bash
# Run all tests
make test
make codestyle
```

## License

MIT
