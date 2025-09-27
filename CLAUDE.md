# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

JBZoo Assets is a PHP library for managing asset files (JavaScript, CSS, LESS) with dependency resolution, compression, and merging capabilities. The library provides a factory pattern for creating different asset types and a manager for handling collections and build processes.

## Development Commands

### Testing
- `make test` - Run PHPUnit tests
- `phpunit` - Run tests directly with PHPUnit
- `phpunit tests/SpecificTest.php` - Run a single test file

### Code Quality
- `make codestyle` - Run all code style checks and fixes
- `make test-all` - Run both tests and code style checks

### Dependencies
- `make update` - Install/update all dependencies using Composer
- `composer update` - Update dependencies directly

## Architecture

### Core Components

**Manager (`src/Manager.php`)** - Central orchestrator that:
- Manages asset registration and queueing
- Handles dependency resolution with circular dependency detection
- Coordinates the build process across different asset types
- Maintains configuration parameters and path resolution

**Factory (`src/Factory.php`)** - Creates asset instances with automatic type detection:
- Maps file extensions to appropriate asset classes (.js → JsFile, .css → CssFile, .less → LessFile, .jsx → JsxFile)
- Supports custom asset type registration
- Handles both file-based and code-based assets

**AbstractAsset (`src/Asset/AbstractAsset.php`)** - Base class defining the asset interface:
- Defines asset type constants (js, css, less, jsx, callback, collection)
- Manages dependencies, options, and metadata
- Requires concrete implementations to define `load()` method

### Asset Types

The library supports multiple asset types in `src/Asset/`:
- **File-based**: `JsFile`, `CssFile`, `LessFile`, `JsxFile` - Handle external files
- **Code-based**: `JsCode`, `CssCode`, `JsxCode` - Handle inline code
- **Special**: `Callback` - Execute custom logic, `Collection` - Group multiple assets

### Dependency Resolution

The Manager implements topological sorting to resolve asset dependencies:
- Detects circular dependencies and throws exceptions
- Ensures dependencies are loaded before dependents
- Maintains resolved/unresolved tracking during traversal

### Build Process

Assets are processed through a standardized build pipeline:
1. Queue assets for inclusion
2. Resolve all dependencies
3. Load asset sources through `load()` method
4. Aggregate by type (js, css, etc.)
5. Return organized arrays ready for output

## Configuration

The library uses JBZoo's Data component for configuration with these key parameters:
- `debug` - Enable debug mode
- `strict_mode` - Enable strict validation
- `less` - LESS compiler options

Path resolution is handled through JBZoo's Path component for consistent file system operations.