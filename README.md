# Gevento Products & Multi-Currency RESTful API (Laravel)

API RESTful desarrollada en **Laravel** para la gestión de productos, costos de impuestos/fabricación y registro de precios en múltiples divisas, aplicando buenas prácticas de Ingeniería de Software Backend.

---

## Cómo levantarlo en otra PC

```bash
docker compose up -d --build
```

Una vez iniciado:

- **API**: http://localhost:8080/products
- **Swagger UI**: http://localhost:8080/swagger/index.html
- **Correr los 36 tests en el contenedor**:

```bash
docker compose exec app php artisan test
```

---

## Ejecución en Devilbox (`http://gevento.loc`)

- **API**: http://gevento.loc/products (o http://gevento.loc/api/products)
- **Swagger UI**: http://gevento.loc/swagger/index.html

---

## Documentación Entregada (`public_html/swagger/`)

- **Swagger UI Interactivo**: [`public_html/swagger/index.html`](./public_html/swagger/index.html)
- **OpenAPI 3.0 (JSON / YAML)**: [`public_html/swagger/openapi.json`](./public_html/swagger/openapi.json) / [`public_html/swagger/openapi.yaml`](./public_html/swagger/openapi.yaml)
- **JSON Postman**: [`public_html/swagger/postman_collection.json`](./public_html/swagger/postman_collection.json)
- **JSON Insomnia**: [`public_html/swagger/insomnia_collection.json`](./public_html/swagger/insomnia_collection.json)
- **Documento Técnico**: [`public_html/swagger/DOCUMENTACION_API.md`](./public_html/swagger/DOCUMENTACION_API.md)

