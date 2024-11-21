### Added
- `"prefer-stable": true` into `composer.json`.

### Updated
- Compatibility with new route url completition and new view  
  rendering in MvcCore v5.3.
- Internal view rendering methods.

### Removed
- Path getters and setters, which have been moved into Application object.
- Form output type dependency to view DOCTYPE.  
  Output type depends now on `Content-Type` header only.
