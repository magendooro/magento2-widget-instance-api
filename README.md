# Magendoo_WidgetInstanceApi

REST API for Magento widget instances (Content > Widgets): search, read, create and update under `/V1/widgetInstance`.

Saves go through Magento's own `Magento\Widget\Model\Widget\Instance` model, so an instance saved through the API gets
the same page rows and layout updates as one saved in the admin form. Widget instances cannot be deleted through this API.

## Installation

Requires Magento Open Source 2.4 and PHP 8.2 to 8.4. The module depends on `Magento_Widget`, `Magento_Theme`,
`Magento_Webapi` and `Magento_Catalog`.

```bash
composer require magendoo/module-widget-instance-api
bin/magento module:enable Magendoo_WidgetInstanceApi
bin/magento setup:upgrade
bin/magento cache:flush
```

To install from a checkout instead, place or symlink the module in `app/code/Magendoo/WidgetInstanceApi` and run the
last three commands. The module has no database schema of its own.

## ACL

All five routes require the ACL resource `Magento_Widget::widget_instance` (the widget instance permission in the admin
role editor). Authenticate with an admin token:

```bash
TOKEN=$(curl -s -X POST "$BASE/rest/V1/integration/admin/token" \
  -H 'Content-Type: application/json' \
  -d '{"username":"admin","password":"..."}' | tr -d '"')
```

## Routes

| Method | URL                              | Service method                             |
|--------|----------------------------------|--------------------------------------------|
| GET    | `/V1/widgetInstance/search`      | `WidgetInstanceRepositoryInterface::getList` |
| GET    | `/V1/widgetInstance/themes`      | `ThemeListInterface::getThemes`            |
| GET    | `/V1/widgetInstance/:instanceId` | `WidgetInstanceRepositoryInterface::getById` |
| POST   | `/V1/widgetInstance`             | `WidgetInstanceRepositoryInterface::save`  |
| PUT    | `/V1/widgetInstance/:instanceId` | `WidgetInstanceRepositoryInterface::save`  |

### GET /V1/widgetInstance/search

Takes the standard `searchCriteria` query parameters.

```bash
curl -H "Authorization: Bearer $TOKEN" \
  "$BASE/rest/V1/widgetInstance/search?searchCriteria[pageSize]=50&searchCriteria[currentPage]=1"
```

```json
{
  "items": [
    {
      "instance_id": 5,
      "instance_type": "Magento\\Cms\\Block\\Widget\\Block",
      "theme_id": 3,
      "title": "Home banner",
      "store_ids": [0],
      "sort_order": 0,
      "widget_parameters": {"block_id": "home-banner"},
      "page_groups": [
        {
          "page_group": "all_pages",
          "layout_handle": "default",
          "block_reference": "content",
          "page_for": "all",
          "entities": "",
          "page_template": "widget/static_block/default.phtml"
        }
      ]
    }
  ],
  "search_criteria": {"filter_groups": [], "page_size": 50, "current_page": 1},
  "total_count": 1
}
```

### GET /V1/widgetInstance/themes

Lists the frontend themes. `code` is the theme path, which is stable across installations where `theme_id` is not.

```json
[
  {"theme_id": 2, "code": "Magento/blank"},
  {"theme_id": 3, "code": "Magento/luma"}
]
```

### GET /V1/widgetInstance/:instanceId

Returns one widget instance in the shape of an `items` entry above. An unknown ID returns HTTP 404.

### POST /V1/widgetInstance

Creates an instance. The body is wrapped in `widget`. Omit `instance_id`.

```json
{
  "widget": {
    "instance_type": "Magento\\Cms\\Block\\Widget\\Block",
    "theme_id": 3,
    "title": "Home banner",
    "store_ids": [0],
    "sort_order": 0,
    "widget_parameters": {"block_id": "home-banner"},
    "page_groups": [
      {
        "page_group": "all_pages",
        "layout_handle": "default",
        "block_reference": "content",
        "page_for": "all",
        "entities": "",
        "page_template": "widget/static_block/default.phtml"
      }
    ]
  }
}
```

The response is the saved instance, including its new `instance_id`, in the shape of an `items` entry above.

### PUT /V1/widgetInstance/:instanceId

Updates an instance. The body is the same as for POST, wrapped in `widget`; the `instanceId` of the URL overrides
`instance_id` in the body. The page groups sent replace every existing page row of the instance. An unknown ID returns
HTTP 400 (`Could not save the widget instance: the widget instance with the "99" ID doesn't exist.`).

### Errors

A failed save returns HTTP 400 with a message that starts with `Could not save the widget instance:`. A message about
a rejected value names the field, for example:

```json
{"message": "Could not save the widget instance: page_group \"bogus\" is not valid. Use one of: all_pages, pages, ..."}
```

## Validation

`save()` checks its input before the Instance model sees it. A rejected request persists nothing.

| Field             | Rule                                                                                                      |
|-------------------|-----------------------------------------------------------------------------------------------------------|
| `instance_type`   | Required. Must be a widget type declared in the `widget.xml` of an enabled module.                        |
| `page_group`      | Required, per page group. One of `all_pages`, `pages`, `page_layouts`, `anchor_categories`, `notanchor_categories`, `all_products`, or `<type>_products` for each installed product type (for example `simple_products`). |
| `page_for`        | `all` or `specific`. Empty means `all`.                                                                   |
| `entities`        | Digits and commas only, for example `1,2,3`.                                                              |
| `layout_handle`   | Lowercase letters, digits and underscores: `[a-z0-9_]+`.                                                  |
| `block_reference` | Letters, digits, underscores, dots and hyphens: `[A-Za-z0-9_.-]+`.                                        |

Empty `entities`, `layout_handle` and `block_reference` are allowed, because which of them a page group needs depends on
the page group. The contents of `widget_parameters`, and the values of `theme_id`, `store_ids` and `page_template`, are
not validated by this module.

## widget_parameters (UnstructuredArray)

`widget_parameters` is a free-form object whose keys depend on the widget type, for example `block_id` for
`Magento\Cms\Block\Widget\Block`. In the service contract it is declared as the Web API pass-through type
`UnstructuredArray`, so the keys survive serialisation in both directions. An instance without parameters returns
`"widget_parameters": {}`, always an object and never a list.

## Consumer

This API is the target of magento2-cms-sync, which reads
`widgetInstance/search`, `widgetInstance/themes` and `widgetInstance/{id}` on a source store and writes with POST and
PUT, bodies wrapped in `widget`, on a target store. Because validation is strict, a widget instance copied from a store
whose widget type is not declared on the target (for example a disabled module) is rejected with a message naming
`instance_type`.

## Tests

The unit tests live in `Test/Unit` and run with the PHPUnit of a Magento 2.4 installation that has the module installed:

```bash
vendor/bin/phpunit -c dev/tests/unit/phpunit.xml.dist app/code/Magendoo/WidgetInstanceApi/Test/Unit
```

## License

OSL-3.0
