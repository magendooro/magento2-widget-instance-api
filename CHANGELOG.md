# Changelog

All notable changes to this module are documented in this file. The format follows
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [1.0.0] - 2026-10-22

### Added

- REST API for widget instances, all routes guarded by `Magento_Widget::widget_instance`:
  `GET /V1/widgetInstance/search`, `GET /V1/widgetInstance/themes`, `GET /V1/widgetInstance/:instanceId`,
  `POST /V1/widgetInstance` and `PUT /V1/widgetInstance/:instanceId`.
- `widget_parameters` as a free-form `UnstructuredArray`; an empty set is returned as `{}`.
- Input validation in `save()` (`WidgetInstanceValidator`): `instance_type` must be declared in a `widget.xml`,
  `page_group` must be a group Magento's Instance model handles, `page_for` must be `all` or `specific`, `entities`
  digits and commas, `layout_handle` `[a-z0-9_]+`, `block_reference` `[A-Za-z0-9_.-]+`. A rejected request throws
  `CouldNotSaveException`, which the API returns as HTTP 400, with a message naming the field.
- PHPUnit 12 unit tests for the repository, the validator, the theme list and the data objects.
- README with installation, routes, payloads, ACL, validation rules and the `UnstructuredArray` parameters type.

### Changed

- Validation is stricter than in the pre-release API: a widget type from a disabled module, or any `page_group`,
  `page_for`, `entities`, `layout_handle` or `block_reference` outside the rules above, is now rejected where it was
  previously passed to the Instance model. Valid payloads save with identical results.
- Widget parameters are normalised with `Magento\Framework\Serialize\Serializer\Json`.
- DocBlocks added to the service contracts, data objects and models so that `phpcs --standard=Magento2`, `phpstan`
  level 2 and `phpmd` pass.
- `composer.json` requires `magento/module-webapi` and `magento/module-catalog`, and `etc/module.xml` sequences
  `Magento_Catalog` for the product-type page groups.
- `WidgetInstanceRepository` carries `@SuppressWarnings(PHPMD.CouplingBetweenObjects)`, as core repositories such as
  `Magento\Cms\Model\BlockRepository` do: it injects the factories, resource, collection and validator it needs.
