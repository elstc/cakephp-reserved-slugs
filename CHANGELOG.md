# Changelog

## 5.1.0 (2026-03-30)

### Added

- Add `SlugExistenceInterface` to decouple `IsNotReservedSlug` from the concrete `ReservedSlugsTable`. Custom tables implementing this interface can now be used as alternative backends for reserved-slug lookups.

### Changed

- `IsNotReservedSlug` now throws `InvalidArgumentException` (previously `RuntimeException`) when the configured table does not implement `SlugExistenceInterface`.

## 5.0.0 (2026-03-30)

- Initial release.